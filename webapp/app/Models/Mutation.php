<?php

namespace App\Models;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Scopes\EnabledScope;
use App\Utils\EmailRecipient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Mutation model.
 *
 * This represents a mutation of a transaction.
 *
 * @property int id
 * @property int transaction_id
 * @property int mutationable_id
 * @property string mutationable_type
 * @property-read mixed mutationable
 * @property decimal amount
 * @property int currency_id
 * @property int state
 * @property int|null depend_on
 * @property int|null owner_id
 * @property-read User|null owner
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Mutation extends Model {

    protected $table = 'mutation';

    protected $fillable = [
        'economy_id',
        'mutationable_id',
        'mutationable_type',
        'amount',
        'currency_id',
        'state',
        'owner_id',
        'depend_on',
    ];

    /**
     * A list of all mutationable types.
     */
    const MUTATIONABLES = [
        MutationMagic::class,
        MutationWallet::class,
        MutationProduct::class,
        MutationPayment::class,
    ];

    const STATE_PENDING = 1;
    const STATE_PROCESSING = 2;
    const STATE_SUCCESS = 3;
    const STATE_FAILED = 4;

    /**
     * States which define this mutation is settled.
     */
    const SETTLED = [
        Self::STATE_SUCCESS,
        Self::STATE_FAILED,
    ];

    public static function boot() {
        parent::boot();

        // Cascade delete to mutationable
        static::deleting(function($model) {
            $model->mutationable()->delete();
        });
    }

    /**
     * Get the transaction this mutation is part of.
     *
     * @return The transaction.
     */
    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get a relation to the specific mutation type data related to the used
     * mutationable.
     *
     * @return Relation to the mutation type data related to the used mutationable.
     */
    public function mutationable() {
        return $this->morphTo();
    }

    /**
     * Set the mutationable attached to this mutation.
     * This is only allowed when no mutationable is set yet.
     *
     * @param Mutationable The mutationable to attach.
     * @param bool [$save=true] True to immediately save this model, false if
     * not.
     *
     * @throws \Exception Throws if a paymentable was already set.
     */
    public function setMutationable($mutationable, $save = true) {
        // Assert no mutationable is set yet
        if(!empty($this->mutationable_id) || !empty($this->mutationable_type))
            throw new \Exception('Could not link mutationable to mutation, it has already been set');

        // Set the mutationable
        $this->mutationable_id = $mutationable->id;
        $this->mutationable_type = get_class($mutationable);
        if($save)
            $this->save();
    }

    /**
     * Get the economy this mutation is taking place in.
     *
     * @return The economy.
     */
    public function economy() {
        return $this->belongsTo(Economy::class);
    }

    /**
     * Get the used currency.
     *
     * This is not the economy currency as specified in the current economy.
     * Rather it's a direct link to the currency used for this mutation.
     *
     * @return The currency.
     */
    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the mutation this depends on, if set.
     *
     * This mutation might depend on some other mutation before it can be marked
     * as complete. This returns the relation to the dependant, if there is any.
     *
     * @return The mutation this mutation depends on.
     */
    public function dependOn() {
        return $this->belongsTo(Self::class, 'depend_on');
    }

    /**
     * Get all the dependents, mutations that depend on this mutation.
     *
     * Some mutation might depend on this mutation before they can be marked
     * as complete. This returns the relation to the depending, if there are
     * any.
     *
     * @return The mutations depending on this mutation.
     */
    public function dependents() {
        return $this->hasMany(Self::class, 'depend_on');
    }

    /**
     * Get a relation to the user that owns this mutation.
     * This is usually the user that initiated this mutation being part of a
     * transaction.
     *
     * @return Relation to the user that created this transaction.
     */
    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Describe the mutation in the current language as summary to show.
     *
     * By default this provides a simple description. The `$detail` parameter
     * may be set to `true` to show more advanced descriptions, which may
     * include a link and name to an affected wallet. Enabling details is more
     * expensive and can greatly slow down the application when rendering lots
     * of mutations, it should therefore be used with care.
     *
     * If detail mode is turned on, HTML instead of plain text is returned. A
     * description in detail mode must therefore be rendered with `{!! $d !!}`.
     *
     * @param bool [$detail=false] Describe in detail or not.
     *
     * @return string Mutation description.
     */
    public function describe($detail = false) {
        // TODO: properly describe based on the type!

        // Determine direction translation key name
        $dir = $this->amount > 0 ? 'From' : 'To';

        // Describe based on the mutation dir
        switch($this->mutationable_type) {
        case MutationMagic::class:
            return __('pages.mutations.types.magic');

        case MutationWallet::class:
            if($detail) {
                // Get the wallet, it's name and build a link to it
                $wallet = $this->mutationable->wallet;
                if($wallet != null) {
                    $link = '<a href="' . $wallet->getUrlShow() . '">' . e($wallet->name) . "</a>";

                    // Return the description string including the wallet name/link
                    return __('pages.mutations.types.wallet' . $dir . 'Detail', ['wallet' => $link]);
                }
            }
            return __('pages.mutations.types.wallet' . $dir);

        case MutationProduct::class:
            if($detail) {
                // Build a list of products with quantities if not 1
                $mut_product = $this->mutationable;
                $product = $mut_product->product()->withTrashed()->first();
                $name = $product != null ? $product->displayName() : __('pages.products.unknownProduct');
                $products[] = ($mut_product->quantity != 1 ? $mut_product->quantity . 'x ' : '') . $name;

                // Return the description string including the product names
                return __('pages.mutations.types.product' . $dir . 'Detail', ['products' => implode(', ', $products)]);
            } else
                return __('pages.mutations.types.product' . $dir);

        case MutationPayment::class:
            // TODO: describe mutation in detail here
            return __('pages.mutations.types.payment' . $dir);

        default:
            throw new \Exception('Unknown mutation type');
        }
    }

    /**
     * Format the mutation amount as human readable text using the proper
     * currency format.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     * @param array [$options=[]] Format options.
     *
     * @return string Formatted balance
     */
    public function formatAmount($format = BALANCE_FORMAT_PLAIN, $options = []) {
        // Gray if failed
        if($this->state == Self::STATE_FAILED)
            $options['color'] = false;
        return balance($this->amount, $this->currency->code, $format, $options);
    }

    /**
     * Get the display name for the current mutation state.
     *
     * @return State display name.
     */
    public function stateName() {
        // Get the state key here
        $key = [
            Self::STATE_PENDING => 'pending',
            Self::STATE_PROCESSING => 'processing',
            Self::STATE_SUCCESS => 'success',
            Self::STATE_FAILED => 'failed',
        ][$this->state];
        if(empty($key))
            throw new \Exception("Unknown mutation state, cannot get state name");

        // Translate and return
        return __('pages.mutations.state.' . $key);
    }

    /**
     * Set the state of this mutation with some bound checks.
     *
     * @param int $state The state to set to.
     * @param boolean [$save=true] True to save the model after setting the state.
     *
     * @throws \Exception Throws if an invalid state is given.
     */
    private function setState($state, $save = true) {
        // Never allow setting to pending
        if($state == Self::STATE_PENDING)
            throw new \Exception('Cannot set mutation state to pending');

        // Set the state, and save
        $this->state = $state;
        if($save)
            $this->save();
    }

    /**
     * Settle this mutation.
     * This in turn settles any related/depending mutations and the
     * encapsulating transaction.
     *
     * @param int $state The new mutation state to settle with.
     * @param bool [$save=true] True to save this model after settling.
     *
     * @throws \Exception Throws if an invalid settle state is given.
     */
    public function settle(int $state) {
        // The given state must be in the settled array
        if(!in_array($state, Self::SETTLED))
            throw new \Exception('Failed to settle mutation, given new state is not recognized as settle state');

        // Skip if already in this state
        if($this->state == $state)
            return;

        // We must be in a database transaction
        assert_transaction();

        // Set the state
        $oldState = $this->state;
        $this->setState($state, true);

        // Apply the state change logic based on the used mutation type
        if($oldState != $state)
            $this->mutationable->applyState($this, $oldState, $state);

        // Settle any dependents
        // TODO: ensure this doesn't cause issues with circular dependencies
        foreach(($this->dependents ?? []) as $dependent)
            $dependent->settle($state, true);

        // TODO: make sure transaction state is still consistent!

        // Settle the transaction
        $transactionState = null;
        switch($state) {
        case Self::STATE_SUCCESS:
            $transactionState = Transaction::STATE_SUCCESS;
            break;
        case Self::STATE_FAILED:
            $transactionState = Transaction::STATE_FAILED;
            break;
        default:
            throw new \Exception('Unknown mutation state');
        }
        $this->transaction->settle($transactionState, true);
    }

    /**
     * Check whether this mutation is settled.
     *
     * @return bool True if settled, false if in progress.
     */
    public function isSettled() {
        return in_array($this->state, Self::SETTLED);
    }

    /**
     * Undo the mutation.
     * This deletes the mutation model if $delete is true.
     *
     * A database transaction must be active.
     *
     * @param bool [$delete=false] True to delete the mutation model, false to
     *      leave it.
     * @param bool [$force=false] True to attempt to force.
     *
     * @throws \Exception Throws if we cannot undo right now or if not in a
     *      transaction.
     */
    // TODO: delete by default, or set states back to pending?
    public function undo($delete = false, $force = false) {
        // We must be in a database transaction
        assert_transaction();

        // Assert we can undo, then undo the mutationable
        if(!$this->canUndo($force))
            throw new \Exception("Attempting to undo transaction mutation while this is not allowed");
        $this->mutationable->undo();

        // Delete the model
        if($delete)
            $this->delete();
    }

    /**
     * This method checks whether this mutation can be undone.
     * This depends on the mutation type.
     *
     * Time is not considered here.
     *
     * @param bool [$force=false] True to check whether we can undo when
     * forcing.
     *
     * @return bool True if it can be undone, false if not.
     */
    public function canUndo($force = false) {
        switch($this->mutationable_type) {
        case MutationWallet::class:
        case MutationProduct::class:
            return true;
        case MutationPayment::class:
            return false;
        case MutationMagic::class:
            return $force;

        default:
            throw new \Exception('Unknown mutation type');
        }
    }

    /**
     * Increment the given amount to the mutation amount.
     *
     * Mutation amounts should always be modified using the `incrementAmount`
     * and `decrementAMount` functions to ensure integrety.
     *
     * @param number $amount The amount to increment the mutation amount with.
     *
     * @throws \Exception Throws an exception if the amount is negative.
     */
    public function incrementAmount($amount) {
        // Assert the amount is positive
        if($amount == 0)
            return;
        if($amount <= 0)
            throw new \Exception("Failed to increment mutation amount, amount is < 0");

        // We must be in a database transaction
        assert_transaction();

        // Increment the amount
        $this->increment('amount', $amount);
    }

    /**
     * Decrement the given amount from the mutation amount.
     *
     * Mutation amounts should always be modified using the `incrementAmount`
     * and `decrementAMount` functions to ensure integrety.
     *
     * @param number $amount The value to decrement the mutation amount with.
     *
     * @throws \Exception Throws an exception if the amount is negative.
     */
    public function decrementAmount($amount) {
        // Assert the amount is positive
        if($amount == 0)
            return;
        if($amount < 0)
            throw new \Exception("Failed to decrement mutation amount, amount is < 0");

        // We must be in a database transaction
        assert_transaction();

        // Decrement the amount
        $this->decrement('amount', $amount);
    }

    /**
     * Find a list of communities this mutation took part in.
     *
     * For example, if this is a wallet mutation, the community the
     * wallet is in will be part of the returned list.
     *
     * @return Collection List of communities, may be empty.
     */
    public function findCommunities() {
        return $this->mutationable->findCommunities();
    }
}
