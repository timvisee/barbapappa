<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Scopes\EnabledScope;
use App\Utils\EmailRecipient;

/**
 * Wallet model.
 *
 * This represents a user wallet in an economy.
 *
 * @property int id
 * @property int economy_member_id
 * @property-read EconomyMember economy_member
 * @property string name
 * @property decimal balance
 * @property int currency_id
 * @property-read NewCurrency currency
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Wallet extends Model {

    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    protected $table = 'wallet';

    protected $fillable = [
        'name',
        'currency_id',
    ];

    /**
     * Get the economy member this wallet model is from.
     *
     * @return The economy member.
     */
    public function economyMember() {
        return $this->belongsTo(EconomyMember::class);
    }

    /**
     * Get a relation to the wallet currency.
     *
     * @return Relation to currency.
     */
    public function currency() {
        return $this->belongsTo(NewCurrency::class);
    }

    /**
     * Get a list of mutations, linked to this wallet.
     *
     * This method does not do any permission checking, all linked mutations are
     * simply related.
     *
     * @return The mutations.
     */
    public function mutations() {
        return $this
            ->hasManyDeepFromRelations(
                $this->hasMany(MutationWallet::class),
                (new MutationWallet)->mutation()
            )
            ->latest();
    }

    /**
     * Get all transactions that affected this wallet, having at least one
     * wallet mutation linked to this wallet.
     *
     * This method does not apply any permission checking, all linked
     * transactions are simply related.
     *
     * The returned relation is sorted, putting the newest transactions first.
     *
     * @return The transactions.
     */
    public function transactions() {
        return $this
            ->hasManyDeepFromRelations(
                $this->mutations(),
                (new Mutation)->transaction()
            )
            ->distinct()
            ->latest();
    }

    /**
     * Get the last few transactions that took place, affecting this wallet.
     *
     * This method does not apply any permission checking, all linked
     * transactions are simply returned.
     *
     * @param [$limit=5] The number of last transactions to return at max.
     *
     * @return The last transactions.
     */
    public function lastTransactions($limit = 5) {
        return $this
            ->transactions()
            ->limit($limit);
    }

    /**
     * Format the wallet balance as human readable text using the proper
     * currency format.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     *
     * @return string Formatted balance
     */
    public function formatBalance($format = BALANCE_FORMAT_PLAIN) {
        return $this->currency->format($this->balance, $format);
    }

    /**
     * Build and return the URL for the wallet show page.
     *
     * @return string The wallet show URL.
     */
    // TODO: attempt to implement some eager loading of the economy model
    public function getUrlShow() {
        return route('community.wallet.show', [
            'communityId' => $this->economyMember->economy->community->human_id,
            'economyId' => $this->economyMember->economy_id,
            'walletId' => $this->id,
        ]);
    }

    /**
     * Withdraw the given amount from the wallet.
     *
     * Wallet balance should always be modified using the `withdraw` and
     * `deposit` functions to ensure integrety.
     *
     * This does not create a corresponding transaction or mutation.
     *
     * @param number $amount The amount to withdraw from this wallet.
     *
     * @throws \Exception Throws an exception if the amount is negative or zero.
     */
    public function withdraw($amount) {
        // Assert the amount is positive
        if($amount <= 0)
            throw new \Exception("Failed to withdraw from wallet, amount is <= 0");

        // We must be in a database transaction
        assert_transaction();

        // Decrement the balance
        $this->decrement('balance', $amount);
    }

    /**
     * Deposit the given amount to the wallet.
     *
     * Wallet balance should always be modified using the `withdraw` and
     * `deposit` functions to ensure integrety.
     *
     * This does not create a corresponding transaction or mutation.
     *
     * @param number $amount The amount to deposit to this wallet.
     *
     * @throws \Exception Throws an exception if the amount is negative or zero.
     */
    public function deposit($amount) {
        // Assert the amount is positive
        if($amount <= 0)
            throw new \Exception("Failed to deposit to wallet, amount is <= 0");

        // We must be in a database transaction
        assert_transaction();

        // Increment the balance
        $this->increment('balance', $amount);
    }

    /**
     * Transfer the given amount to the given wallet.
     *
     * This does not create a corresponding transaction or mutation.
     *
     * @param number $amount The amount to transfer to the given wallet.
     * @param Wallet $wallet The wallet to transfer to.
     * @param boolean [$sameEconomy=true] True to require the wallets are in the
     *      same economy.
     *
     * @throws \Exception Throws an exception if the given amount is negative or
     * zero.
     */
    public function transfer($amount, $wallet, $sameEconomy = true) {
        // Assert the amount is positive
        if($amount <= 0)
            throw new \Exception("Failed to transfer money to wallet, amount is <= 0");

        // Verify the currency and economy
        if($this->currency_id != $wallet->currency_id)
            throw new \Exception("Failed to transfer money to wallet, currencies differ");
        if($sameEconomy && $this->economy_id != $wallet->economy_id)
            throw new \Exception("Failed to transfer money to wallet, economies differ");

        // Increment the balance
        $this->withdraw($amount);
        $wallet->deposit($amount);
    }

    /**
     * This method determines whether this wallet can be deleted.
     * This does not involve any permission checking. Instead, it ensures there
     * is no balance left in this wallet.
     *
     * Blocking:
     * - non-zero wallet balance
     *
     * @return boolean True if it can be deleted, false if not.
     */
    public function canDelete() {
        return $this->balance == 0;
    }

    /**
     * Trace back the balance at the given date and time by walking through all
     * transactions and mutations in reverse.
     *
     * This is a costly operation, especially when crossing longer periods. Use
     * this with care.
     *
     * If a date in the future is given, the current balance is returned.
     *
     * @param \Carbon $at The date to trace and get the balance to.
     *
     * @return number The balance at the given time.
     */
    public function traceBalance($at) {
        // Get all mutation changes in this period, and sum the amounts
        $change = $this->mutations()
            ->where('mutation.created_at', '>=', $at)
            ->where('state', Mutation::STATE_SUCCESS)
            ->sum('amount');

        // Apply the delta to the current balance, return the result
        return $this->balance + $change;
    }
}
