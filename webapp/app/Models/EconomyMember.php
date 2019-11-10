<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;

/**
 * Economy member model.
 *
 * @property int id
 * @property int economy_id
 * @property-read Economy economy
 * @property int user_id
 * @property-read user
 * @property-read aliases
 * @property-read wallets
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class EconomyMember extends Pivot {

    protected $table = 'economy_member';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $with = ['user', 'aliases'];

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
     * Scope to a specific balance import alias.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param BalanceImportAlias $alias The alias.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAlias($query, BalanceImportAlias $alias) {
        return $query->whereExists(function($query) use($alias) {
            $query->selectRaw('1')
                ->from('economy_member_alias')
                ->whereRaw('economy_member.id = economy_member_alias.member_id')
                ->where('alias_id', $alias->id);
        });
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
        //     // ->selectRaw("CONCAT(user.first_name, ' ', users.last_name) AS name")
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
                                ->from('balance_import_alias')
                                ->join('economy_member_alias', 'economy_member_alias.alias_id', 'balance_import_alias.id')
                                ->whereRaw('economy_member.id = economy_member_alias.member_id')
                                ->where('name', 'LIKE', '%' . escape_like($word) . '%');
                        })
                        ->orWhereExists(function($query) use($word) {
                            $query->selectRaw('1')
                                ->from('user')
                                ->whereRaw('user.id = economy_member.user_id')
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
     * Get a relation to all economy member aliases.
     *
     * @return Relation to the aliases.
     */
    public function aliases() {
        return $this->belongsToMany(
            BalanceImportAlias::class,
            'economy_member_alias',
            'member_id',
            'alias_id'
        );
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
     * @param bool [$force=false] True to force wallet creation even if wallet
     *      creation is disabled.
     *
     * @return Wallet|null The primary wallet, or null if there is none.
     */
    public function getOrCreateWallet($econ_currencies, $error = true, $force = false) {
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
            if(!$force && !$econ_currency->allow_wallet)
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
            'name' => $name ?? __('pages.wallets.nameDefault'),
            'currency_id' => $currency_id,
        ]);
    }

    /**
     * Merge the given list of economy members into a single member.
     *
     * This will delete all but the first member.
     *
     * @param [EconomyMember] $members List of economy members.
     * @return EconomyMember Returns the member everything is merged into.
     */
    public static function mergeAll($members) {
        if($members->count() <= 1)
            return $members[0];

        // List all collective member and alias IDs
        $user_ids = $members->where('user_id', '!=', null)->pluck('user_id')->unique();
        $alias_ids = $members->flatMap(function($member) {
            return $member->aliases()->pluck('balance_import_alias.id');
        })->unique();

        // Members must not have conflicting user ID
        if($user_ids->count() > 1)
            throw new \Exception("Attempting to merge economy members with conflicting user IDs");

        // Sort member list, priority member entry with set user ID
        $members = $members->sortByDesc('user_id')->values();

        DB::transaction(function() use(&$members, $user_ids, $alias_ids) {
            $newMember = $members[0];
            $oldMembers = $members->slice(1);

            // Set all properties on first member, sync aliases
            $newMember->user_id = $user_ids[0];
            $newMember->save();
            $newMember->aliases()->sync($alias_ids);

            // Move all wallets to first member
            // TODO: properly merge wallets/transactions/mutations
            foreach($oldMembers as $oldMember)
                $oldMember->wallets()->update(['economy_member_id' => $newMember->id]);

            // Destroy all other members
            EconomyMember::destroy($oldMembers->pluck('id'));
        });

        return $members[0];
    }
}
