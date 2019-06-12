<?php

namespace App\Jobs;

use App\Models\BunqAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use bunq\Model\Generated\Endpoint\Payment;
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
    public function __construct(BunqAccount $account, Payment $payment) {
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
        $payment = Payment::get($this->paymentId, $account->monetary_account_id)
            ->getValue();
        $amount = $payment->getAmount();
        $amountValue = (float) $amount->getValue();

        // Ignore negative amounts, or amounts not in euro
        if($amountValue <= 0 || $amount->getCurrency() != 'EUR')
            return;

        // This payment is unknown, send the money back
        Self::payBack($account, $payment);
    }

    /**
     * Send back the given payment to the counter party.
     *
     * @param BunqAccount $account The bunq account.
     * @param Payment $payment The payment to send back.
     */
    private static function payBack(BunqAccount $account, Payment $payment) {
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
        Payment::create(
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
}
