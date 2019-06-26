<?php

namespace BarPay\Models;

use App\Models\BunqAccount;
use App\Models\Notifications\PaymentRequiresUserAction;
use App\Models\User;
use BarPay\Controllers\PaymentBunqIbanController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Bunq IBAN payment data class.
 *
 * This represents a payment data for a bunq IBAN transfer.
 *
 * @property int id
 * @property-read Payment payment
 * @property string|null from_iban IBAN user transfers from.
 * @property datetime|null transferred_at When the user manually transferred if done.
 * @property datetime|null checked_at Last time the transaction was checked at.
 * @property datetime|null settled_at When the bunq transfer was settled by the counter party if done.
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class PaymentBunqIban extends Model {

    use Paymentable;

    protected $table = "payment_bunq_iban";

    protected $casts = [
        'transferred_at' => 'datetime',
        'settled_at' => 'datetime',
    ];

    /**
     * The controller to use for this paymentable.
     */
    public const CONTROLLER = PaymentBunqIbanController::class;

    /**
     * The root for views related to this payment.
     */
    public const VIEW_ROOT = 'barpay::payment.bunqiban';

    /**
     * The root for language related to this payment.
     */
    public const LANG_ROOT = 'barpay::payment.bunqiban';

    const STEP_TRANSFER = 'transfer';
    const STEP_RECEIPT = 'receipt';

    /**
     * An ordered list of steps in this payment.
     */
    public const STEPS = [
        Self::STEP_TRANSFER,
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
        // Do not include this for community action
        $paymentable_query->whereRaw('1 = 2');
    }

    /**
     * Get the bunq account.
     *
     * @return The bunq account.
     */
    // TODO: use a relation, make this more efficient
    public function getBunqAccount() {
        return $this->payment->service->serviceable->bunqAccount;
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
        // We must be in a database transaction
        assert_transaction();

        // Get the serviceable
        $serviceable = $service->serviceable;

        // Build the paymentable for the payment
        $paymentable = new PaymentBunqIban();
        $paymentable->save();

        // Attach the paymentable to the payment
        $payment->setState(Payment::STATE_PENDING_USER, false);
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
        if($this->settled_at == null)
            return Self::STEP_RECEIPT;

        throw new \Exception('Paymentable is in invalid step state');
    }

    /**
     * Invoked when the current step for this payment changes.
     */
    public function onStepChange($step) {}

    /**
     * Check whether this payment can be cancelled at this moment.
     *
     * This does not do permission checking. It simply checks whether this
     * payment is eligible for cancellation in it's current state.
     *
     * @return boolean True if it can be cancelled, false if not.
     */
    public function canCancel() {
        return $this->getStep() == Self::STEP_TRANSFER;
    }

    /**
     * Block directly deleting.
     */
    public function delete() {
        throw new \Exception('cannot directly delete paymentable, delete the owning payment instead');
    }

    /**
     * Called when the state of the payment is changed.
     *
     * @param int $state The new state.
     */
    public function onSetState($state, $save = true) {}
}
