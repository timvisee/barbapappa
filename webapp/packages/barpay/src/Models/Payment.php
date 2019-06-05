<?php

namespace BarPay\Models;

use App\Http\Controllers\CommunityController;
use App\Models\Currency;
use App\Models\Mutation;
use App\Models\MutationPayment;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Payment model.
 *
 * This represents a payment.
 *
 * @property int id
 * @property int state
 * @property int service_id
 * @property int paymentable_id
 * @property string paymentable_type
 * @property-read mixed paymentable
 * @property string|null reference
 * @property decimal amount
 * @property int currency_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Payment extends Model {

    protected $table = "payments";

    /**
     * The character length of a payment reference.
     */
    const REFERENCE_LEN = 12;

    const STATE_INIT = 0;
    const STATE_PENDING_MANUAL = 1;
    const STATE_PENDING_AUTO = 2;
    const STATE_PROCESSING = 3;
    const STATE_COMPLETED = 4;
    const STATE_REVOKED = 5;
    const STATE_REJECTED = 6;
    const STATE_FAILED = 7;

    /**
     * A list of all availalbe paymentables.
     */
    const PAYMENTABLES = [
        PaymentManualIban::class,
    ];

    /**
     * Array containing all states that define a payment is settled (not in
     * progress).
     */
    const SETTLED = [
        Self::STATE_COMPLETED,
        Self::STATE_REVOKED,
        Self::STATE_REJECTED,
        Self::STATE_FAILED,
    ];

    /**
     * A scope for selecting payments that are, or are not in progress.
     */
    public function scopeInProgress($query, $inProgress = true) {
        if($inProgress)
            return $query->whereNotIn('state', Self::SETTLED);
        else
            return $query->whereIn('state', Self::SETTLED);
    }

    /**
     * A scope for selecting payments that can be managed as a community/economy
     * manager or administrator by the currently authenticated user.
     *
     * @param Builder $query Query builder.
     */
    public function scopeCanManage($query) {
        // TODO: properly select economies user can manage!
        $economies = [];

        // Query mutation payment
        $query->whereExists(function($query) use($economies) {
            $query->selectRaw('1')
                ->from('mutations_payment')
                ->whereRaw('payments.id = mutations_payment.payment_id')
                ->leftJoin('mutations', function($leftJoin) {
                    $leftJoin->on('mutations_payment.mutation_id', '=', 'mutations.id');
                })
                // TODO: enable this again once implemented!
                // ->whereIn('economy_id', $economies)
                ;
        });
    }

    /**
     * A scope for selecting payments that currently require action by the user
     * that makes the payment, for example, to enter payment credentials.
     *
     * This only returns payments that are in progress.
     *
     * @param Builder $query Query builder.
     */
    // TODO: deduplicate, use generic method for this kind of scope
    public function scopeRequireUserAction($query) {
        $query->inProgress(true)
            ->where(function($query) {
                // For each paymentable type, attempt to select payments
                // that require user action using it's specific logic and scope
                foreach(Self::PAYMENTABLES as $paymentable_type)
                    $query->orWhere(function($query) use($paymentable_type) {
                        // Limit to current paymentable
                        $query->where('paymentable_type', $paymentable_type);

                        // Get table for paymentable
                        $table = (new $paymentable_type)->getTable();

                        // Create query builder for paymentable
                        $query->whereExists(function($p_query) use($query, $table, $paymentable_type) {
                            $p_query->selectRaw('1')
                                ->from($table)
                                ->whereRaw('payments.paymentable_id = ' . $table . '.id')
                                ->where(function($p_query) use($query, $paymentable_type) {
                                    // Use paymentable specific scope for payment and
                                    // paymentable query builders
                                    // TODO: use a real paymentable scope here,
                                    // without main payment query
                                    $paymentable_type::scopeRequireUserAction($query, $p_query);
                                });
                        });
                    });
            });
    }

    /**
     * A scope for selecting payments that currently require action by a manager
     * in the respective community/economy, for example, to approve a payment.
     *
     * This only returns payments that are in progress.
     *
     * @param Builder $query Query builder.
     */
    public function scopeRequireCommunityAction($query) {
        $query->inProgress(true)
            ->where(function($query) {
                // For each paymentable type, attempt to select payments
                // that require community action using it's specific logic and scope
                foreach(Self::PAYMENTABLES as $paymentable_type)
                    $query->orWhere(function($query) use($paymentable_type) {
                        // Limit to current paymentable
                        $query->where('paymentable_type', $paymentable_type);

                        // Get table for paymentable
                        $table = (new $paymentable_type)->getTable();

                        // Create query builder for paymentable
                        $query->whereExists(function($p_query) use($query, $table, $paymentable_type) {
                            $p_query->selectRaw('1')
                                ->from($table)
                                ->whereRaw('payments.paymentable_id = ' . $table . '.id')
                                ->where(function($p_query) use($query, $paymentable_type) {
                                    // Use paymentable specific scope for payment and
                                    // paymentable query builders
                                    // TODO: use a real paymentable scope here,
                                    // without main payment query
                                    $paymentable_type::scopeRequireCommunityAction($query, $p_query);
                                });
                        });
                    });
            });
    }

    /**
     * Get the relation to the used service linked to this payment.
     *
     * @return Relation to the used service.
     */
    public function service() {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the used user.
     *
     * @return The user.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the used currency.
     *
     * @return The currency.
     */
    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get a relation to the payment mutation this belongs to.
     *
     * @return Relation to payment mutation.
     */
    public function mutationPayment() {
        return $this->hasOne(MutationPayment::class);
    }

    /**
     * Format the money amount for this payment.
     * This shows a neutral value by default.
     *
     * If the user pays money, the returned value is positive. If the user
     * receives/deposits money, the returned value is negative.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     * @param boolean [$invert=false] True to invert the cost value.
     * @param array [$options=[]] List of formatting options.
     *
     * @return string Formatted amount.
     */
    public function formatCost($format = BALANCE_FORMAT_PLAIN, $options = ['neutral' => true]) {
        return $this->currency->formatAmount($this->money, $format, $options);
    }

    /**
     * Get the display name for the current payment state.
     *
     * @return State display name.
     */
    public function stateName() {
        // Get the state key here
        $key = [
            Self::STATE_INIT => 'init',
            Self::STATE_PENDING_MANUAL => 'pendingManual',
            Self::STATE_PENDING_AUTO => 'pendingAuto',
            Self::STATE_PROCESSING => 'processing',
            Self::STATE_COMPLETED => 'completed',
            Self::STATE_REVOKED => 'revoked',
            Self::STATE_REJECTED => 'rejected',
            Self::STATE_FAILED => 'failed',
        ][$this->state];
        if(empty($key))
            throw new \Exception("Unknown payment state, cannot get state name");

        // Translate and return
        return __('pages.payments.state.' . $key);
    }

    /**
     * Get a relation to the specific payment type data related to the used
     * payment service.
     *
     * @return Relation to the payment type data related to the used payment
     * service.
     */
    public function paymentable() {
        return $this->morphTo();
    }

    /**
     * Set the paymentable attached to this service.
     * This is only allowed when no paymentable is set yet.
     *
     * @param mixed The paymentable to attach.
     * @param bool [$save=true] True to immediately save this model, false if
     * not.
     *
     * @throws \Exception Throws if a paymentable was already set.
     */
    public function setPaymentable($paymentable, $save = true) {
        // Assert no paymentable is set yet
        if(!empty($this->paymentable_id) || !empty($this->paymentable_type))
            throw new \Exception('Could not link paymentable to payment, it has already been set');

        // Set the paymentable
        $this->paymentable_id = $paymentable->id;
        $this->paymentable_type = get_class($paymentable);
        if($save)
            $this->save();
    }

    /**
     * Get the unique reference for this payment.
     *
     * @param bool [$prefix=true] Include an application prefix.
     * @param bool [$format=true] Format the reference for humans.
     *
     * @return string The payment reference.
     */
    public function getReference($prefix = true, $format = true) {
        $reference = $this->reference;
        if($format)
            $reference = format_payment_reference($reference);
        if($prefix)
            $reference = 'BarApp ' . $reference;
        return $reference;
    }

    /**
     * Check whehter this payment is still in progress.
     *
     * The payment is in progress when the payment has not successfully
     * completed, rejected or revoked yet.
     * This method also returns `true` if the payment is in the `init` state.
     *
     * @return bool True if in progress, false if not.
     */
    public function isInProgress() {
        return !in_array($this->state, Self::SETTLED);
    }

    /**
     * Get the display name for this payment.
     *
     * @return string Display name based on serviceable type.
     */
    public function displayName() {
        // TODO: this is very inefficient, fix this!
        return $this->service->displayName();
    }

    public function getStepsData() {
        return $this->paymentable->getStepsData();
    }

    /**
     * Find the transaction this payment is linked to.
     *
     * @return Transaction|null The transaction, or null if there is none.
     */
    public function findTransaction() {
        $mut_payment = $payment->mutationPayment;
        if($mut_payment != null)
            return $mut_payment->mutation->transaction;
        return null;
    }

    /**
     * Start a new payment with the given service, currency and amount.
     *
     * @param Service $service The payment service to use.
     * @param Currency $currency The currency to use.
     * @param float $amount The payment amount.
     *
     * @return Payment The created payment.
     */
    public static function startNew(Service $service, Currency $currency, float $amount) {
        // TODO: require to be in a transaction

        // TODO: assert this payment service can be used with this currency and amount
        // TODO: assert amount is 0.01 or higher, or should we allow negative as well?

        // Generate a new unique payment reference
        $reference = null;
        while($reference == null || Payment::where('reference', $reference)->count() > 0)
            $reference = random_str(Self::REFERENCE_LEN, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');

        // Build a new payment
        $payment = new Payment();
        // TODO: should we immediately jump to the `pending_manual` state here?
        $payment->state = Payment::STATE_INIT;
        $payment->service_id = $service->id;
        $payment->reference = $reference;
        $payment->paymentable_id = 0;
        $payment->paymentable_type = '';
        $payment->currency_id = $currency->id;
        $payment->money = $amount;
        $payment->save();

        // Build the paymentable and attach it to the payment
        $paymentable = $service->serviceable::startPaymentable($payment, $service);

        return $payment;
    }

    /**
     * Set the state of this payment with some bound checks.
     *
     * @param int $state The state to set to.
     * @param boolean [$save=true] True to save the model after setting the state.
     *
     * @throws \Exception Throws if an invalid state is given.
     */
    private function setState($state, $save = true) {
        // Never allow setting to init
        if($state == Self::STATE_INIT)
            throw new \Exception('Cannot set payment state to init');

        // Set the state, and save
        $this->state = $state;
        if($save)
            $this->save();
    }

    /**
     * Settle this payment.
     * This in turn settles any linked mutations, transactions and such.
     *
     * @param int $state The new payment state to settle with.
     * @param bool [$save=true] True to save this model after settling.
     *
     * @throws \Exception Throws if an invalid settle state is given.
     */
    public function settle($state, $save = true) {
        // The given state must be in the settled array
        if(!in_array($state, Self::SETTLED))
            throw new \Exception('Failed to settle payment, given new state is not recognized as settle state');

        // Skip if already in this state
        if($this->state == $state)
            return;

        // TODO: must be in a transaction

        // Set the state
        $this->setState($state, false);

        // Settle state of linked payment mutation if there is any
        $mut_payment = $this->mutationPayment;
        if($mut_payment != null) {
            $mutation = $mut_payment->mutation;

            // Determine the state to set linked payment mutations to
            $mutationState = null;
            switch($state) {
            case Self::STATE_COMPLETED:
                $mutationState = Mutation::STATE_SUCCESS;
                break;
            case Self::STATE_REVOKED:
            case Self::STATE_REJECTED:
            case Self::STATE_FAILED:
                $mutationState = Mutation::STATE_FAILED;
                break;
            default:
                throw new \Exception('Unknown state');
            }

            // Settle the mutation
            $mutation->settle($mutationState);
        }

        // TODO: send message to the user

        // Save the model
        if($save)
            $this->save();
    }
}
