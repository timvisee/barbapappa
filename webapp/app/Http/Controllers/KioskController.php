<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use App\Models\Currency;
use App\Models\EconomyMember;
use App\Models\Mutation;
use App\Models\MutationProduct;
use App\Models\MutationWallet;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class KioskController extends Controller {

    /**
     * The limit for the member list.
     */
    const MEMBERS_LIMIT = 15;

    /**
     * Limit of members to show that are common buyers.
     */
    const MEMBERS_TOP_LIMIT = 5;

    /**
     * Limit of members to show that are recent buyers.
     */
    const MEMBERS_RECENT_LIMIT = 10;

    /**
     * The limit for the products list.
     */
    const PRODUCTS_LIMIT = 13;

    /**
     * Limit of products to show that are commonly bought
     */
    const PRODUCTS_TOP_LIMIT = 9;

    /**
     * Limit of products to show that were recently bought.
     */
    const PRODUCT_RECENT_LIMIT = 4;

    /**
     * The maximum age of the 'initiated_at' field of a transaction in seconds.
     *
     * If the 'initiated_at' value is more seconds ago than specified here, the
     * field is cleared because then it is assumed to be malformed.
     */
    const TRANSACTION_INITIATED_AT_MAX_AGE = 2 * 30 * 24 * 60 * 60;

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
     * Kiosk history page.
     *
     * @return Response
     */
    public function history() {
        // Get the bar and session user
        $bar = kioskauth()->getBar();

        // List the last product mutations
        $productMutations = $bar
            ->productMutations()
            ->latest()
            ->where('created_at', '>', now()->subSeconds(config('bar.bar_recent_product_transaction_period')))
            ->get();

        // Show the kiosk join page
        return view('kiosk.history')
            ->with('bar', $bar)
            ->with('productMutations', $productMutations);
    }

    /**
     * Kiosk join page.
     *
     * @return Response
     */
    public function join() {
        // Get the bar and session user
        $bar = kioskauth()->getBar();

        // Build QR-code URL
        $qrData = ['barId' => $bar->human_id];
        if($bar->password != null)
            $qrData['code'] = $bar->password;
        $qrUrl = route('bar.join', $qrData);

        // Show the kiosk join page
        return view('kiosk.join')
            ->with('bar', $bar)
            ->with('qr_url', $qrUrl);
    }

    /**
     * API route for listing economy members in this bar.
     *
     * @return Response
     */
    public function apiMembers() {
        // Get the bar, current user and the search query
        $bar = kioskauth()->getBar();
        $economy = $bar->economy;
        $search = \Request::query('q');
        $all = is_checked(\Request::query('all'));

        // Determine whether to search or whether to show default member list
        $doSearch = $all || !empty($search);

        // Return a default user list, or search based on a given query
        if(!$doSearch)
            $members = self::getMemberList($bar, self::MEMBERS_LIMIT);
        else {
            $members = $economy
                ->members()
                ->search($search)
                ->showInKiosk()
                ->limit(!$all ? self::MEMBERS_LIMIT : null)
                ->get();
        }

        // Set and limit fields to repsond with and sort
        $members = $members
            ->map(function($m) {
                $m->name = $m->name;
                $data = $m->only(['id', 'name']);
                $data['registered'] = $m->user_id != null && $m->user_id > 0;
                return $data;
            })
            ->sortBy([['registered', 'desc'], 'name'], SORT_NATURAL | SORT_FLAG_CASE)
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
        // Get the bar and the search query
        $bar = kioskauth()->getBar();
        $economy = $bar->economy;
        $search = \Request::get('q');
        $all = is_checked(\Request::query('all'));

        // Select currency
        // TODO: find proper currency here, possibly show selection for it
        // $currency = $economy->currencies()->firstOrFail();
        $currency_id = $economy
            ->mutations()
            ->latest()
            ->limit(100)
            ->pluck('currency_id')
            ->countBy()
            ->sortDesc()
            ->keys()
            ->first();
        $currency = Currency::findOrFail($currency_id);
        $currencies = [$currency];

        // Determine whether to search or whether to show default member list
        $doSearch = $all || !empty($search);

        // Search, or use top products
        if($doSearch)
            $products = $bar->economy->searchProducts($search, [$currency->id]);
        else
            $products = self::getProductList($bar, self::PRODUCTS_LIMIT, [$currency->id]);

        // Add formatted price fields
        $products = $products
            ->map(function($product) use($currencies) {
                $product->price_display = $product->formatPrice($currencies);
                return $product;
            });

        // Separate top products from list
        if($doSearch) {
            $top = collect();
            $list = $products;
        } else {
            $top = $products->take(5);
            $list = $products->skip(5);
        }

        return [
            'top' => $top->values() ?? [],
            'list' => $list->sortBy(['exhausted', 'name'], SORT_NATURAL | SORT_FLAG_CASE)->values(),
        ];
    }

    /**
     * Create a list of products personalized for the authenticated in user by
     * estimating their preference, based on buy history inside this economy.
     *
     * This list includes the most bought products, the most recently bought
     * products and may be topped up with random products to reach the desired
     * limit. See PRODUCTS_TOP_LIMIT and PRODUCT_RECENT_LIMIT.
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
        $products = self::getTopProductList($bar, self::PRODUCTS_TOP_LIMIT, $currency_ids, true);

        // Add recent products
        $products = $products->concat(
            self::getRecentProductList($bar, self::PRODUCT_RECENT_LIMIT, $currency_ids, $products->pluck('id'), true)
        );

        // Add random products
        if($products->count() < self::PRODUCTS_LIMIT)
            $products = $products->concat(
                self::getRandomProductList($bar, self::PRODUCTS_LIMIT - $products->count(), $currency_ids, $products->pluck('id'))
            );

        return $products->take($limit);
    }

    /**
     * Get a list of top bought products.
     *
     * @param Bar $bar The bar to get products for.
     * @param int $limit Product limit.
     * @param [int]|null $currency_ids A list of Currency IDs returned
     *      products must have a price configured in in at least one of them.
     * @param bool $noExhausted Whether to exclude exhausted products.
     *
     * @return object A list of products.
     */
    private static function getTopProductList(Bar $bar, $limit, $currency_ids, $noExhausted) {
        // Get facts
        $economy = $bar->economy;

        // List latest product mutations
        $product_mutations = $economy
            ->mutations()
            ->select('id', 'mutationable_type', 'mutationable_id')
            ->where('mutationable_type', MutationProduct::class)
            ->whereIn('currency_id', $currency_ids)
            ->latest()
            ->limit(100)
            ->with('mutationable')
            ->get();

        // Exclude exhausted
        // TODO: this is inefficient, do this on query level
        if($noExhausted)
            $product_mutations = $product_mutations
                ->filter(function($p) {
                    return !($p->mutationable->product?->exhausted ?? false);
                });

        // List product IDs sorted by most bought
        $product_ids = $product_mutations
            ->reduce(function($list, $item) {
                $key = strval($item->mutationable->product_id);
                $quantity = sqrt($item->mutationable->quantity);
                if(isset($list[$key]))
                    $list[$key] += $quantity;
                else
                    $list[$key] = $quantity;
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
     * @param bool $noExhausted Whether to exclude exhausted products.
     *
     * @return object A list of products.
     */
    private static function getRecentProductList(Bar $bar, $limit, $currency_ids, $ignore_product_ids = [], $noExhausted) {
        // Get facts
        $economy = $bar->economy;
        $ignore_product_ids = collect($ignore_product_ids);

        // List latest product mutations
        $product_mutations = $economy
            ->mutations()
            ->select('id', 'mutationable_type', 'mutationable_id')
            ->where('mutationable_type', MutationProduct::class)
            ->whereIn('currency_id', $currency_ids)
            ->whereHasMorph(
                'mutationable',
                MutationProduct::class,
                function(Builder $query) use($ignore_product_ids) {
                    $query
                        ->whereNotNull('product_id')
                        ->whereNotIn('product_id', $ignore_product_ids);
                }
            )
            ->latest()
            ->limit(100)
            ->with('mutationable')
            ->get();

        // Exclude exhausted
        // TODO: this is inefficient, do this on query level
        if($noExhausted)
            $product_mutations = $product_mutations
                ->filter(function($p) {
                    return !($p->mutationable->product?->exhausted ?? false);
                });

        // Take limit of recent products, skip null or ignored products
        return $product_mutations
            ->unique(function($p) {
                return $p->mutationable->product_id;
            })
            ->take($limit)
            ->map(function($p) {
                return $p->mutationable->product;
            })
            ->whereNotNull();
    }

    /**
     * Get a list of random products.
     * What products are returned is undefined other than the constraints that
     * are given as parameter.
     *
     * @param Bar $bar The bar to get products for.
     * @param int $limit Product limit.
     * @param [int]|null $currency_ids A list of Currency IDs returned
     *      products must have a price configured in in at least one of them.
     * @param [int]|null $ignore_product_ids A list of product IDs to ignore.
     *
     * @return object A list of products.
     */
    private static function getRandomProductList(Bar $bar, $limit, $currency_ids, $ignore_product_ids = []) {
        return $bar
            ->economy
            ->products()
            ->whereNotIn('id', $ignore_product_ids)
            ->whereHas('prices', function($query) use($currency_ids) {
                return $query->whereIn('currency_id', $currency_ids);
            })
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get a list of economy members that are most likely to buy new products.
     * This is shown in the advanced product buying page.
     *
     * This list includes members that buy the most products, members that most
     * recently bought a product and may be topped up with random members to
     * reach the desired limit. See MEMBERS_TOP_LIMIT and MEMBERS_RECENT_LIMIT.
     *
     * @param Bar $bar The bar to get a list of users for.
     * @parma int $limit The limit of users to return, might be less.
     * @param int[] [$ignore_user_ids] List of user IDs to ignore.
     *
     * @return object A list of members.
     */
    private static function getMemberList(Bar $bar, $limit, $ignore_user_ids = []) {
        // Return nothing if the limit is too low
        if($limit <= 0)
            return collect();

        // Get most common members
        $members = self::getTopMemberList($bar, self::MEMBERS_TOP_LIMIT);

        // Add recent members
        // TODO: use economy member id instead of user id
        $members = $members->concat(
            self::getRecentMemberList($bar, self::MEMBERS_RECENT_LIMIT, $members->pluck('user_id'))
        );

        // Add random members
        if($members->count() < self::MEMBERS_LIMIT)
            $members = $members->concat(
                self::getRandomMemberList($bar, self::MEMBERS_LIMIT - $members->count(), $members->pluck('user_id'))
            );

        return $members->take($limit);
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
            ->showInKiosk()
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
            ->showInKiosk()
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
     * Get a list of random members.
     * What members are returned is undefined other than the constraints that
     * are given as parameter.
     *
     * @param Bar $bar The bar to get members for.
     * @param int $limit Member limit.
     * @param int[] [$ignore_user_ids] List of user IDs to ignore.
     *
     * @return object A list of members.
     */
    private static function getRandomMemberList(Bar $bar, $limit, $ignore_user_ids = []) {
        return $bar
            ->economy
            ->members()
            ->showInKiosk()
            ->whereNotIn('user_id', $ignore_user_ids)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * API route for buying products in the users advanced buying cart.
     *
     * @return Response
     */
    // TODO: we must validate request data
    public function apiBuy(Request $request) {
        // Get the bar, current user and the search query
        $bar = kioskauth()->getBar();
        $economy = $bar->economy;
        $buyData = $request->post();
        $self = $this;

        // Error if bar is disabled
        if(!$bar->enabled) {
            return response()->json([
                'message' => __('pages.bar.disabled'),
            ])->setStatusCode(403);
        }

        // Take cart from request buy data
        if(isset($buyData['cart'])) {
            $cart = collect($buyData['cart']);
            $initiated_at_timestamp = $buyData['initiated_at'] ?? null;
        } else if(is_array($buyData)) {
            // Backwards compatability: client version <= 0.1.175
            $cart = collect($buyData);
            $initiated_at_timestamp = null;
        } else {
            throw new \Exception('Invalid buy data');
        }

        // Process initiated at timestamp
        $initiated_at = null;
        if($initiated_at_timestamp != null) {
            // Parse timestamp
            $initiated_at = Carbon::createFromTimestamp($initiated_at_timestamp);

            // Timestamp cannot be in the future
            if($initiated_at->isFuture())
                $initiated_at = now();

            // Clear initiated at timestamp if it is too long ago
            if($initiated_at->diffInSeconds() >= Self::TRANSACTION_INITIATED_AT_MAX_AGE)
                $initiated_at = null;
        }

        // Do everything in a database transaction
        $productCount = 0;
        $userCount = $cart->count();
        DB::transaction(function() use($bar, $economy, $cart, $initiated_at, $self, &$productCount) {
            // For each user, purchase the selected products
            $cart->each(function($userItem) use($bar, $economy, $initiated_at, $self, &$productCount) {
                $user = $userItem['user'];
                $products = collect($userItem['products']);

                // Retrieve user and product models from database
                $member = $economy->members()->showInKiosk()->findOrFail($user['id']);
                $products = $products->map(function($product) use($economy) {
                    $product['product'] = $economy->products()->findOrFail($product['product']['id']);
                    return $product;
                });

                // Buy the products, increase product count
                $result = $self->buyProducts($bar, $member, $products, $initiated_at);
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
     * @param Carbon|null $initiated_at Time at which the transaction was
     *      initiated.
     */
    // TODO: support paying in multiple currencies for different products at the same time
    // TODO: make a request when paying for other users
    function buyProducts(Bar $bar, EconomyMember $economy_member, $products, ?Carbon $initiated_at) {
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
                ->price
                ?? null;
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
        DB::transaction(function() use($bar, $products, $user_id, $wallet, $currency, $price, $initiated_at, &$out, &$productCount) {
            // TODO: last_transaction is used here but never defined

            // Create the transaction or use last transaction
            $transaction = $last_transaction ?? Transaction::create([
                'state' => Transaction::STATE_SUCCESS,
                'owner_id' => $user_id,
                'initiated_by_id' => null,
                'initiated_by_other' => true,
                'initiated_by_kiosk' => true,
                'initiated_at' => $initiated_at,
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
                        'depend_on' => $mut_wallet?->id,
                    ]);
                $mut_product->setMutationable(
                    MutationProduct::create([
                        'product_id' => $product['product']->id,
                        'bar_id' => $bar->id,
                        'quantity' => $quantity,
                    ])
                );

                // Update bar inventory
                if($bar->inventory != null) {
                    $product['product']->subtractFromInventory(
                        $bar->inventory,
                        $mut_product->mutationable->quantity,
                        $mut_product->mutationable,
                    );
                }
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
