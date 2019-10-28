<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Economy member model.
 *
 * @property int id
 * @property int economy_id
 * @property-read Economy economy
 * @property int user_id
 * @property-read user
 * @property-read wallets
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class EconomyMember extends Pivot {

    protected $table = 'economy_member';

    protected $with = ['user'];

    /**
     * Get dynamic properties.
     *
     * @param string $name Property name.
     *
     * @return mixed|string Result.
     */
    public function __get($name) {
        switch($name) {
            case 'name':
                return $this->user->name;
            default:
                return parent::__get($name);
        }
    }

    /**
     * Check whether dynamic properties exist.
     *
     * @param string $name Property name.
     *
     * @return bool True if exists, false if not.
     */
    public function __isset($name) {
        switch($name) {
            case 'name':
                return true;
            default:
                return parent::__isset($name);
        }
    }

    /**
     * Scope to a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param User $user The user.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUser($query, User $user) {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope a query to only include economy members relevant to the given
     * search query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param string $search The search query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search) {
        // TODO: search for nicknames and a-like as well (members with null user)
        // return $query
        //     // ->selectRaw("CONCAT(users.first_name, ' ', users.last_name) AS name")
        //     // ->where('name', 'LIKE', '%' . escape_like($search) . '%')
        //     ->where('first_name', 'LIKE', '%' . escape_like($search) . '%')
        //     ->orWhere('last_name', 'LIKE', '%' . escape_like($search) . '%');

        // Search for each word separately in the first/last name fields
        return $query
            ->where(function($query) use($search) {
                foreach(explode(' ', $search) as $word)
                    if(!empty($word))
                        $query->whereExists(function($query) use($word) {
                            $query->selectRaw('1')
                                ->from('users')
                                ->whereRaw('users.id = economy_member.user_id')
                                ->where(function($query) use($word) {
                                    $query->where('first_name', 'LIKE', '%' . escape_like($word) . '%')
                                        ->orWhere('last_name', 'LIKE', '%' . escape_like($word) . '%');
                                });
                        });
        });
    }

    /**
     * Get the member economy.
     *
     * @return Economy The economy.
     */
    public function economy() {
        return $this->belongsTo(Economy::class);
    }

    /**
     * Get the member user.
     *
     * @return User The user.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallets that belong to this member.
     *
     * @return Relation to the member wallets.
     */
    public function wallets() {
        return $this
            ->hasMany(Wallet::class, 'economy_member_id')
            ->orderBy('balance', 'DESC');
    }

    /**
     * Get a wallet for this economy member, that uses any of the given
     * currencies. This attempts to find a wallet for each currency in order,
     * and returns it if one is found.
     *
     * If no wallet is found, one will be created automatically using the first
     * currency that allows this.
     * Null is only returned if no wallet could be created.
     *
     * @param [EconomyCurrency] $econ_currencies A list of EconomyCurrency IDs.
     * @param bool [$error=true] True to throw an error if no wallet was found
     *      or created. False to return null instead.
     *
     * @return Wallet|null The primary wallet, or null if there is none.
     */
    public function getOrCreateWallet($econ_currencies, $error = true) {
        // A currency must be given
        if($econ_currencies->isEmpty()) {
            if($error)
                throw new \Exception("Failed to get or create wallet, no currencies given");
            return null;
        }

        // Get the economy member and fetch all user wallets
        $wallets = $this->wallets ?? collect();

        // Find and return an existing wallet for any of the currencies in order
        $wallet = $econ_currencies
            ->map(function($c) use($wallets) {
                return $wallets->firstWhere('currency_id', $c->currency_id);
            })
            ->filter(function($w) {
                return $w != null;
            })
            ->first();
        if($wallet != null)
            return $wallet;

        // Create a new wallet for the first currency that allows it
        foreach($econ_currencies as $econ_currency) {
            // Find the currency, skip if we cannot create a wallet for it
            if(!$econ_currency->allow_wallet)
                continue;

            // Create and return a wallet
            return $this->createWallet($econ_currency->currency_id);
        }

        // Throw a error if no wallet could be found/created or return null
        if($error)
            throw new \Exception("Failed to get or create wallet for any of given currencies");
        return null;
    }

    /**
     * Create a new wallet for this economy member.
     *
     * @param int $currency_id The ID of the currency this wallet uses.
     * @param string|null [$name=null] The name of the wallet, or null to use
     *      the default.
     *
     * @return Wallet The created wallet.
     */
    // TODO: move this somewhere else?
    public function createWallet(int $currency_id, $name = null) {
        // Create the wallet
        return $this->wallets()->create([
            'user_id' => $this->id,
            'economy_id' => $economy->id,
            'name' => $name ?? __('pages.wallets.nameDefault'),
            'currency_id' => $currency_id,
        ]);
    }
}
