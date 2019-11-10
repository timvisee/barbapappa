<?php

namespace App\Models;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Traits\Joinable;
use App\Utils\EmailRecipient;
use BarPay\Models\Service as PayService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

    use Joinable;

    protected $table = 'economy';

    protected $fillable = ['name'];

    /**
     * The limit of quick buy products to list based on the top bought products
     * by the current user.
     */
    const QUICK_BUY_TOP_LIMIT = 5;

    /**
     * The total limit of quick buy products to list.
     */
    const QUICK_BUY_TOTAL_LIMIT = 8;

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
     * Get a list of economy currencies.
     *
     * @return List of supported currencies.
     */
    public function currencies() {
        return $this->hasMany(EconomyCurrency::class);
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
        return $this->hasMany(Wallet::class);
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
     * Get a relation to all configured balance import aliasses linked to this
     * economy.
     *
     * @return List of balance import aliasses.
     */
    public function balanceImportAliasses() {
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
     * A list of economy member models for users that joined this economy.
     *
     * @return Query for list of economy member models.
     */
    public function members() {
        return $this->hasMany(EconomyMember::class);
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
     * Example return:
     * ```php
     * [1.23, 'EUR', true] // 1.23 euro, approximately
     * ```
     *
     * @return [$balance, $currency, $approximate] The balance, chosen currency
     *      code and whether the value is approximate.
     */
    public function calcBalance() {
        // Get the user and economy member if available
        $user = barauth()->getUser();
        $economy_member = $this->members()->user($user)->first();

        // Obtain the wallets, return zero with default currency if none
        $wallets = $economy_member != null ? $economy_member->wallets()->with('currency')->get() : collect();
        if($wallets->isEmpty())
            return [0, config('currency.default'), false];

        // Build a map with per currency sums
        $sums = [];
        foreach($wallets as $wallet) {
            $currency = $wallet->currency->code;
            $sums[$wallet->currency->code] = ($sums[$wallet->currency->code] ?? 0) + $wallet->balance;
        }

        // Find the currency with the biggest difference from zero, is it approx
        $currency = null;
        $diff = 0;
        foreach($sums as $c => $b)
            if(abs($b) > $diff) {
                $currency = $c;
                $diff = abs($b);
            }
        $approximate = count($sums) > 1;

        // Sum the balance, convert other currencies
        $balance = collect($sums)
            ->map(function($b, $c) use($currency) {
                return $currency == $c ? $b : currency($b, $c, $currency, false);
            })
            ->sum();

        return [$balance, $currency, $approximate];
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
     * economy for the current user. See `$this->calcBalance()`.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     *
     * @return string Formatted balance
     */
    public function formatBalance($format = BALANCE_FORMAT_PLAIN) {
        // Obtain balance information
        $out = $this->calcBalance();
        $balance = $out[0];
        $currency = $out[1];
        $prefix = $out[2] ? '&asymp; ' : '';

        // Format the balance
        return balance($balance, $currency, $format, ['prefix' => $prefix]);
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
     * @param [int]|null [$currency_ids=null] A list of EconomyCurrency IDs
     *      returned products must have a price configured in in at least one of
     *      them.
     *
     * @return array An array of product models that were found.
     */
    function selectTopProducts($mutation_ids = null, $exclude_product_ids = null, $limit = 0, $currency_ids = null) {
        // Return nothing if limit is zero
        if($limit <= 0)
            return collect();

        // Build a sub query for selecting the last 100 product mutations
        $lastProducts = MutationProduct::select('product_id', 'quantity');
        if($mutation_ids != null)
            $lastProducts = $lastProducts
                ->whereExists(function($query) {
                    $query->selectRaw('1')
                        ->from('mutation')
                        ->whereRaw('mutation.mutationable_id = mutation_product.id');
                });
        if($exclude_product_ids != null)
            $lastProducts = $lastProducts->whereNotIn('product_id', $exclude_product_ids);
        $lastProducts = $lastProducts
            ->latest()
            ->limit(100);

        // Build a query for counting how often products were bought
        $productCounts = DB::table(DB::raw("({$lastProducts->toSql()}) AS m"))
            ->mergeBindings($lastProducts->getQuery())
            ->select(DB::raw('SUM(quantity)'))
            ->whereRaw('m.product_id = product.id');

        // Select the top bought products
        $products = Product::select('*')
            ->selectSub($productCounts, 'count')
            ->havingCurrency($currency_ids)
            ->orderBy('count', 'DESC')
            // TODO: ->havingRaw('count > 0'), replace collection filter below
            ->limit($limit);

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
     * @param array $mutation_ids A list of IDs of mutations to search in.
     * @param array $exclude_product_ids A list of product IDs to exclude from
     *      the search.
     * @param int $limit The maximum number of products to return.
     * @param [int]|null $currency_ids A list of EconomyCurrency IDs returned
     *      products must have a price configured in in at least one of them.
     *
     * @return array An array of product models that were found.
     */
    function selectLastProducts($mutation_ids, $exclude_product_ids, $limit, $currency_ids) {
        // Return nothing if limit is zero
        if($limit <= 0)
            return collect();

        // TODO: use join to limit economy instead

        // Find all recent product mutations in order
        $product_ids = MutationProduct::select('product_id')
            ->distinct()
            ->whereExists(function($query) {
                $query->selectRaw('1')
                    ->from('mutation')
                    ->whereRaw('mutation.mutationable_id = mutation_product.id');
            })
            ->whereNotIn('product_id', $exclude_product_ids)
            ->latest()
            ->limit($limit)
            ->get()
            ->pluck('product_id');

        // Find all corresponding products that have a price in allowed currency
        $products = Product::whereIn('id', $product_ids)
            ->havingCurrency($currency_ids)
            ->get();

        // Rebuild the list of products in order, based on ID list order
        return $product_ids
            ->map(function($id) use($products) {
                return $products->firstWhere('id', $id);
            })
            ->filter(function($p) {
                return $p != null;
            });
    }

    /**
     * Create a list of products personalized for the authenticated in user by
     * estimating their preference, based on buy history inside this economy.
     *
     * TODO: define in detail what steps are taken to generate this list
     *
     * @param [int]|null $currency_ids A list of EconomyCurrency IDs returned
     *      products must have a price configured in in at least one of them.
     *
     * @return array A list of products.
     */
    public function quickBuyProducts($currency_ids) {
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
        $products = $this->selectTopProducts(
            $mutation_ids,
            null,
            Self::QUICK_BUY_TOP_LIMIT,
            $currency_ids
        );

        // Add products last bought by user not in list already to total of 8
        $products = $products->merge(
            $this->selectLastProducts(
                $mutation_ids,
                $products->pluck('id'),
                Self::QUICK_BUY_TOTAL_LIMIT - $products->count(),
                $currency_ids
            )
        );

        // Fill the list with the top products bought by any user
        if($products->count() < Self::QUICK_BUY_TOTAL_LIMIT) {
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
            $products = $products->merge(
                $this->selectTopProducts(
                    $mutation_ids,
                    $products->pluck('id'),
                    Self::QUICK_BUY_TOTAL_LIMIT - $products->count(),
                    $currency_ids
                )
            );
        }

        // Fill with random products
        if($products->count() < Self::QUICK_BUY_TOTAL_LIMIT) {
            // Add top products by any user in last 100 mutations not already in list to total of 8
            $products = $products->merge(
                $this->products()
                    ->havingCurrency($currency_ids)
                    ->whereNotIn('id', $products->pluck('id'))
                    ->limit(8 - $products->count())
                    ->get()
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
     * @param [int]|null $currency_ids A list of EconomyCurrency IDs returned
     *      products must have a price configured in in at least one of them.
     *
     * @return array A list of products matching the query.
     */
    public function searchProducts($search = null, $currency_ids) {
        // Get a relation to the products we should search
        $products = $this
            ->products()
            ->havingCurrency($currency_ids);

        // Define the query
        if(!empty($search))
            $products = $products->search($search);

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
     * @return array List of entities that block community deletion.
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
