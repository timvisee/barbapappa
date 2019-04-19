<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Scopes\EnabledScope;
use App\Utils\EmailRecipient;

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
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Mutation extends Model {

    protected $table = "mutations";

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
        return $this->hasMany('App\Models\Mutation', 'depend_on');
    }

    /**
     * Describe the mutation in the current language as summary to show.
     *
     * @param bool [$detail=false] Describe the transaction in detail if true,
     *          might produce HTML which should not be escaped.
     *
     * @return Mutation description.
     */
    // TODO: default detail parameter to false
    // TODO: describe mutations here!
    // TODO: translate
    public function describe($detail = false) {
        // Do some property checks
        $deposit = $this->amount > 0;

        // Describe based on the mutation type
        switch($this->type) {
        case Self::TYPE_MAGIC:
            return __('pages.mutations.types.magic');

        case Self::TYPE_WALLET:
            if($detail) {
                // Get the wallet, it's name and build a link to it
                $wallet = $this->mutationData->wallet;
                $name = $wallet->name;
                $link = '<a href="' . route('community.wallet.show', [
                    'communityId' => $wallet->economy->community_id,
                    'economyId' => $wallet->economy_id,
                    'walletId' => $wallet->id,
                ]) . '">' . htmlspecialchars($name) . "</a>";

                // Return the description string including the wallet name/link
                if($deposit)
                    return __('pages.mutations.types.walletDepositDetail', ['wallet' => $link]);
                else
                    return __('pages.mutations.types.walletWithdrawDetail', ['wallet' => $link]);
            } else {
                if($deposit)
                    return __('pages.mutations.types.walletDeposit');
                else
                    return __('pages.mutations.types.walletWithdraw');
            }

        case Self::TYPE_PRODUCT:
            // TODO: describe mutation in detail here
            if($deposit)
                return __('pages.mutations.types.productDeposit');
            else
                return __('pages.mutations.types.productWithdraw');

        case Self::TYPE_PAYMENT:
            // TODO: describe mutation in detail here
            if($deposit)
                return __('pages.mutations.types.paymentDeposit');
            else
                return __('pages.mutations.types.paymentWithdraw');

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
     *
     * @return string Formatted balance
     */
    public function formatAmount($format = BALANCE_FORMAT_PLAIN) {
        return balance($this->amount, $this->currency->code, $format);
    }

    /**
     * Get the display name for the current mutation state.
     *
     * @return State display name.
     */
    public function stateName() {
        // TODO: properly transalte here!
        return [
            Self::STATE_PENDING => 'Pending',
            Self::STATE_PROCESSING => 'Processing',
            Self::STATE_SUCCESS => 'Success',
            Self::STATE_FAILED => 'Failed',
        ][$this->state] ?? 'Unknown';
    }
}
