<?php

namespace App\Jobs;

use App\Models\BunqAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use bunq\Context\ApiContext;
use bunq\Context\BunqContext;
use bunq\Exception\ApiException;
use bunq\Exception\BadRequestException;
use bunq\Http\Pagination;
use bunq\Model\Generated\Endpoint\Event;
use bunq\Model\Generated\Endpoint\MonetaryAccountBank;
use bunq\Model\Generated\Object\NotificationFilter;
use bunq\Model\Generated\Object\Pointer;
use bunq\Util\BunqEnumApiEnvironmentType;

/**
 * Process all events on the given bunq account, that have not yet been
 * processed.
 */
class ProcessBunqAccountEvents implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The maximum number of unhandled events to query from bunq at once.
     *
     * This job will automatically be repeated until all events are handled.
     */
    const EVENT_COUNT = 200;

    /**
     * Number of seconds between events being handled.
     */
    const EVENT_INTERVAL = 2;

    /**
     * The ID of a bunq account model to handle new events for.
     *
     * @var int
     */
    private $accountId;

    /**
     * Create a new job instance.
     *
     * @param BunqAccount $account The account to handle new events for.
     *
     * @return void
     */
    public function __construct(BunqAccount $account) {
        $this->accountId = $account->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Handle the event in a database transaction
        $job = $this;
        DB::transaction(function() use($job) {
            $job->handleInTransaction();
        });
    }

    /**
     * Execute the job inside of a database transaction.
     *
     * @return void
     */
    private function handleInTransaction() {
        // Assert we're in a database transaction
        assert_transaction();

        // Gather facts, load bunq API context
        $account = BunqAccount::findOrFail($this->accountId);
        $account->loadBunqContext();

        // Configure pagination, start after last ID
        $pagination = new Pagination();
        $pagination->setCount(Self::EVENT_COUNT);
        $pagination->setNewerId($account->last_event_id ?? 0);

        // List account events, return if emtpy
        $events = Event::listing(array_merge(
                [
                    'monetary_account_id' => $account->monetary_account_id,
                    'status' => 'FINALIZED',
                ],
                $pagination->getUrlParamsNextPage()
            ), [])->getValue();
        $events = collect($events)->reverse();
        if($events->isEmpty())
            return;

        // Update last event ID we've processed, check whether we're done
        $account->last_event_id = $events->last()->getId();
        $account->save();
        $completed = $events->count() < $pagination->getCount();

        // Handle each not-yet-handled event
        $events
            // Only keep event types we should handle, then reindex
            ->filter(function($event) {
                // Get the object
                $o = $event->getObject();
                if(is_null($o))
                    return false;

                // Process payments if we received money in euros
                if(($payment = $o->getPayment()) != null) {
                    $amount = $payment->getAmount();
                    $amountValue = (float) $amount->getValue();
                    return $amountValue >= 0 && $amount->getCurrency() == 'EUR';
                }

                // Filter to only keep other event types we should handle
                return !is_null($o->getBunqMeTab());
            })
            ->values()

            // Spawn a job for each event
            ->each(function($event, $i) use($account) {
                // Obtain the object
                $o = $event->getObject();

                // Determine delay for this job based on it's index
                $delay = now()->addSeconds($i * Self::EVENT_INTERVAL);

                // Spawn a job corresponding to the event type
                if(!is_null($payment = $o->getPayment()))
                    $job = ProcessBunqPaymentEvent::dispatch($account, $payment)
                        ->delay($delay);
                else if(!is_null($tab = $o->getBunqMeTab()))
                    $job = ProcessBunqBunqMeTabEvent::dispatch()->delay($delay);
                else
                    throw new \Exception('Attempting to handle bunq event with unhandled type');
            });

        // Walk through new events again if not completed, to handle the rest
        if(!$completed) {
            // Dispatch this event handling job again, delayed after event jobs
            Self::dispatch($account)
                ->delay(
                    now()->addSeconds($events->count() * Self::EVENT_INTERVAL)
                );
        }
    }
}
