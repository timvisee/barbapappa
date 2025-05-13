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
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use bunq\Model\Generated\Endpoint\PaymentApiObject as ApiPayment;
use bunq\Model\Generated\Object\PointerObject;

class ProcessBunqPaymentEvent implements ShouldQueue {

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
     * Parts of payment descriptions which we should ignore.
     */
    const IGNORE_DESCRIPTIONS = [
        // Contains these if we got a Barbapappa reverted payment back
        'unknown payment refund',
        'terugbetaling onbekende storting',
        // Contains this when bunq count not send payment to counter party
        'payment was reverted for technical reasons',
    ];

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

        // Fetch the payment, and gather some facts
        $apiPayment = ApiPayment::get($this->apiPaymentId, $account->monetary_account_id)
            ->getValue();
        $amount = $apiPayment->getAmount();
        $amountValue = (float) $amount->getValue();
        $description = $apiPayment->getDescription();

        // Ignore negative amounts, or amounts not in euro
        if($amountValue <= 0 || $amount->getCurrency() != 'EUR')
            return;

        // Ignore payments which contain specific descriptions
        $hasIgnoreDescription = collect(Self::IGNORE_DESCRIPTIONS)
            ->contains(function($ignore) use($description) {
                return str_contains(strtolower($description), strtolower($ignore));
            });
        if($hasIgnoreDescription)
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
            $barPaymentable->from_iban ??= $paymentIban;
            $barPaymentable->transferred_at ??= now();
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
        // Build a description and sanitize
        $description = [config('app.name') . ' ' . __('barpay::service.bunq.unknownPaymentRefund')];
        if(!empty($apiPayment->getDescription()))
            $description[] = $apiPayment->getDescription();
        $description = implode(': ', $description);
        $description = Self::sanitizePaymentDescription($description);

        // Build a pointer to send the money back to
        $counterparty = $apiPayment->getCounterpartyAlias();
        $to = new PointerObject(
            'IBAN',
            $counterparty->getIban(),
            $counterparty->getDisplayName(),
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
        $to = new PointerObject(
            'IBAN',
            $serviceable->iban,
            $serviceable->account_holder
        );

        // Forward the payment
        Self::sendPayment(
            $account,
            $apiPayment,
            $to,
            config('app.name')
                . ' '
                . __('barpay::service.bunq.paid', [], config('app.locale'))
                . ': '
                . $barPayment->getReference(),
        );
    }

    /**
     * Send the given API payment to a new target.
     *
     * @param BunqAccount $account The bunq account that is used.
     * @param ApiPayment $apiPayment The payment to send.
     * @param PointerObject $pointer The target to send the payment to.
     * @param string $description The payment description.
     */
    private static function sendPayment(BunqAccount $account, ApiPayment $apiPayment, PointerObject $to, string $description) {
        // Queue the payment sending action
        SendBunqPayment::dispatch(
            $account,
            $to,
            $apiPayment->getAmount(),
            $description,
        )->delay(now()->addSeconds(Self::FORWARD_DELAY));
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

    /**
     * Sanitize a payment description.
     *
     * This is required when sending a payment to a non-bunq bank, because it
     * uses a SEPA transfer with a very limited set of description characters.
     *
     * This does:
     * - Remove all non-allowed characters
     * - Truncates to 140 characters
     * - Trims
     *
     * Note: only alphanumeric characters, spaces and colons are kept.
     *
     * @param string $description The description to sanitize.
     * @return string The sanitized description.
     */
    private static function sanitizePaymentDescription(string $description) {
        return substr(
            trim(preg_replace("/[^A-Za-z0-9: ]/", "", $description)),
            0,
            140,
        );
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
