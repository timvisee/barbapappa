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
 * @property int economy_id
 * @property string name
 * @property decimal balance
 * @property int currency_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Wallet extends Model {

    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    protected $table = "wallets";

    protected $fillable = [
        'economy_id',
        'name',
        'currency_id',
    ];

    /**
     * Get the user this wallet model is from.
     *
     * @return The user.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the economy this wallet model is part of.
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
     * Rather it's a direct link to the currency used for this wallet.
     *
     * @return The currency.
     */
    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get a relation to the economy currency used in this wallet.
     *
     * @return A relation to the economy currency.
     */
    public function economyCurrency() {
        return $this->hasOneThrough(
            EconomyCurrency::class,
            Currency::class,
            'id',
            'currency_id',
            'currency_id',
            'id'
        );
    }

    // TODO: is this replaced by the `mutations` function?
    // /**
    //  * Get a list of wallet mutations, linked to this wallet.
    //  * These aren't regular mutations, rather they are wallet specific
    //  * mutations which are linked to a regular mutation.
    //  *
    //  * @return The wallet mutations.
    //  */
    // public function walletMutations() {
    //     // TODO: implement this!
    //     throw new \Exception("not yet implemented");
    //     return $this->hasMany('App\Models\WalletMutation');
    // }

    /**
     * Get a list of mutations, linked to this wallet.
     *
     * This method does not do any permission checking, all linked mutations are
     * simply related.
     *
     * @return The mutations.
     */
    public function mutations() {
        return $this->hasManyDeep(
            Mutation::class,
            [MutationWallet::class],
            [
                'wallet_id',
                'id',
            ],
            [
                'id',
                'mutation_id',
            ]
        );
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
                (new \App\Models\Mutation)->transaction()
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
        return $this->currency->formatAmount($this->balance, $format);
    }

    /**
     * Build and return the URL for the wallet show page.
     *
     * @return string The wallet show URL.
     */
    // TODO: attempt to implement some eager loading of the economy model
    public function getUrlShow() {
        return route('community.wallet.show', [
            // TODO: can we use $this->economy->community_id here?
            'communityId' => $this->economy->community->human_id,
            'economyId' => $this->economy_id,
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

        // TODO: assert we're in a transaction

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

        // TODO: assert we're in a transaction

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

        // TODO: assert we're in a transaction

        // Verify the currency and economy
        if($this->currency_id != $wallet->currency_id)
            throw new \Exception("Failed to transfer money to wallet, currencies differ");
        if($sameEconomy && $this->economy_id != $wallet->economy_id)
            throw new \Exception("Failed to transfer money to wallet, economies differ");

        // Increment the balance
        $this->withdraw($amount);
        $wallet->deposit($amount);
    }
}
