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
 * @property int type
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

    protected $table = "mutations";

    protected $fillable = [
        'economy_id',
        'type',
        'amount',
        'currency_id',
        'state',
        'owner_id',
        'depend_on',
    ];

    const TYPE_MAGIC = 1;
    const TYPE_WALLET = 2;
    const TYPE_PRODUCT = 3;
    const TYPE_PAYMENT = 4;
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

    /**
     * The child mutation types that belong to this mutation for a given type.
     *
     * This list is dynamically used to link child mutation data to this
     * mutation, if this mutation is of a type that has additional data.
     */
    protected static $typeModels = [
        Self::TYPE_WALLET => MutationWallet::class,
        Self::TYPE_PRODUCT => MutationProduct::class,
        Self::TYPE_PAYMENT => MutationPayment::class,
    ];

    /**
     * Get the transaction this mutation is part of.
     *
     * @return The transaction.
     */
    public function transaction() {
        return $this->belongsTo(Transaction::class);
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
        // Determine direction translation key name
        $dir = $this->amount > 0 ? 'From' : 'To';

        // Describe based on the mutation dir
        switch($this->type) {
        case Self::TYPE_MAGIC:
            return __('pages.mutations.types.magic');

        case Self::TYPE_WALLET:
            if($detail) {
                // Get the wallet, it's name and build a link to it
                $wallet = $this->mutationData->wallet;
                $name = $wallet->name;
                $link = '<a href="' . $wallet->getUrlShow() . '">' . e($name) . "</a>";

                // Return the description string including the wallet name/link
                return __('pages.mutations.types.wallet' . $dir . 'Detail', ['wallet' => $link]);
            } else
                return __('pages.mutations.types.wallet' . $dir);

        case Self::TYPE_PRODUCT:
            if($detail) {
                // Build a list of products with quantities if not 1
                $product = $this->mutationData->product()->withTrashed()->first();
                $name = $product != null ? $product->displayName() : __('pages.products.unknownProduct');
                $products[] = ($this->mutationData->quantity != 1 ? $this->mutationData->quantity . 'x ' : '') . $name;

                // Return the description string including the product names
                return __('pages.mutations.types.product' . $dir . 'Detail', ['products' => implode(', ', $products)]);
            } else
                return __('pages.mutations.types.product' . $dir);

        case Self::TYPE_PAYMENT:
            // TODO: describe mutation in detail here
            return __('pages.mutations.types.payment' . $dir);

        default:
            throw new \Exception("Unknown mutation type, cannot describe");
        }
    }

    /**
     * Check whether the mutation has any related child mutation data, based on
     * the mutation type. See `Self::mutationData()`.
     *
     * @return bool True if this mutation has child data, false if not.
     */
    public function hasMutationData() {
        return isset(Self::$typeModels[$this->type]);
    }

    /**
     * Get the relation to the child mutation data object, if available.
     *
     * For example, this would provide a relation to the `MutationPayment`
     * object that belongs to this mutation, for a payment mutation.
     *
     * @return HasOne The child mutation data model relation.
     * @throws \Exception Throws if the current mutation type doesn't have
     *      additional mutation data.
     */
    public function mutationData() {
        // Make sure this mutation type has additional data
        if(!$this->hasMutationData())
            throw new \Exception(
                "attempted to get relation to additional mutation data, " .
                "for a mutation type that doesn't have this"
            );

        // Return the relation
        return $this->hasOne(Self::$typeModels[$this->type], 'mutation_id', 'id');
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

        // TODO: must be in a transaction

        // Set the state
        $this->setState($state, true);

        // Settle any dependents
        // TODO: ensure this doesn't cause issues with circular dependencies
        foreach(($this->dependents ?? []) as $dependent)
            $dependent->settle($state, true);

        // TODO: do mutation specific logic on settling and/or block
        // - payment: do not allow settle if not settled
        // - wallet: transfer moneys
        // TODO: move this into a mutation specific function
        if($state == Self::STATE_SUCCESS && $this->type == Self::TYPE_WALLET) {
            $wallet = $this->mutationData->wallet;
            if($wallet->currency_id != $this->currency_id)
                throw new \Exception('Wallet mutation and wallet differ in currency, cannot process');
            if($this->amount > 0)
                $wallet->withdraw($this->amount);
            else
                $wallet->deposit(-$this->amount);
        }

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
     *
     * @throws \Exception Throws if we cannot undo right now or if not in a
     *      transaction.
     */
    public function undo($delete = false) {
        // Assert we have an active database transaction
        if(DB::transactionLevel() <= 0)
            throw new \Exception("Mutation can only be undone when database transaction is active");

        // Assert we can undo
        if(!$this->canUndo())
            throw new \Exception("Attempting to undo transaction mutation while this is not allowed");

        // Undo on the mutation data
        if($this->hasMutationData())
            $this->mutationData->undo();

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
     * @return bool True if it can be undone, false if not.
     */
    public function canUndo() {
        switch($this->type) {
        case Self::TYPE_MAGIC:
        case Self::TYPE_WALLET:
        case Self::TYPE_PRODUCT:
            return true;
        case Self::TYPE_PAYMENT:
            return false;
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

        // TODO: assert we're in a transaction

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

        // TODO: assert we're in a transaction

        // Decrement the amount
        $this->decrement('amount', $amount);
    }
}
