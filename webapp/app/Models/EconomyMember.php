<?php

namespace App\Models;

use App\Utils\MoneyAmount;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Economy member model.
 *
 * @property int id
 * @property int economy_id
 * @property-read Economy economy
 * @property int user_id
 * @property-read user
 * @property-read string|null nickname
 * @property-read bool show_in_buy
 * @property-read bool show_in_kiosk
 * @property-read aliases
 * @property-read wallets
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class EconomyMember extends Pivot {

    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

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
                // Nickname if set
                if(!empty($this->nickname))
                    return $this->nickname;

                // Use real name
                return $this->real_name;

            case 'real_name':
                // Users full name
                if($this->user != null)
                    return $this->user->first_name . ' ' . $this->user->last_name;

                // Fall back to alias or null if unavailable
                if(($alias = $this->aliases()->first()) != null)
                    return $alias->name;
                return null;

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
        // Starts with search mode
        $startsWith = Str::startsWith($search, "^");
        if($startsWith)
            $search = Str::substr($search, 1);

        // Don't scope anything if the query is empty
        if(empty(trim($search)))
            return;

        // TODO: search for nicknames and a-like as well (members with null user)
        // return $query
        //     // ->selectRaw("CONCAT(user.first_name, ' ', users.last_name) AS name")
        //     // ->where('name', 'LIKE', '%' . escape_like($search) . '%')
        //     ->where('first_name', 'LIKE', '%' . escape_like($search) . '%')
        //     ->orWhere('last_name', 'LIKE', '%' . escape_like($search) . '%');

        // Search for each word separately in the first/last name fields
        $query = $query
            ->where(function($query) use($search, $startsWith) {
                foreach(explode(' ', $search) as $word)
                    if(!empty($word))
                        $query->whereExists(function($query) use($word, $startsWith) {
                            $query->selectRaw('1')
                                ->from('balance_import_alias')
                                ->join('economy_member_alias', 'economy_member_alias.alias_id', 'balance_import_alias.id')
                                ->whereRaw('economy_member.id = economy_member_alias.member_id')
                                ->where('name', 'LIKE', (!$startsWith ? '%' : '' ) . escape_like($word) . '%');
                        })
                        ->orWhereExists(function($query) use($word, $startsWith) {
                            $query->selectRaw('1')
                                ->from('user')
                                ->whereRaw('user.id = economy_member.user_id')
                                ->where(function($query) use($word, $startsWith) {
                                    $query->where('first_name', 'LIKE', (!$startsWith ? '%' : '' ) . escape_like($word) . '%');

                                    if(!$startsWith)
                                        $query->orWhere('last_name', 'LIKE', (!$startsWith ? '%' : '' ) . escape_like($word) . '%');
                                });
                        });
            });

        // Search for each word separately in nickname field
        foreach(explode(' ', $search) as $word)
            $query = $query
                ->orWhere('nickname', 'LIKE', (!$startsWith ? '%' : '' ) . escape_like($word) . '%')
                ->orWhere('tags', 'LIKE', (!$startsWith ? '%' : '' ) . escape_like($word) . '%');

        return $query;
    }

    /**
     * Scope to visibility in buy screens.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param bool [$allow_self=false] Whether to always return the current
     *      authenticated user, ignoring the show_in_buy state.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowInBuy($query, bool $allow_self = false) {
        // Get authenticated user
        $user = barauth()->getUser();

        // Simply show_in_buy state
        if(!$allow_self || $user == null)
            return $query->where('show_in_buy', true);

        // Show_in_buy state, but always allow current user
        return $query->where(function($query) use($user) {
            return $query->where('show_in_buy', true)
                ->orWhere('user_id', $user->id);
        });
    }

    /**
     * Scope to visibility in kiosk.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowInKiosk($query) {
        return $query->where('show_in_kiosk', true);
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
     * Get a relation to all balance import changes for this user.
     */
    public function balanceImportChanges() {
        return $this->hasManyDeepFromRelations(
            $this->aliases(),
            (new BalanceImportAlias)->changes()
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
            ->orderBy('wallet.balance', 'DESC');
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
     * Get a wallet for this economy member, that uses any of the given
     * currencies. This attempts to find a wallet for each currency in order,
     * and returns it if one is found.
     *
     * If no wallet is found, one will be created automatically using the first
     * currency that allows this.
     * Null is only returned if no wallet could be created.
     *
     * @param [Currency] $currencies A list of Currency IDs.
     * @param bool [$error=true] True to throw an error if no wallet was found
     *      or created. False to return null instead.
     * @param bool [$force=false] True to force wallet creation even if wallet
     *      creation is disabled.
     *
     * @return Wallet|null The primary wallet, or null if there is none.
     */
    public function getOrCreateWallet($currencies, $error = true, $force = false) {
        // A currency must be given
        if($currencies->isEmpty()) {
            if($error)
                throw new \Exception("Failed to get or create wallet, no currencies given");
            return null;
        }

        // Get the economy member and fetch all user wallets
        $wallets = $this->wallets ?? collect();

        // Find and return an existing wallet for any of the currencies in order
        $wallet = $currencies
            ->map(function($c) use($wallets) {
                return $wallets->firstWhere('currency_id', $c->id);
            })
            ->filter(function($w) {
                return $w != null;
            })
            ->first();
        if($wallet != null)
            return $wallet;

        // Create a new wallet for the first currency that allows it
        foreach($currencies as $currency) {
            // Find the currency, skip if we cannot create a wallet for it
            if(!$force && !$currency->allow_wallet)
                continue;

            // Create and return a wallet
            return $this->createWallet($currency->id);
        }

        // Throw a error if no wallet could be found/created or return null
        if($error)
            throw new \Exception("Failed to get or create wallet for any of given currencies");
        return null;
    }

    /**
     * Sum all balances for this member.
     *
     * @return MoneyAmount|null The cummulative balance of this member.
     */
    public function sumBalance() {
        // Return wallet balances
        if($this->wallets->isNotEmpty())
            return Economy::sumAmounts($this->wallets, 'balance');

        // TODO: sum approved but not committed balance imports as well
        // // Select last accepted but not commited balance import change in each
        // // system for this user
        // $member = $this;
        // $changes = $member->balanceImportChanges()
        //     // ->selectRaw('balance_import_change.*')
        //     ->join('balance_import_event AS e1', 'balance_import_change.event_id', 'e1.id')
        //     ->where('balance_import_change.id', function($query) use($member) {
        //         $query->fromRaw('balance_import_change c2')
        //             ->select('c2.id')
        //             ->join('balance_import_event AS e2', 'c2.event_id', 'e2.id')
        //             ->whereColumn('e1.system_id', 'e2.system_id')
        //             ->whereColumn('balance_import_change.alias_id', 'c2.alias_id')
        //             ->whereNotNull('c2.balance')
        //             ->whereNotNull('c2.approved_at')
        //             ->whereNull('c2.committed_at')
        //             ->orderBy('c2.created_at', 'DESC')
        //             ->limit(1);
        //     })
        //     ->whereNotNull('balance_import_change.balance')
        //     ->whereNotNull('balance_import_change.approved_at')
        //     ->whereNull('balance_import_change.committed_at')
        //     ->groupBy('e1.system_id', 'alias_id');

        // SQL PoC:
        // SELECT * FROM balance_import_change c1
        // JOIN balance_import_event e1 ON c1.event_id = e1.id
        // WHERE c1.id = (
        //     SELECT c2.id FROM balance_import_change c2
        //     JOIN balance_import_event e2 ON c2.event_id = e2.id
        //     WHERE e1.system_id = e2.system_id
        //         AND c1.alias_id = c2.alias_id
        //         AND c2.balance IS NOT NULL
        //         AND c2.approved_at IS NOT NULL
        //         AND c2.committed_at IS NULL
        //     ORDER BY c2.created_at DESC
        //     LIMIT 1
        // )
        // AND c1.balance IS NOT NULL
        // AND c1.approved_at IS NOT NULL
        // AND c1.committed_at IS NULL
        // GROUP BY system_id, alias_id;

        return null;
    }

    /**
     * Merge the given list of economy members into a single member.
     *
     * This will delete all but the first member.
     *
     * @param [EconomyMember] $members List of economy members.
     * @return EconomyMember Returns the member everything is merged into.
     *
     * @throws \Exception Throws if merging failed due to incorrect wallet
     *      balance.
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

            // Migrate all wallets to the new member
            foreach($oldMembers as $oldMember) {
                foreach($oldMember->wallets as $oldWallet) {
                    // Find compatible wallet on final user to migrate to
                    $wallet = $newMember->wallets()->compatibleWith($oldWallet)->first();
                    if($wallet != null) {
                        $oldWallet->migrateTransactions($wallet);
                        if($oldWallet->balance != 0)
                            throw new \Exception('Cannot migrate wallet transactions and delete, should have balance of 0 after migration transactions');
                        $oldWallet->delete();
                    } else {
                        // No wallet to migrate to, just move this to player
                        $oldWallet->economy_member_id = $newMember->id;
                        $oldWallet->save();
                    }
                }
            }

            // Destroy all other members
            EconomyMember::destroy($oldMembers->pluck('id'));
        });

        return $members[0];
    }

    /**
     * Reset properties a user might configure, such as their display name.
     *
     * Note: this resets the properties in this model, saving it to the database
     * is still required.
     */
    public function resetUserProperties() {
        $this->nickname = null;
        $this->tags = null;
        $this->show_in_buy = true;
        $this->show_in_kiosk = true;
    }
}
