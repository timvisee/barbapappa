<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use App\Models\Currency;
use App\Models\EconomyMember;
use App\Models\Mutation;
use App\Models\MutationProduct;
use App\Models\MutationWallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class KioskController extends Controller {

    /**
     * The default limit for items in the kiosk listview.
     */
    const LIST_LIMIT = 15;

    /**
     * Limit of members to show that are common buyers.
     */
    const MEMBERS_TOP_LIMIT = 5;

    /**
     * Limit of members to show that are recent buyers.
     */
    const MEMBERS_RECENT_LIMIT = 10;

    /**
     * Limit of products to show that are commonly bought
     */
    const PRODUCTS_TOP_LIMIT = 10;

    /**
     * Limit of products to show that were recently bought.
     */
    const PRODUCT_RECENT_LIMIT = 5;

    /**
     * Kiosk buy page.
     *
     * @return Response
     */
    public function main() {
        // Get the bar and session user
        $bar = kioskauth()->getBar();

        // Show the bar page
        return view('kiosk.main')
            ->with('bar', $bar)
            ->with('economy', $bar->economy);
    }

    /**
     * API route for listing economy members in this bar.
     *
     * @return Response
     */
    public function apiMembers() {
        // TODO: list 10 latest members, and 5 additional most common

        // Get the bar, current user and the search query
        $bar = kioskauth()->getBar();
        $economy = $bar->economy;
        $search = \Request::query('q');

        // Return a default user list, or search based on a given query
        if(empty($search))
            $members = self::getMemberList($bar, self::LIST_LIMIT);
        else
            $members = $economy
                ->members()
                ->search($search)
                ->limit(self::LIST_LIMIT)
                ->get();

        // Set and limit fields to repsond with and sort
        $members = $members
            ->map(function($m) {
                $m->name = $m->name;
                return $m->only(['id', 'name']);
            })
            ->sortBy('name')
            ->values();

        return $members;
    }

    /**
     * API route for listing products in this bar.
     *
     * // TODO: limit product fields returned here
     *
     * @return Response
     */
    public function apiProducts() {
        // TODO: list 10 most common products, and 5 additional latest

        // Get the bar and the search query
        $bar = kioskauth()->getBar();
        $economy = $bar->economy;

        // Select currency
        // TODO: find proper currency here, possibly show selection for it
        // $currency = $economy->currencies()->firstOrFail();
        $currency_id = $economy
            ->mutations()
            ->latest()
            ->limit(100)
            ->pluck('currency_id')
            ->countBy()
            ->keys()[0];
        $currency = Currency::findOrFail($currency_id);
        $currencies = [$currency];

        // Search, or use top products
        $search = \Request::get('q');
        if(!empty($search))
            $products = $bar->economy->searchProducts($search, [$currency->id]);
        else
            $products = self::getProductList($bar, self::LIST_LIMIT, [$currency->id]);

        // Add formatted price fields
        $products = $products
            ->map(function($product) use($currencies) {
                $product->price_display = $product->formatPrice($currencies);
                return $product;
            })
            ->sortBy('name')
            ->values();

        return $products;
    }

    /**
     * Create a list of products personalized for the authenticated in user by
     * estimating their preference, based on buy history inside this economy.
     *
     * TODO: define in detail what steps are taken to generate this list
     *
     * @param Bar $bar The bar to get products for.
     * @parma int $limit The limit of products to return, might be less.
     * @param [int]|null $currency_ids A list of Currency IDs returned
     *      products must have a price configured in in at least one of them.
     *
     * @return object A list of products.
     */
    private static function getProductList(Bar $bar, $limit, $currency_ids) {
        // Return nothing if the limit is too low
        if($limit <= 0)
            return collect();

        // Get most common products
        $products = self::getTopProductList($bar, self::PRODUCTS_TOP_LIMIT, $currency_ids);

        // Add recent members
        $products = $products->concat(
            self::getRecentProductList($bar, self::PRODUCT_RECENT_LIMIT, $currency_ids, $products->pluck('id'))
        );

        // TODO: extend list with random products if not LIST_LIMIT yet

        return $products;
    }

    /**
     * Get a list of top bought products.
     *
     * @param Bar $bar The bar to get products for.
     * @param int $limit Product limit.
     * @param [int]|null $currency_ids A list of Currency IDs returned
     *      products must have a price configured in in at least one of them.
     *
     * @return object A list of products.
     */
    private static function getTopProductList(Bar $bar, $limit, $currency_ids) {
        // Get facts
        $economy = $bar->economy;

        // List latest product mutations
        // TODO: update select
        $product_mutations = $economy
            ->mutations()
            // ->select('id', 'mutationable_type', 'mutationable_id')
            ->where('mutationable_type', MutationProduct::class)
            ->whereIn('currency_id', $currency_ids)
            ->latest()
            ->limit(100)
            ->with('mutationable')
            ->get();

        // List product IDs sorted by most bought
        $product_ids = $product_mutations
            ->reduce(function($list, $item) {
                $key = strval($item->mutationable->product_id);
                if(isset($list[$key]))
                    $list[$key] += $item->mutationable->quantity;
                else
                    $list[$key] = $item->mutationable->quantity;
                return $list;
            }, collect())
            ->sort()
            ->reverse()
            ->take($limit)
            ->keys();

        // Extract list of products from mutations result so we don't have to
        // query the database again
        $products = $product_mutations
            ->reduce(function($products, $p) {
                $products[strval($p->mutationable->product_id)] = $p->mutationable->product;
                return $products;
            }, collect());

        // Transform list of product IDs into actual products
        return $product_ids
            ->map(function($product_id) use($products) {
                return $products[strval($product_id)];
            })
            ->filter(function($product) {
                return $product != null;
            });
    }

    /**
     * Get a list of recently bought products.
     *
     * @param Bar $bar The bar to get products for.
     * @param int $limit Product limit.
     * @param [int]|null $currency_ids A list of Currency IDs returned
     *      products must have a price configured in in at least one of them.
     * @param [int]|null $ignore_product_ids A list of product IDs to ignore.
     *
     * @return object A list of products.
     */
    private static function getRecentProductList(Bar $bar, $limit, $currency_ids, $ignore_product_ids = []) {
        // Get facts
        $economy = $bar->economy;
        $ignore_product_ids = collect($ignore_product_ids);

        // List latest product mutations
        // TODO: update select
        // TODO: ignore product IDs in this query instead of later on in collection
        $product_mutations = $economy
            ->mutations()
            // ->select('id', 'mutationable_type', 'mutationable_id')
            ->where('mutationable_type', MutationProduct::class)
            ->whereIn('currency_id', $currency_ids)
            ->latest()
            ->limit(100)
            ->with('mutationable')
            ->get();

        // Take limit of recent products, skip null or ignored products
        return $product_mutations
            ->filter(function($p) use($ignore_product_ids) {
                return $p->mutationable->product_id != null
                    && !$ignore_product_ids->contains($p->mutationable->product_id);
            })
            ->unique(function($p) use($ignore_product_ids) {
                return $p->mutationable->product_id;
            })
            ->take($limit)
            ->map(function($p) {
                return $p->mutationable->product;
            });
    }

    /**
     * Get a list of economy members that are most likely to buy new products.
     * This is shown in the advanced product buying page.
     *
     * @param Bar $bar The bar to get a list of users for.
     * @parma int $limit The limit of users to return, might be less.
     * @param int[] [$ignore_user_ids] List of user IDs to ignore.
     *
     * @return object A list of members.
     */
    // TODO: return list of top members instead of finding them for transactions
    private static function getMemberList(Bar $bar, $limit, $ignore_user_ids = []) {
        // Return nothing if the limit is too low
        if($limit <= 0)
            return collect();

        // Get most common members
        $members = self::getTopMemberList($bar, self::MEMBERS_TOP_LIMIT);

        // Add recent members
        // TODO: pluck economy_id instead?
        $members = $members->concat(
            self::getRecentMemberList($bar, self::MEMBERS_RECENT_LIMIT, $members->pluck('user_id'))
        );

        // TODO: extend list with random members if not LIST_LIMIT yet

        return $members;
    }

    /**
     * Get a list of members that recently bought products.
     *
     * @param Bar $bar The bar to get a list of users for.
     * @parma int $limit The limit of users to return, might be less.
     * @param int[] [$ignore_user_ids] List of user IDs to ignore.
     *
     * @return object A list of members.
     */
    private static function getRecentMemberList(Bar $bar, $limit, $ignore_user_ids = []) {
        // Return nothing if the limit is too low
        if($limit <= 0)
            return collect();

        // List last 100 transactions to build recent user list
        $transactions = $bar
            ->transactions()
            ->whereNotNull('mutation.owner_id')
            ->whereNotIn('mutation.owner_id', $ignore_user_ids)
            ->latest('mutation.updated_at')
            ->limit(100)
            ->get(['mutation.owner_id', 'mutation.updated_at']);

        // Get user IDs for recent transactions
        // TODO: do unique query on database
        $user_ids = $transactions
            ->pluck('owner_id')
            ->unique()
            ->values()
            ->take($limit);

        // Fetch and return the members for these users
        $econ_members = $bar
            ->economy
            ->members()
            ->whereIn('user_id', $user_ids)
            ->limit($limit)
            ->get();
        return $user_ids
            ->map(function($user_id) use($econ_members) {
                return $econ_members->firstWhere('user_id', $user_id);
            })
            ->filter(function($member) {
                return $member != null;
            });
    }

    /**
     * Get a list of members that commonly buy products.
     *
     * @param Bar $bar The bar to get a list of users for.
     * @parma int $limit The limit of users to return, might be less.
     *
     * @return object A list of members.
     */
    private static function getTopMemberList(Bar $bar, $limit) {
        // Return nothing if the limit is too low
        if($limit <= 0)
            return collect();

        // List recent transactions
        $query = $bar
            ->transactions()
            ->latest('mutation.updated_at')
            ->whereNotNull('mutation.owner_id');

        // Fetch transaction details for last 100 relevant transactions
        $transactions = $query
            ->limit(100)
            ->get(['mutation.owner_id', 'mutation_product.quantity', 'mutation.updated_at']);

        // List user IDs sorted by most bought
        $user_ids = $transactions
            ->reduce(function($list, $item) {
                $key = strval($item->owner_id);
                if(isset($list[$key]))
                    $list[$key] += $item->quantity;
                else
                    $list[$key] = $item->quantity;
                return $list;
            }, collect())
            ->sort()
            ->reverse()
            ->take($limit)
            ->keys();

        // Fetch and return the members for these users
        $econ_members = $bar
            ->economy
            ->members()
            ->whereIn('user_id', $user_ids)
            ->limit($limit)
            ->get();
        return $user_ids
            ->map(function($user_id) use($econ_members) {
                return $econ_members->firstWhere('user_id', $user_id);
            })
            ->filter(function($member) {
                return $member != null;
            });
    }

    /**
     * API route for buying products in the users advanced buying cart.
     *
     * @return Response
     */
    public function apiBuy(Request $request) {
        // Get the bar, current user and the search query
        $bar = kioskauth()->getBar();
        $economy = $bar->economy;
        $cart = collect($request->post());
        $self = $this;

        // Do everything in a database transaction
        $productCount = 0;
        $userCount = $cart->count();
        DB::transaction(function() use($bar, $economy, $cart, $self, &$productCount) {
            // For each user, purchase the selected products
            $cart->each(function($userItem) use($bar, $economy, $self, &$productCount) {
                $user = $userItem['user'];
                $products = collect($userItem['products']);

                // Retrieve user and product models from database
                $member = $economy->members()->findOrFail($user['id']);
                $products = $products->map(function($product) use($economy) {
                    $product['product'] = $economy->products()->findOrFail($product['product']['id']);
                    return $product;
                });

                // Buy the products, increase product count
                $result = $self->buyProducts($bar, $member, $products);
                $productCount += $result['productCount'];
            });
        });

        // Return some useful stats
        return [
            'productCount' => $productCount,
            'userCount' => $userCount,
        ];
    }

    /**
     * Buy the given list of products for the given user.
     *
     * @param Bar $bar The bar to buy the products in.
     * @param EconomyMember $economy_member The economy member to buy the products for.
     * @param array $products [[quantity: int, product: Product]] List of
     *      products and quantities to buy.
     */
    // TODO: support paying in multiple currencies for different products at the same time
    // TODO: make a request when paying for other users
    function buyProducts(Bar $bar, EconomyMember $economy_member, $products) {
        $products = collect($products);

        // Build a list of preferred currencies for the member, filter currencies
        // with no price
        // TODO: replace this extern invocation
        $currencies = BarController::userCurrencies($bar, $economy_member)
            ->filter(function($currency) use($products) {
                $product = $products[0]['product'];
                return $product->prices->contains('currency_id', $currency->id);
            });
        if($currencies->isEmpty())
            throw new \Exception("Could not quick buy product, no supported currencies");
        $currency_ids = $currencies->pluck('id');

        // Get or create a wallet for the economy member, get the price
        $wallet = $economy_member->getOrCreateWallet($currencies);
        $currency = $wallet->currency;

        // Select the price for each product, find the total price
        $products = $products->map(function($item) use($wallet, $currency) {
            // The quantity must be 1 or more
            if($item['quantity'] < 1)
                throw new \Exception('Cannot buy product with quantity < 1');

            // Select price for this product
            $price = $item['product']
                ->prices
                ->whereStrict('currency_id', $currency->id)
                ->first()
                ->price;
            if($price == null)
                throw new \Exception('Product does not have price in selected currency');
            $item['priceEach'] = $price * 1;
            $item['priceTotal'] = $price * $item['quantity'];

            return $item;
        });
        $price = $products->sum('priceTotal');

        // TODO: notify user if wallet is created?

        // Get the user ID
        $user_id = $economy_member->user_id;

        // Start a database transaction for the product transaction
        // TODO: create a nice generic builder for the actions below
        $out = null;
        $productCount = 0;
        DB::transaction(function() use($bar, $products, $user_id, $wallet, $currency, $price, &$out, &$productCount) {
            // TODO: last_transaction is used here but never defined

            // Create the transaction or use last transaction
            $transaction = $last_transaction ?? Transaction::create([
                'state' => Transaction::STATE_SUCCESS,
                'owner_id' => $user_id,
                'initiated_by_id' => null,
                'initiated_by_other' => true,
                'initiated_by_kiosk' => true,
            ]);

            // Determine whether the product was free
            $free = $price == 0;

            // Create the wallet mutation unless product is free
            $mut_wallet = null;
            if(!$free) {
                // Create a new wallet mutation or update the existing
                $mut_wallet = $transaction
                    ->mutations()
                    ->create([
                        'economy_id' => $bar->economy_id,
                        'mutationable_id' => 0,
                        'mutationable_type' => '',
                        'amount' => $price,
                        'currency_id' => $currency->id,
                        'state' => Mutation::STATE_SUCCESS,
                        'owner_id' => $user_id,
                    ]);
                $mut_wallet->setMutationable(
                    MutationWallet::create([
                        'wallet_id' => $wallet->id,
                    ])
                );
            }

            // Create a product mutation for each product type
            $products->each(function($product) use($transaction, $bar, $currency, $user_id, $mut_wallet, &$productCount) {
                // Get the quantity for this product, increase product count
                $quantity = $product['quantity'];
                $productCount += $quantity;

                // Create the product mutation
                $mut_product = $transaction
                    ->mutations()
                    ->create([
                        'economy_id' => $bar->economy_id,
                        'mutationable_id' => 0,
                        'mutationable_type' => '',
                        'amount' => -$product['priceTotal'],
                        'currency_id' => $currency->id,
                        'state' => Mutation::STATE_SUCCESS,
                        'owner_id' => $user_id,
                        'depend_on' => $mut_wallet != null ? $mut_wallet->id : null,
                    ]);
                $mut_product->setMutationable(
                    MutationProduct::create([
                        'product_id' => $product['product']->id,
                        'bar_id' => $bar->id,
                        'quantity' => $quantity,
                    ])
                );
            });

            // Update the wallet balance
            // TODO: do this by setting the mutation states instead
            if(!$free)
                $wallet->withdraw($price);

            // Return the transaction
            $out = $transaction;
        });

        // Return the transaction details
        return [
            'transaction' => $out,
            'productCount' => $productCount,
            'currency' => $currency,
            'price' => $price,
        ];
    }
}
