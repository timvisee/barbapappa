<?php

namespace App\Jobs;

use App\Models\BunqAccount;
use BarPay\Models\Payment;
use BarPay\Models\PaymentBunqIban;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use bunq\Model\Generated\Endpoint\Payment as BunqPayment;
use bunq\Model\Generated\Object\Pointer;

class ProcessBunqPaymentEvent implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $accountId;
    private $paymentId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(BunqAccount $account, BunqPayment $payment) {
        $this->accountId = $account->id;
        $this->paymentId = $payment->getId();
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
        $payment = BunqPayment::get($this->paymentId, $account->monetary_account_id)
            ->getValue();
        $amount = $payment->getAmount();
        $amountValue = (float) $amount->getValue();

        // Ignore negative amounts, or amounts not in euro
        if($amountValue <= 0 || $amount->getCurrency() != 'EUR')
            return;

        // Attempt to handle this as bunq IBAN transaction
        if(Self::handleBunqIban($payment))
            return;

        // This payment is unknown, send the money back
        Self::payBack($account, $payment);
    }

    // TODO: do all this in transaction with fresh models
    private static function handleBunqIban(BunqPayment $payment) {
        // Search for reference in payment
        $ref = Self::parsePaymentReference($payment);
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
        $amount = $payment->getAmount();
        $amountValue = (float) $amount->getValue();

        // Must be same amount and euro
        if($amountValue != $barPayment->money || $amount->getCurrency() != 'EUR')
            return false;

        // TODO: do source IBAN check

        // Settle this payment
        DB::transaction(function() use($barPayment) {
            $barPayment->settle(Payment::STATE_COMPLETED);
        });

        // TODO: forward payment to proper account

        // TODO: return true here, we succeeded!
        return false;
    }

    /**
     * Send back the given payment to the counter party.
     *
     * @param BunqAccount $account The bunq account.
     * @param BunqPayment $payment The payment to send back.
     */
    private static function payBack(BunqAccount $account, BunqPayment $payment) {
        // Build a description
        // TODO: use language file here
        $description = [config('app.name') . ' unknown payment refund'];
        if(!empty($payment->getDescription()))
            $description[] = $payment->getDescription();
        $description = substr(implode(': ', $description), 0, 140);

        // Build a pointer to send the money back to
        $counterparty = $payment->getCounterpartyAlias();
        $pointer = new Pointer(
            'IBAN',
            $counterparty->getIban(),
            $counterparty->getDisplayName()
        );

        // Send the money back
        BunqPayment::create(
            $payment->getAmount(),
            $pointer,
            $description,
            $account->monetary_account_id,
            null,
            null,
            null,
            []
        );
    }

    /**
     * Attempt to parse a payment reference from the given payment.
     *
     * The reference is normalized and returned. `null` is returned if no
     * reference was found.
     *
     * @param BunqPayment $payment The payment to check.
     * @return string|null The normalized payment reference, or null.
     */
    private static function parsePaymentReference(BunqPayment $payment) {
        preg_match(
            '/(^|\s)b?ar\s*app\s*(([A-Z0-9]\s*){12})(\s|$)/i',
            $payment->getDescription(),
            $matches
        );
        return isset($matches[2])
            ? strtoupper(str_replace(' ', '', $matches[2]))
            : null;
    }
}
