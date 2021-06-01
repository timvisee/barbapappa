<?php

namespace App\Jobs;

use App\Jobs\SendBunqPayment;
use App\Models\BunqAccount;
use BarPay\Models\Payment;
use BarPay\Models\PaymentBunqMeTab;
use BarPay\Models\ServiceBunqMeTab;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use bunq\Model\Generated\Endpoint\BunqMeTab;
use bunq\Model\Generated\Object\Amount;
use bunq\Model\Generated\Object\Pointer;

class ProcessBunqBunqMeTabEvent implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'high';

    /**
     * Number of seconds to delay payment forwards.
     * This small delay is used to minimize API rate limiting.
     *
     * @var int
     */
    const FORWARD_DELAY = 6;

    /**
     * The bunq account ID.
     */
    private $accountId;

    /**
     * The bunq me tab ID to process.
     */
    private $tabId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(BunqAccount $account, BunqMeTab $tabResult) {
        // Set queue
        $this->onQueue(Self::QUEUE);

        $this->accountId = $account->id;
        $this->tabId = $tabResult->getId();
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware() {
        return [new RateLimited('bunq-api')];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Find the bunq account, load the bunq API context
        $account = BunqAccount::findOrFail($this->accountId);
        $account->loadBunqContext();

        // Fetch the bunqme tab payment, and gather some facts
        $bunqMeTab = BunqMeTab::get($this->tabId, $account->monetary_account_id)
            ->getValue();

        // Attempt to handle this as bunq IBAN transaction
        if(Self::handleBunqMeTabEvent($account, $bunqMeTab))
            return;

        // TODO: send a message to admin instead, should not reach this
        \Log::error(new \Exception('Unhandled BunqMe Tab payment, should refund?'));
    }

    /**
     * Handle a payment event for an automated BunqMe Tab payment.
     *
     * True is returned if the payment was successfully handled, false
     * otherwise.
     *
     * @param BunqAccount $account The bunq account we received on.
     * @param BunqMeTabResultResponse $tabResponse The received bunq API payment model.
     * @return bool True if handled, false if not.
     */
    private static function handleBunqMeTabEvent(BunqAccount $account, BunqMeTab $bunqMeTab) {
        // Find the bar payment related to this request
        $barPaymentable = PaymentBunqMeTab::where('bunq_tab_id', $bunqMeTab->getId())
            ->first();
        if($barPaymentable == null)
            return;
        $barPayment = $barPaymentable->payment;
        if(!$barPayment->isInProgress())
            return;

        // Gather facts
        $amount = $bunqMeTab->getBunqmeTabEntry()->getAmountInquired();
        $service = $barPayment->service;
        $serviceable = $service->serviceable;

        // Were done if not inquired
        $inquiries = $bunqMeTab->getResultInquiries();
        if(count($inquiries) <= 0)
            return true;

        // TODO: do amount check, once payment data from bunq API isn't null anymore

        // Settle this payment
        DB::transaction(function() use($account, $amount, $barPayment, $serviceable, $barPaymentable) {
            // Forward the money
            Self::forwardPayment($account, $amount, $barPayment, $serviceable);

            // Set settled time
            $barPaymentable->settled_at = now();
            $barPaymentable->save();

            // Settle the payment
            $barPayment->settle(Payment::STATE_COMPLETED);

            // TODO: on settlement: cancel bunqme tab link
        });

        // We handled the payment
        return true;
    }

    /**
     * Forward the payment to the configured community IBAN.
     *
     * @param BunqAccount $account The bunq account.
     * @param Amount $amount The amount to send.
     * @param Payment $barPayment The bar payment model we're forwarding for.
     * @param ServiceBunqMeTab $serviceable The bunqme tab serviceable, holding
     *      the account to forward to.
     */
    private static function forwardPayment(BunqAccount $account, Amount $amount, Payment $barPayment, ServiceBunqMeTab $serviceable) {
        // Build a pointer to send the money to
        $to = new Pointer(
            'IBAN',
            $serviceable->iban,
            $serviceable->account_holder
        );

        // Forward the payment
        SendBunqPayment::dispatch(
            $account,
            $to,
            $amount,
            config('app.name')
                . ' '
                . __('barpay::service.bunq.paid', [], config('app.locale'))
                . ': '
                . $barPayment->getReference()
        )->delay(now()->addSeconds(Self::FORWARD_DELAY));
    }

    /**
     * Backoff times in seconds.
     *
     * @return array
     */
    public function backoff() {
        // The bunq API has a 30-second cooldown when throttling, retry quickly
        // a few times because this has high priority for users, then backoff
        return [2, 3, 5, 10, 32, 60];
    }
}
