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
        return $this->belongsTo('App\Models\Transaction');
    }

    /**
     * Get the economy this mutation is taking place in.
     *
     * @return The economy.
     */
    public function economy() {
        return $this->belongsTo('App\Models\Economy');
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
        return $this->belongsTo('App\Models\Currency');
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
        return $this->belongsTo('App\Models\User', 'owner_id');
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
                $products[] = ($this->mutationData->quantity != 1 ? $this->mutationData->quantity . 'x ' : '') . $this->mutationData->product->displayName();

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
     * @param bool [$neutral=false] Show a neutral balance, absolute and neutrally colored.
     *
     * @return string Formatted balance
     */
    public function formatAmount($format = BALANCE_FORMAT_PLAIN, $neutral = false) {
        return balance($this->amount, $this->currency->code, $format, null, $neutral);
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
}
