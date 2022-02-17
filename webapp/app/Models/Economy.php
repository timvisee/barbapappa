<?php

namespace App\Models;

use App\Jobs\CommitBalanceUpdatesForUser;
use App\Traits\Joinable;
use App\Utils\MoneyAmount;
use App\Utils\MoneyAmountBag;
use BarPay\Models\Payment as PayPayment;
use BarPay\Models\Service as PayService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Economy model.
 *
 * @property int id
 * @property int community_id
 * @property string name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Economy extends Model {

    use HasFactory, Joinable;

    protected $table = 'economy';

    protected $fillable = ['name'];

    /**
     * The limit of quick buy products to list based on the top bought products
     * by the current user.
     */
    const QUICK_BUY_TOP_LIMIT = 6;

    /**
     * The total limit of quick buy products to list.
     */
    const QUICK_BUY_TOTAL_LIMIT = 9;

    /**
     * Get the community this economy is part of.
     *
     * @return The community.
     */
    public function community() {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the bars that use this economy.
     *
     * @return The bars.
     */
    public function bars() {
        return $this->hasMany(Bar::class);
    }

    /**
     * Get a list of economies.
     *
     * @return List of economies.
     */
    public function currencies() {
        return $this->hasMany(Currency::class);
    }

    /**
     * Get a relation to all the products that are part of this economy.
     *
     * @return The products.
     */
    public function products() {
        // TODO: should we filter user made products by default?
        return $this->hasMany(Product::class);
    }

    /**
     * Get the wallets created by users in this economy.
     *
     * @return The wallets.
     */
    public function wallets() {
        return $this->hasManyThrough(
            Wallet::class,
            EconomyMember::class,
            'economy_id',
            'economy_member_id',
            'id',
            'id'
        );
    }

    /**
     * Get a relation to all configured balance import systems linked to this
     * economy.
     *
     * @return List of balance import systems.
     */
    public function balanceImportSystems() {
        return $this->hasMany(BalanceImportSystem::class);
    }

    /**
     * Get a relation to all configured balance import aliases linked to this
     * economy.
     *
     * @return List of balance import aliases.
     */
    public function balanceImportAliases() {
        return $this->hasMany(BalanceImportAlias::class);
    }

    // /**
    //  * Get all the transactions that took place in this economy.
    //  *
    //  * TODO: filter by user?
    //  *
    //  * @return The transactions.
    //  */
    // public function transactions() {
    //     // TODO: return proper selection
    //     return $this->hasMany(Transaction::class);
    //     // return Transaction::where('id', '!=', -1);
    // }

    // /**
    //  * Get the last few transactions that took place in this economy.
    //  *
    //  * TODO: filter by user
    //  *
    //  * @param [$limit=5] The number of last transactions to return at max.
    //  *
    //  * @return The last transactions.
    //  */
    // public function lastTransactions($limit = 5) {
    //     return $this
    //         ->transactions()
    //         ->latest()
    //         ->limit($limit);
    // }

    /**
     * Get a relation to all the transaction mutations that took place in this
     * economy.
     *
     * @return A relation to all economy mutations.
     */
    public function mutations() {
        return $this->hasMany(Mutation::class);
    }

    /**
     * Get a relation to all payment services configured in this economy.
     *
     * @return Relation to payment services.
     */
    public function paymentServices() {
        return $this->hasMany(PayService::class);
    }

    /**
     * Get a relation to all payments made with currencies in this economy.
     *
     * @return Relation to payments.
     */
    public function payments() {
        // TODO: also select through payment service if service link is broken?
        return $this->hasManyThrough(
            PayPayment::class,
            Currency::class,
            'economy_id',
            'currency_id',
            'id',
            'id'
        );
    }

    /**
     * A list of economy member models for users that joined this economy.
     *
     * @return Query for list of economy member models.
     */
    public function members() {
        return $this->hasMany(EconomyMember::class);
    }

    /**
     * Get the inventories in this economy.
     *
     * @return List of inventories.
     */
    public function inventories() {
        return $this->hasMany(Inventory::class);
    }

    /**
     * A list of users that joined this economy.
     *
     * @param array [$pivotColumns] An array of pivot columns to include.
     * @param boolean [$withTimestamps=true] True to include timestamp columns.
     *
     * @return Query for list of users that are member.
     */
    public function memberUsers($pivotColumns = ['id'], $withTimestamps = true) {
        // Query relation with pivot model
        $query = $this->belongsToMany(
                User::class,
                'economy_member',
                'economy_id',
                'user_id'
            )
            ->using(EconomyMember::class);

        // With pivot columns
        if(!empty($pivotColumns))
            $query = $query->withPivot($pivotColumns);

        // With timestamps
        if($withTimestamps)
            $query = $query->withTimestamps();

        return $query;
    }

    /**
     * Count the members this economy has.
     *
     * @return int Member count.
     */
    public function memberCount() {
        return $this->memberUsers([], false)->count();
    }

    /**
     * Let the given user join this economy.
     * This automatically joins the user in the related community.
     *
     * @param User $user The user to join.
     *
     * @throws \Exception Throws if already joined.
     */
    public function join(User $user) {
        $community = $this->community;
        $economy = $this;

        // Join the community and economy
        DB::transaction(function() use($community, $economy, $user) {
            if(!$community->isJoined($user))
                $community->join($user);
            $economy->memberJoin($user);

            // Refresh economy member entries, and commit
            BalanceImportAlias::refreshEconomyMembersForUser($user);
            CommitBalanceUpdatesForUser::dispatch($user->id);
        });
    }

    /**
     * Let the given user leave this economy.
     * Note: this throws an error if the user has not joined.
     *
     * @param User $user The user to leave.
     */
    public function leave(User $user) {
        $community = $this->community;
        $economy = $this;

        // User must not be joined
        if(!$economy->isJoined($user))
            throw new Exception("Unable to leave economy, not a member");

        // Leave economy
        // TODO: make sure user can actually leave this economy (with community)
        DB::transaction(function() use($user, $community, $economy) {
            // Leave economy if member has no balance import alias
            $memberHasAliases = $economy
                ->members()
                ->user($user)
                ->firstOrFail()
                ->aliases()
                ->limit(1)
                ->count() > 0;
            if($memberHasAliases)
                $economy->members()->user($user)->limit(1)->update(['user_id' => null]);
            else
                $economy->memberLeave($user);

            // Leave community if user is orphan
            if($community->isJoined($user))
                $community->leaveIfOrphan($user);
        });
    }

    /**
     * Let the given user leave this community, if it's an orphan.
     *
     * The user will leave if:
     * - it has not joined any economy bars
     *
     * @param User $user The user to leave if orphan.
     * @throws \Exception Throws if the user is not joined.
     */
    public function leaveIfOrphan(User $user) {
        // User must not be a bar member
        $barIds = $this
            ->bars()
            ->select('id')
            ->pluck('id');
        $memberInEconomyBars = BarMember::whereIn('bar_id', $barIds)
            ->where('user_id', $user->id)
            ->limit(1)
            ->count() > 0;
        if($memberInEconomyBars)
            return;

        $this->leave($user);
    }

    /**
     * Go through all wallets of the current user in this economy, and calculate
     * the total balance.
     *
     * This method automatically selects the best currency to return in, and
     * notes whether the returned value is approximate or not. Balances in other
     * currencies are automatically converted using the latest known exchange
     * rates from the currencies table. The method also notes whether the
     * returned value is approximate, which is true when multiple currencies
     * ware summed.
     *
     * If no wallet is created, zero is returned in the default currency.
     *
     * @return MoneyAmount The summed user balance.
     */
    public function calcUserBalance() {
        // Get the user and economy member if available
        $user = barauth()->getUser();
        $economy_member = $this->members()->user($user)->first();

        // Obtain the wallets, return zero with default currency if none
        $wallets = $economy_member?->wallets()->with('currency')->get() ?? collect();
        return Self::sumAmounts($wallets, 'balance');
    }

    /**
     * Go through the given list of models, and sum all money amounts in a
     * shared currency.
     *
     * This method automatically selects the best currency to return in, and
     * notes whether the returned value is approximate or not. Balances in other
     * currencies are automatically converted using the latest known exchange
     * rates from the currencies table. The method also notes whether the
     * returned value is approximate, which is true when multiple currencies
     * ware summed.
     *
     * @return MoneyAmountBag The summed amount.
     */
    // TODO: move this to some utilty class, maybe into MoneyAmount
    // TODO: deprecate and remove?
    public static function sumAmounts($models, string $amountKey) {
        // Return zero if no models are given
        if($models->isEmpty())
            return null;

        // Convert into money amounts, sum through money amount bag
        $amounts = $models
            ->map(function($model) use($amountKey) {
                return new MoneyAmount($model->currency, $model->$amountKey);
            });
        return (new MoneyAmountBag($amounts))->sumAmounts();
    }

    /**
     * Check whether the current authenticated user has any wallet with a
     * non-zero balance.
     *
     * @return boolean True if the user has a wallet, false if not.
     */
    public function userHasBalance() {
        // Get the user and economy member if available
        $user = barauth()->getUser();
        $economy_member = $this->members()->user($user)->first();

        // Return if user is not an economy member
        if($economy_member == null)
            return false;

        // Count any wallets with non-zero balance
        return $economy_member
            ->wallets()
            ->where('balance', '<>', 0)
            ->limit(1)
            ->count() > 0;
    }

    /**
     * Calcualte and format the total balance for all the wallets in this
     * economy for the current user. See `$this->calcUserBalance()`.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     *
     * @return string Formatted balance
     */
    public function formatUserBalance($format = BALANCE_FORMAT_PLAIN) {
        $balance = $this->calcUserBalance();
        return $balance?->formatAmount($format) ?? $balance;
    }

    /**
     * Select the top products that were bought in any of the mutations in the
     * given list. Some products may be excluded. The limit of the products to
     * the return should be given.
     *
     * @param array|null [$mutation_ids=null] A list of IDs of mutations to
     *      search in.
     * @param array|null [$exclude_product_ids=null] A list of product IDs to
     *      exclude from the search.
     * @param int $limit The maximum number of products to return.
     * @param [int]|null [$currency_ids=null] A list of Currency IDs
     *      returned products must have a price configured in in at least one of
     *      them.
     * @param bool [$noExhausted=true] Whether to exclude exhausted products.
     *
     * @return object An array of product models that were found.
     */
    static function selectTopProducts($mutation_ids = null, $exclude_product_ids = null, $limit = 0, $currency_ids = null, $noExhausted = true) {
        // Return nothing if limit is zero
        if($limit <= 0)
            return collect();

        // Build a sub query for selecting the last 100 product mutations
        $lastProducts = MutationProduct::select('product_id', 'quantity');
        if($mutation_ids != null)
            $lastProducts = $lastProducts
                ->whereExists(function($query) use($mutation_ids) {
                    $query->selectRaw('1')
                        ->from('mutation')
                        ->whereRaw('mutation.mutationable_id = mutation_product.id')
                        ->whereIn('mutation.id', $mutation_ids);
                });
        if($exclude_product_ids != null)
            $lastProducts = $lastProducts->whereNotIn('product_id', $exclude_product_ids);
        $lastProducts = $lastProducts
            ->latest()
            ->limit(100);

        // Build a query for counting how often products were bought
        $productCounts = DB::table(DB::raw("({$lastProducts->toSql()}) AS m"))
            ->mergeBindings($lastProducts->getQuery())
            ->select(DB::raw('SUM(SQRT(quantity))'))
            ->whereRaw('m.product_id = product.id');

        // Select the top bought products
        $products = Product::select('*')
            ->selectSub($productCounts, 'count')
            ->havingCurrency($currency_ids)
            ->orderBy('count', 'DESC')
            // TODO: ->havingRaw('count > 0'), replace collection filter below
            ->limit($limit);

        // Hide exhausted products
        if($noExhausted)
            $products = $products->where('exhausted', false);

        // Filter products not being bought ever
        return $products
            ->get()
            ->filter(function($p) {
                return $p->count > 0;
            });
    }

    /**
     * Select the last distinct products that were bought in any of the
     * mutations in the given list. Some products may be excluded. The limit of
     * the products to the return should be given.
     *
     * This method does not have any preference for quantity, and just returns
     * distinct products in order putting the last ordered first.
     *
     * @param object|array $mutation_ids A list of IDs of mutations to search in.
     * @param object|array $exclude_product_ids A list of product IDs to exclude from
     *      the search.
     * @param int $limit The maximum number of products to return.
     * @param array(int)|null $currency_ids A list of Currency IDs returned
     *      products must have a price configured in in at least one of them.
     * @param bool $noExhausted Whether to exclude exhausted products.
     *
     * @return object An array of product models that were found.
     */
    static function selectLastProducts($mutation_ids, $exclude_product_ids, $limit, $currency_ids, $noExhausted) {
        // Return nothing if limit is zero
        if($limit <= 0)
            return collect();

        // Prepare parameters
        $mutation_ids = collect($mutation_ids);
        $exclude_product_ids = collect($exclude_product_ids);

        // Take list of product IDs from mutations ordered by latest
        $mutations = Mutation::with('mutationable')->findMany($mutation_ids);
        $product_ids = $mutation_ids
            ->map(function($id) use($mutations) {
                // TODO: ensure mutationable isn't causing extra queries
                return $mutations
                    ->firstWhere('id', $id)
                    ?->mutationable
                    ?->product_id;
            })
            ->filter(function($product_id) use($exclude_product_ids) {
                return $product_id != null
                    && !$exclude_product_ids->contains($product_id);
            })
            ->unique();

        // Build list of products, take limit
        $query = Product::havingCurrency($currency_ids);
        if($noExhausted)
            $query = $query->where('exhausted', false);
        $products = $query->findMany($product_ids);
        return $product_ids
            ->map(function($id) use($products) {
                return $products->firstWhere('id', $id);
            })
            ->filter(function($product) {
                return $product != null;
            })
            ->take($limit);
    }

    /**
     * Create a list of products personalized for the authenticated in user by
     * estimating their preference, based on buy history inside this economy.
     *
     * TODO: define in detail what steps are taken to generate this list
     *
     * @param [int]|null $currency_ids A list of Currency IDs returned
     *      products must have a price configured in in at least one of them.
     * @param int [$limit=QUICK_BUY_TOTAL_LIMIT] Limit of products to show,
     *      unless searching.
     *
     * @return object A list of products.
     */
    public function quickBuyProducts($currency_ids, $limit = Self::QUICK_BUY_TOTAL_LIMIT) {
        // Get the last 100 product mutation IDs for the current user
        $mutation_ids = $this
            ->mutations()
            ->select('id')
            ->where('owner_id', barauth()->getUser()->id)
            ->where('mutationable_type', MutationProduct::class)
            ->latest()
            ->limit(100)
            ->get()
            ->pluck('id');

        // Get top 5 user bought products in last 100 mutations
        $products = self::selectTopProducts(
            $mutation_ids,
            null,
            Self::QUICK_BUY_TOP_LIMIT,
            $currency_ids,
            true,
        );

        // Add products last bought by user not in list already to total of 8
        $products = $products->concat(
            self::selectLastProducts(
                $mutation_ids,
                $products->pluck('id'),
                $limit - $products->count(),
                $currency_ids,
                true,
            )
        );

        // Fill the list with the top products bought by any user
        if($products->count() < $limit) {
            // Get the last 100 product mutation IDs for any user
            $mutation_ids = $this
                ->mutations()
                ->select('id')
                ->where('mutationable_type', MutationProduct::class)
                ->latest()
                ->limit(100)
                ->get()
                ->pluck('id');

            // Add top products by any user in last 100 mutations not already in list to total of 8
            $products = $products->concat(
                self::selectTopProducts(
                    $mutation_ids,
                    $products->pluck('id'),
                    $limit - $products->count(),
                    $currency_ids,
                    true,
                )
            );
        }

        // Fill with random products
        if($products->count() < $limit) {
            // Add top products by any user in last 100 mutations not already in list to total of 8
            $products = $products->concat(
                $this->products()
                    ->havingCurrency($currency_ids)
                    ->whereNotIn('id', $products->pluck('id'))
                    ->limit($limit - $products->count())
                    ->get(),
            );
        }

        return $products;
    }

    /**
     * Do a simple product search in this economy based on the given query.
     *
     * If the given query is empty or null, all products are returned.
     *
     * @param string|null [$search=null] The query string.
     * @param [int]|null $currency_ids A list of Currency IDs returned
     *      products must have a price configured in in at least one of them.
     *
     * @return object A list of products matching the query.
     */
    public function searchProducts($search = null, $currency_ids = null) {
        // Get a relation to the products we should search
        $products = $this
            ->products()
            ->havingCurrency($currency_ids);

        // Define the query
        if(!empty($search))
            $products = $products->search($search);

        // Put exhausted products last
        $products = $products->orderBy('exhausted');

        // Fetch the products and return
        return $products->get();
    }

    /**
     * This method determines whether this economy can be deleted.
     * This does not involve any permission checking. Instead, it ensures there
     * are no dependencies such as user wallets blocking the safe deletion of
     * this economy.
     *
     * Blocking entities:
     * - user wallets
     *
     * @return boolean True if it can be deleted, false if not.
     */
    public function canDelete() {
        // There must not be any wallets in this economy
        return $this
            ->wallets
            ->every(function($w) {
                return $w->canDelete();
            });
    }

    /**
     * List all entities that currently block this economy from being deleted.
     *
     * Blocking entities:
     * - user wallets
     *
     * See `canDelete()` as well.
     *
     * @return object List of entities that block community deletion.
     */
    public function getDeleteBlockers() {
        return $this
            ->wallets
            ->filter(function($w) {
                return !$w->canDelete();
            });
    }

    /**
     * Delete this economy, and dependent entities that can be deleted.
     *
     * @throws \Exception Throws if this entity cannot be deleted due to
     *      dependent entities. See `canDelete()`.
     */
    public function delete() {
        // Ensure everything can be deleted
        if(!$this->canDelete())
            throw new \Exception('Cannot delete economy, has dependent entities that cannot just be deleted');

        // Start a transaction to delete this economy
        $economy = $this;
        DB::transaction(function() use($economy) {
            // Delete all wallets
            $economy->wallets()->delete();

            // Delete this economy
            parent::delete();
        });
    }
}
