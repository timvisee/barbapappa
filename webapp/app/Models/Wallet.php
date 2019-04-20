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
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Get the economy this wallet model is part of.
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
     * Rather it's a direct link to the currency used for this wallet.
     *
     * @return The currency.
     */
    public function currency() {
        return $this->belongsTo('App\Models\Currency');
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
            'App\Models\Mutation',
            ['App\Models\MutationWallet'],
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
            ->orderBy('created_at', 'DESC');
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
            // ->orderBy('created_at', 'DESC')
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
        return balance($this->balance, $this->currency->code, $format);
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
}
