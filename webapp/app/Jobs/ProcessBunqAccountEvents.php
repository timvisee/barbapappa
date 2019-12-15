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
use bunq\Model\Generated\Object\Pointer;
use bunq\Util\BunqEnumApiEnvironmentType;

/**
 * Process all events on the given bunq account, that have not yet been
 * processed.
 */
class ProcessBunqAccountEvents implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'normal';

    /**
     * The number of seconds to wait before retrying the job.
     * The bunq API has a 30-second cooldown when throttling.
     *
     * @var int
     */
    public $retryAfter = 32;

    /**
     * The maximum number of unhandled events to query from bunq at once.
     *
     * This job will automatically be repeated until all events are handled.
     *
     * @var int
     */
    const EVENT_COUNT = 200;

    /**
     * Number of seconds between events being handled.
     *
     * @var int
     */
    const EVENT_INTERVAL = 3;

    /**
     * The number of seconds to wait for retrying this job once, when no new
     * events were found the first time and `$retryIfNone` is `true`.
     *
     * @var int
     */
    const EMPTY_RETRY_AFTER = 15;

    /**
     * The ID of a bunq account model to handle new events for.
     *
     * @var int
     */
    private $accountId;

    /**
     * Retry this job once after a few seconds, if no new bunq events were found.
     * This specifies how many more times to retry.
     *
     * @var bool
     */
    private $retryCountIfNone;

    /**
     * Create a new job instance.
     *
     * @param BunqAccount $account The account to handle new events for.
     * @param bool [$retryCountIfNone=0] Retry this job for this many times after
     *      a few seconds, if no new events were found when running it the first time.
     *
     * @return void
     */
    public function __construct(BunqAccount $account, int $retryCountIfNone = 0) {
        // Set queue
        $this->onQueue(Self::QUEUE);

        $this->accountId = $account->id;
        $this->retryCountIfNone = $retryCountIfNone;
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
        $account = BunqAccount::find($this->accountId);
        if($account == null)
            return;
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
        $events = $events
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

                // Only handle BunqMe Tab event updates
                if(!is_null($o->getBunqMeTab()))
                    return $event->getAction() == 'UPDATE';

                return false;
            })
            ->values();

        // Spawn a job for each event
        $events->each(function($event, $i) use($account) {
            // Obtain the object
            $o = $event->getObject();

            // Determine delay for this job based on it's index
            $delay = now()->addSeconds($i * Self::EVENT_INTERVAL);

            // Spawn a job corresponding to the event type
            if(!is_null($payment = $o->getPayment()))
                $job = ProcessBunqPaymentEvent::dispatch($account, $payment)
                    ->delay($delay);
            else if(!is_null($tab = $o->getBunqMeTab()))
                $job = ProcessBunqBunqMeTabEvent::dispatch($account, $tab)
                    ->delay($delay);
            else
                throw new \Exception('Attempting to handle bunq event with unhandled type');
        });

        // If no events were found, reschedule job after a few seconds
        if($this->retryCountIfNone > 0 && $events->isEmpty()) {
            Self::dispatch($account, $this->retryCountIfNone - 1)
                ->delay(now()->addSeconds(Self::EMPTY_RETRY_AFTER));
            return;
        }

        // Walk through new events again if not completed, to handle the rest
        if(!$completed)
            // Dispatch this event handling job again, delayed after event jobs
            Self::dispatch($account, false)
                ->delay(
                    now()->addSeconds($events->count() * Self::EVENT_INTERVAL)
                );
    }
}
