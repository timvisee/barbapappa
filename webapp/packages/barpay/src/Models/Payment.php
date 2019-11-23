<?php

namespace BarPay\Models;

use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;
use App\Http\Controllers\CommunityController;
use App\Mail\Email\Payment\Completed;
use App\Mail\Email\Payment\Failed;
use App\Models\Community;
use App\Models\Currency;
use App\Models\Economy;
use App\Models\Mutation;
use App\Models\MutationPayment;
use App\Models\Notifications\PaymentRequiresCommunityAction;
use App\Models\Notifications\PaymentRequiresUserAction;
use App\Models\Transaction;
use App\Models\User;
use App\Perms\AppRoles;
use App\Perms\CommunityRoles;
use App\Utils\EmailRecipient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
 * @property-read Currency currency
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Payment extends Model {

    protected $table = 'payment';

    /**
     * The character length of a payment reference.
     */
    const REFERENCE_LEN = 12;

    const STATE_INIT = 0;
    const STATE_PENDING_USER = 1;
    const STATE_PENDING_COMMUNITY = 2;
    const STATE_PENDING_AUTO = 3;
    const STATE_PROCESSING = 4;
    const STATE_COMPLETED = 5;
    const STATE_REVOKED = 6;
    const STATE_REJECTED = 7;
    const STATE_FAILED = 8;

    /**
     * A list of all availalbe paymentables.
     */
    const PAYMENTABLES = [
        PaymentBunqMeTab::class,
        PaymentBunqIban::class,
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

    public static function boot() {
        parent::boot();

        // Cascade delete to paymentable
        static::deleting(function($model) {
            $model->paymentable()->delete();

            // TODO: delete related notifications?
        });
    }

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
     * @param User $user The user to manage the payments.
     */
    public function scopeCanManage($query, $user = null) {
        // Select the user
        if($user == null)
            $user = barauth()->getUser();
        if($user == null)
            throw new Exception("Failed to filter managable payments, current user is unknown");

        // Allow if user is application admin
        if(perms(AppRoles::presetAdmin()))
            return;

        // Check whether user has manage permission in related community
        $query->whereExists(function($query) use($user) {
            $query->selectRaw('1')
                ->from('service')
                ->whereRaw('payment.service_id = service.id')
                ->join('economy', 'economy.id', 'service.economy_id')
                ->join('community', 'community.id', 'economy.community_id')
                ->join('community_member', 'community_member.community_id', 'community.id')
                ->where('community_member.user_id', $user->id)
                ->where('community_member.role', '>=', CommunityRoles::MANAGER);
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
                                ->whereRaw('payment.paymentable_id = ' . $table . '.id')
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
                                ->whereRaw('payment.paymentable_id = ' . $table . '.id')
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
     * A scope for selecting payments that should expire.
     *
     * This does not return payments that are already completed.
     *
     * @param Builder $query Query builder.
     */
    public function scopeToExpire($query) {
        $query->inProgress(true)
            ->where(function($query) {
                // For each paymentable type, attempt to select expired payments
                foreach(Self::PAYMENTABLES as $paymentable_type) {
                    $query->orWhere(function($query) use($paymentable_type) {
                        // Limit to current paymentable
                        $query->where('paymentable_type', $paymentable_type);

                        // Get table for paymentable
                        $table = (new $paymentable_type)->getTable();

                        // Create query builder for paymentable
                        $query->whereExists(function($p_query) use($query, $table, $paymentable_type) {
                            $p_query->selectRaw('1')
                                ->from($table)
                                ->whereRaw('payment.paymentable_id = ' . $table . '.id')
                                ->where(function($p_query) use($query, $paymentable_type) {
                                    // Use paymentable specific scope for payment and
                                    // paymentable query builders
                                    // TODO: use a real paymentable scope here,
                                    // without main payment query
                                    $paymentable_type::scopeToExpire($query, $p_query);
                                });
                        });
                    });
                }
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
     * Get a relation to the currency.
     *
     * @return Relation to the currency.
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
        // Blue in progress, green/red succeeded, gray failed
        if($this->isFailed())
            $options['color'] = false;
        else if(!$this->isInProgress())
            $options['neutral'] = false;
        return $this->currency->format($this->money, $format, $options);
    }

    /**
     * Build and return the URL for the payment show page.
     *
     * @return string The payment show URL.
     */
    // TODO: attempt to implement some eager loading of the economy model
    public function getUrlShow() {
        return route('payment.show', [
            'paymentId' => $this->id,
        ]);
    }

    /**
     * Get the state identifier, such as `completed` or `revoked`.
     *
     * @return State display name.
     */
    public function stateIdentifier() {
        // Get the state identifier here
        $id = [
            Self::STATE_INIT => 'init',
            Self::STATE_PENDING_USER => 'pendingUser',
            Self::STATE_PENDING_COMMUNITY => 'pendingCommunity',
            Self::STATE_PENDING_AUTO => 'pendingAuto',
            Self::STATE_PROCESSING => 'processing',
            Self::STATE_COMPLETED => 'completed',
            Self::STATE_REVOKED => 'revoked',
            Self::STATE_REJECTED => 'rejected',
            Self::STATE_FAILED => 'failed',
        ][$this->state];
        if(empty($id))
            throw new \Exception("Unknown payment state, cannot get state identifier");

        return $id;
    }

    /**
     * Get the display name for the current payment state.
     *
     * @return State display name.
     */
    public function stateName() {
        return __('pages.payments.state.' . $this->stateIdentifier());
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
     * @param Paymentable The paymentable to attach.
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
     * Check whether this payment failed.
     * This would mean the payment is not in progress anymore, and was not
     * completed.
     *
     * @return bool True if failed, false if not.
     */
    public function isFailed() {
        return !$this->isInProgress() && $this->state != Self::STATE_COMPLETED;
    }

    /**
     * Get the display name for this payment.
     *
     * @return string Display name based on serviceable type.
     */
    public function displayName() {
        // Show generic payment service name if unknown
        if($this->service_id == null)
            return __('barpay::service.unknown.name');

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
        $mut_payment = $this->mutationPayment;
        if($mut_payment != null)
            return $mut_payment->mutation->transaction;
        return null;
    }

    /**
     * Find the economy this payment is linked to.
     *
     * @return Economy|null The community, or null if there is none.
     */
    public function findEconomy() {
        $mut_payment = $this->mutationPayment;
        if($mut_payment != null)
            return $mut_payment->mutation->economy;
        return null;
    }

    /**
     * Find the wallet this payment is linked to.
     *
     * @return Wallet|null The wallet, or null if there is none.
     */
    public function findWallet() {
        $mut_payment = $this->mutationPayment;
        if($mut_payment == null)
            return null;
        $mut_payment = $mut_payment->mutation;

        $mut_wallet = $mut_payment->dependents->first();
        if($mut_wallet == null)
            return null;
        return $mut_wallet->mutationable->wallet;
    }

    /**
     * Start a new payment with the given service, currency and amount.
     *
     * @param Service $service The payment service to use.
     * @param Currency $currency The currency to use.
     * @param float $amount The payment amount.
     * @param User $user User the payment is for.
     *
     * @return Payment The created payment.
     */
    public static function startNew(Service $service, Currency $currency, float $amount, User $user) {
        // We must be in a database transaction
        assert_transaction();

        // TODO: assert this payment service can be used with this currency and amount
        // TODO: assert amount is 0.01 or higher, or should we allow negative as well?

        // Generate a new unique payment reference
        $reference = null;
        while($reference == null || Payment::where('reference', $reference)->count() > 0)
            $reference = random_str(Self::REFERENCE_LEN, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');

        // Build a new payment
        $payment = new Payment();
        $payment->state = Payment::STATE_INIT;
        $payment->service_id = $service->id;
        $payment->user_id = $user->id;
        $payment->reference = $reference;
        $payment->paymentable_id = 0;
        $payment->paymentable_type = '';
        $payment->currency_id = $currency->id;
        $payment->money = $amount;
        $payment->save();

        // Build the paymentable and attach it to the payment
        $paymentable = $service->serviceable::startPaymentable($payment, $service);

        // Assert the payment state is not init anymore
        if($payment->state == Payment::STATE_INIT)
            throw new \Exception('Could not create payment, it\'s state should have been set by the corresponding paymentable');

        return $payment;
    }

    /**
     * Check whether this payment can be cancelled at this moment.
     * The payment must be in progress, and the paymentable that is use must
     * allow it in it's current state.
     *
     * This does not do permission checking. It simply checks whether this
     * payment is eligible for cancellation in it's current state.
     *
     * @return boolean True if it can be cancelled, false if not.
     */
    public function canCancel() {
        return $this->isInProgress() && $this->paymentable->canCancel();
    }

    /**
     * Check what the current payment state should be, and update it is in a
     * different state.
     */
    public function updateState() {
        // Skip if not in progress anymore
        if(!$this->isInProgress())
            return;

        // Gather facts
        $paymentable = $this->paymentable;

        // Set pending states
        if($paymentable->checkRequiresUserAction())
            $this->setState(Payment::STATE_PENDING_USER);
        else if($paymentable->checkRequiresCommunityAction())
            $this->setState(Payment::STATE_PENDING_COMMUNITY);

        // Move to pending from initial state
        if($this->state = Payment::STATE_INIT)
            $this->setState(Payment::STATE_PENDING_AUTO);
    }

    /**
     * Set the state of this payment with some bound checks.
     *
     * @param int $state The state to set to.
     * @param boolean [$save=true] True to save the model after setting the state.
     *
     * @throws \Exception Throws if an invalid state is given.
     */
    public function setState($state, $save = true) {
        // Never allow setting to init
        if($state == Self::STATE_INIT)
            throw new \Exception('Cannot set payment state to init');

        // Do not change if already in this state
        if($this->state == $state)
            return;

        // Set the state, and save
        $this->state = $state;
        if($save)
            $this->save();

        // Show/suppress require user/community admin action notifications
        if($state == Self::STATE_PENDING_USER)
            PaymentRequiresUserAction::notify($this);
        else
            PaymentRequiresUserAction::suppress($this);
        if($state == Self::STATE_PENDING_COMMUNITY)
            PaymentRequiresCommunityAction::notify($this);
        else
            PaymentRequiresCommunityAction::suppress($this);
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
    public function settle(int $state, $save = true) {
        // The given state must be in the settled array
        if(!in_array($state, Self::SETTLED))
            throw new \Exception('Failed to settle payment, given new state is not recognized as settle state');

        // Skip if already in this state
        if($this->state == $state)
            return;

        // We must be in a database transaction
        assert_transaction();

        // Set the state
        $this->setState($state, false);

        // Call event handler in paymentable
        $this->paymentable->onSetState($state);

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

        // Save the model
        if($save)
            $this->save();

        // Invoke a payment completion or failure event
        if($state == Payment::STATE_COMPLETED)
            event(new PaymentCompleted($this));
        else
            event(new PaymentFailed($this));
    }
}
