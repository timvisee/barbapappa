<?php

namespace App\Jobs;

use App\Jobs\SendBunqPayment;
use App\Models\BunqAccount;
use BarPay\Models\Payment;
use BarPay\Models\PaymentBunqIban;
use BarPay\Models\ServiceBunqIban;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use bunq\Model\Generated\Endpoint\Payment as ApiPayment;
use bunq\Model\Generated\Object\Pointer;

class ProcessBunqPaymentEvent implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'high';

    private $accountId;
    private $apiPaymentId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(BunqAccount $account, ApiPayment $apiPayment) {
        // Set queue
        $this->onQueue(Self::QUEUE);

        $this->accountId = $account->id;
        $this->apiPaymentId = $apiPayment->getId();
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

        // Fetch the payment, and gather some facts
        $apiPayment = ApiPayment::get($this->apiPaymentId, $account->monetary_account_id)
            ->getValue();
        $amount = $apiPayment->getAmount();
        $amountValue = (float) $amount->getValue();

        // Ignore negative amounts, or amounts not in euro
        if($amountValue <= 0 || $amount->getCurrency() != 'EUR')
            return;

        // Attempt to handle this as bunq IBAN transaction
        if(Self::handleBunqIban($account, $apiPayment))
            return;

        // This payment is unknown, send the money back
        Self::refundPayment($account, $apiPayment);
    }

    /**
     * Handle a payment event for an automated bunq IBAN transaction.
     *
     * If the given payment did not match an initiated bunq IBAN payment, false
     * is returned.
     * True is returned if the payment was successfully handled.
     *
     * @param BunqAccount $account The bunq account we received on.
     * @param ApiPayment $apiPayment The received bunq API payment model.
     * @return bool True if handled, false if not.
     */
    private static function handleBunqIban(BunqAccount $account, ApiPayment $apiPayment) {
        // Search for reference in payment
        $ref = Self::parsePaymentReference($apiPayment);
        if($ref == null)
            return false;

        // Find a matching bunq IBAN payment, must be one result
        $barPayments = Payment::inProgress()
            ->where('reference', $ref)
            ->where('paymentable_type', PaymentBunqIban::class)
            ->get();
        if($barPayments->count() != 1)
            return false;

        // Gather facts
        $barPayment = $barPayments->first();
        $barPaymentable = $barPayment->paymentable;
        $amount = $apiPayment->getAmount();
        $amountValue = (float) $amount->getValue();
        $service = $barPayment->service;
        $serviceable = $service->serviceable;
        $paymentIban = $apiPayment->getCounterpartyAlias()->getIban();
        $fromIban = $barPaymentable->from_iban;

        // Must be same amount and euro
        if($amountValue != $barPayment->money || $amount->getCurrency() != 'EUR')
            return false;

        // IBANs must match if set
        if($paymentIban != $fromIban && $paymentIban != null && $fromIban != null)
            return false;

        // Settle this payment
        DB::transaction(function() use($account, $apiPayment, $barPayment, $serviceable, $barPaymentable, $paymentIban) {
            // Forward the money
            Self::forwardPayment($account, $apiPayment, $barPayment, $serviceable);

            // Set from IBAN and transfer time if not set, set settled time
            if($barPaymentable->from_iban == null)
                $barPaymentable->from_iban = $paymentIban;
            if($barPaymentable->transferred_at == null)
                $barPaymentable->transferred_at = now();
            $barPaymentable->settled_at = now();
            $barPaymentable->save();

            // Settle the payment
            $barPayment->settle(Payment::STATE_COMPLETED);
        });

        // We handled the payment
        return true;
    }

    /**
     * Send back the given payment to the counter party.
     *
     * @param BunqAccount $account The bunq account.
     * @param ApiPayment $apiPayment The payment to send back.
     */
    private static function refundPayment(BunqAccount $account, ApiPayment $apiPayment) {
        // Build a description
        $description = [config('app.name') . ' ' . __('barpay::service.bunq.unknownPaymentRefund')];
        if(!empty($apiPayment->getDescription()))
            $description[] = $apiPayment->getDescription();
        $description = substr(implode(': ', $description), 0, 140);

        // Build a pointer to send the money back to
        $counterparty = $apiPayment->getCounterpartyAlias();
        $to = new Pointer(
            'IBAN',
            $counterparty->getIban(),
            $counterparty->getDisplayName()
        );

        // Refund the money
        Self::sendPayment($account, $apiPayment, $to, $description);
    }

    /**
     * Forward the given payment to the counter party.
     *
     * @param BunqAccount $account The bunq account.
     * @param ApiPayment $apiPayment The payment to send back.
     * @param Payment $barPayment The bar payment model we're forwarding for.
     * @param ServiceBunqIban $serviceable The bunq IBAN serviceable, holding
     *      the account to forward to.
     */
    private static function forwardPayment(BunqAccount $account, ApiPayment $apiPayment, Payment $barPayment, ServiceBunqIban $serviceable) {
        // Build a pointer to send the money to
        $to = new Pointer(
            'IBAN',
            $serviceable->iban,
            $serviceable->account_holder
        );

        // Forward the payment
        Self::sendPayment(
            $account,
            $apiPayment,
            $to,
            config('app.name') . ' ' . __('barpay::service.bunq.payed') . ': ' . $barPayment->getReference()
        );
    }

    /**
     * Send the given API payment to a new target.
     *
     * @param BunqAccount $account The bunq account that is used.
     * @param ApiPayment $apiPayment The payment to send.
     * @param Pointer $pointer The target to send the payment to.
     * @param string $description The payment description.
     */
    private static function sendPayment(BunqAccount $account, ApiPayment $apiPayment, Pointer $to, string $description) {
        // Queue the payment sending action
        SendBunqPayment::dispatch(
            $account,
            $to,
            $apiPayment->getAmount(),
            $description
        )->delay(now()->addSecond());
    }

    /**
     * Attempt to parse a payment reference from the given payment.
     *
     * The reference is normalized and returned. `null` is returned if no
     * reference was found.
     *
     * @param ApiPayment $apiPayment The payment to check.
     * @return string|null The normalized payment reference, or null.
     */
    private static function parsePaymentReference(ApiPayment $apiPayment) {
        preg_match(
            '/(^|\s)b?ar\s*app\s*(([A-Z0-9]\s*){12})(\s|$)/i',
            $apiPayment->getDescription(),
            $matches
        );
        return isset($matches[2])
            ? strtoupper(str_replace(' ', '', $matches[2]))
            : null;
    }
}
