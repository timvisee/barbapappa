<?php

namespace BarPay\Models;

use BarPay\Controllers\PaymentManualIbanController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Manual IBAN payment data class.
 *
 * This represents a payment data for a manual IBAN transfer.
 *
 * @property int id
 * @property int payment_id
 * @property string iban IBAN to transfer to.
 * @property string ref A reference code.
 * @property datetime|null transferred_at When the user manuall transferred if done.
 * @property datetime|null confirmed_at When the manual transfer was confirmed by the counter party if done.
 * @property string|null bic Optional BIC corresponding to the IBAN.
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class PaymentManualIban extends Model {

    use Paymentable;

    protected $table = "payment_manual_iban";

    protected $casts = [
        'transferred_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    /**
     * Wait this number of seconds after a transfer, before asking a community
     * manager to confirm the payment is received.
     */
    public const TRANSFER_WAIT = 2 * 24 * 60 * 60;

    /**
     * The controller to use for this paymentable.
     */
    public const CONTROLLER = PaymentManualIbanController::class;

    /**
     * The root for views related to this payment.
     */
    public const VIEW_ROOT = 'barpay::payment.manualiban';

    /**
     * The root for language related to this payment.
     */
    public const LANG_ROOT = 'barpay::payment.manualiban';

    const STEP_TRANSFER = 'transfer';
    const STEP_TRANSFERRING = 'transferring';
    const STEP_RECEIPT = 'receipt';

    /**
     * An ordered list of steps in this payment.
     */
    public const STEPS = [
        Self::STEP_TRANSFER,
        Self::STEP_TRANSFERRING,
        Self::STEP_RECEIPT,
    ];

    /**
     * A scope for selecting payments that currently require action by the user
     * that makes the payment, for example, to enter payment credentials.
     *
     * This only returns payments that are in progress.
     *
     * @param Builder $query Query builder for the payment.
     * @param Builder $paymentable_query Query builder for the corresponding
     *      paymentable.
     */
    public static function scopeRequireUserAction($query, $paymentable_query) {
        $paymentable_query
            ->where('from_iban', null)
            ->orWhere('transferred_at', null);
    }

    /**
     * A scope for selecting payments that currently require action by a
     * community/economy manager, for example, to approve the payment.
     *
     * This only returns payments that are in progress.
     *
     * @param Builder $query Query builder for the payment.
     * @param Builder $paymentable_query Query builder for the corresponding
     *      paymentable.
     */
    public static function scopeRequireCommunityAction($query, $paymentable_query) {
        $paymentable_query
            ->where('transferred_at', '<', now()->subSeconds(Self::TRANSFER_WAIT));
    }

    /**
     * Create the paymentable part for a newly started payment, and attach it to
     * the payment.
     *
     * @param Payment $payment The payment to create it for, and to attach it to.
     * @param Service $service The payment service to use.
     *
     * @return Paymentable The created payment.
     */
    public static function startPaymentable(Payment $payment, Service $service) {
        // TODO: require to be in a transaction

        // Get the serviceable
        $serviceable = $service->serviceable;

        // Build the paymentable for the payment
        $paymentable = new PaymentManualIban();
        $paymentable->payment_id = $payment->id;
        $paymentable->to_account_holder = $serviceable->account_holder;
        $paymentable->to_iban = $serviceable->iban;
        $paymentable->to_bic = $serviceable->bic;
        $paymentable->save();

        // Attach the paymentable to the payment
        $payment->setPaymentable($paymentable);

        return $paymentable;
    }

    /**
     * Get the current paymentable step.
     *
     * @return string Paymentable step.
     */
    public function getStep() {
        if($this->transferred_at == null || $this->from_iban == null)
            return Self::STEP_TRANSFER;
        if($this->transferred_at > now()->subSeconds(Self::TRANSFER_WAIT))
            return Self::STEP_TRANSFERRING;
        if($this->confirmed_at == null)
            return Self::STEP_RECEIPT;

        throw new \Exception('Paymentable is in invalid step state');
    }

    /**
     * Block direclty deleting.
     */
    public function delete() {
        throw new \Exception('cannot directly delete paymentable, delete the owning payment instead');
    }
}
