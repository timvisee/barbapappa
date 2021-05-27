<?php

namespace App\Http\Controllers;

use App\Models\Bar;
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

    // /**
    //  * API route for listing products in this bar, that a user can buy.
    //  *
    //  * // TODO: limit product fields returned here
    //  *
    //  * @return Response
    //  */
    // public function apiBuyProducts($barId) {
    //     // Get the bar, current user and the search query
    //     $bar = \Request::get('bar');
    //     $user = barauth()->getSessionUser();

    //     // Build a list of preferred currencies for the user
    //     // TODO: if there's only one currency, that is usable, use null to
    //     //       greatly simplify product queries
    //     $currencies = Self::userCurrencies($bar, $user);
    //     $currency_ids = $currencies->pluck('id');

    //     // Search, or use top products
    //     $search = \Request::get('q');
    //     if(!empty($search))
    //         $products = $bar->economy->searchProducts($search, $currency_ids);
    //     else
    //         $products = $bar->economy->quickBuyProducts($currency_ids);

    //     // Add formatted price fields
    //     $products = $products->map(function($product) use($currencies) {
    //         $product->price_display = $product->formatPrice($currencies);
    //         return $product;
    //     });

    //     return $products;
    // }

    // /**
    //  * API route for listing economy members in this bar, products can be bought for.
    //  *
    //  * // TODO: limit member fields returned here
    //  *
    //  * @return Response
    //  */
    // public function apiBuyMembers($barId) {
    //     // Get the bar, current user and the search query
    //     $bar = \Request::get('bar');
    //     $user = barauth()->getSessionUser();
    //     $economy = $bar->economy;
    //     $economy_member = $economy->members()->user($user)->firstOrFail();
    //     $search = \Request::query('q');
    //     $product_ids = json_decode(\Request::query('product_ids'));

    //     // Return a default user list, or search based on a given query
    //     if(empty($search)) {
    //         // Add the current member
    //         $members = collect([$economy_member]);

    //         // Build a list of members most likely to buy new products
    //         // Specifically for selected products first, then fill gor any
    //         $limit = 7;
    //         if(!empty($product_ids))
    //             $members = $members->merge($this->getProductBuyMemberList(
    //                 $bar,
    //                 5,
    //                 [$user->id],
    //                 $product_ids
    //             ));
    //         $members = $members->merge($this->getProductBuyMemberList(
    //             $bar,
    //             $limit - $members->count(),
    //             $members->pluck('user_id')
    //         ));
    //     } else
    //         $members = $economy->members()->search($search)->get();

    //     // Always appent current user to list if not included
    //     $hasCurrent = $members->contains(function($m) use($economy_member) {
    //         return $m->id == $economy_member->id;
    //     });
    //     if(!$hasCurrent)
    //         $members[] = $economy_member;

    //     // Set and limit fields to repsond with
    //     $members = $members->map(function($m) use($economy_member) {
    //         $m->name = $m->name;
    //         $m->me = $m->id == $economy_member->id;
    //         return $m->only(['id', 'name', 'me']);
    //     });

    //     return $members;
    // }

    // /**
    //  * Get a list of economy members that are most likely to buy new products.
    //  * This is shown in the advanced product buying page.
    //  *
    //  * A list of product IDs may be given to limit the most lickly buy hunting
    //  * to just those products.
    //  *
    //  * @param Bar $bar The bar to get a list of users for.
    //  * @parma int $limit The limit of users to return, might be less.
    //  * @param int[]|null [$ignore_user_ids] List of user IDs to ignore.
    //  * @param int[]|null [$product_ids] List of product IDs to prefer.
    //  *
    //  * @return EconomyMember[]
    //  */
    // private function getProductBuyMemberList(Bar $bar, $limit, $ignore_user_ids = null, $product_ids = null) {
    //     // Return nothing if the limit is too low
    //     if($limit <= 0)
    //         return [];

    //     // Find other users that recently made a transaction with these products
    //     $query = $bar
    //         ->transactions()
    //         ->latest('mutation.updated_at')
    //         ->whereNotIn('mutation.owner_id', $ignore_user_ids);

    //     // Limit to specific product IDs
    //     if(!empty($product_ids))
    //         $query = $query->whereIn('mutation_product.product_id', $product_ids);

    //     // Fetch transaction details for last 100 relevant transactions
    //     $transactions = $query
    //         ->limit(100)
    //         ->get(['mutation.owner_id', 'mutation_product.quantity']);

    //     // List user IDs sorted by most bought
    //     $user_ids = $transactions
    //         ->reduce(function($list, $item) {
    //             $key = strval($item->owner_id);
    //             if(isset($list[$key]))
    //                 $list[$key] += $item->quantity;
    //             else
    //                 $list[$key] = $item->quantity;
    //             return $list;
    //         }, collect())
    //         ->sort()
    //         ->reverse()
    //         ->take($limit)
    //         ->keys();

    //     // Fetch and return the members for these users
    //     $econ_members = $bar
    //         ->economy
    //         ->members()
    //         ->whereIn('user_id', $user_ids)
    //         ->limit($limit)
    //         ->get();
    //     return $user_ids
    //         ->map(function($user_id) use($econ_members) {
    //             return $econ_members->firstWhere('user_id', $user_id);
    //         })
    //         ->filter(function($member) {
    //             return $member != null;
    //         });
    // }

    // /**
    //  * API route for buying products in the users advanced buying cart.
    //  *
    //  * @return Response
    //  */
    // public function apiBuyBuy(Request $request) {
    //     // Get the bar, current user and the search query
    //     $bar = \Request::get('bar');
    //     $economy = $bar->economy;
    //     $cart = collect($request->post());
    //     $self = $this;

    //     // Do everything in a database transaction
    //     $productCount = 0;
    //     $userCount = $cart->count();
    //     DB::transaction(function() use($bar, $economy, $cart, $self, &$productCount) {
    //         // For each user, purchase the selected products
    //         $cart->each(function($userItem) use($bar, $economy, $self, &$productCount) {
    //             $user = $userItem['user'];
    //             $products = collect($userItem['products']);

    //             // Retrieve user and product models from database
    //             $member = $economy->members()->findOrFail($user['id']);
    //             $products = $products->map(function($product) use($economy) {
    //                 $product['product'] = $economy->products()->findOrFail($product['product']['id']);
    //                 return $product;
    //             });

    //             // Buy the products, increase product count
    //             $result = $self->buyProducts($bar, $member, $products);
    //             $productCount += $result['productCount'];
    //         });
    //     });

    //     // Return some useful stats
    //     return [
    //         'productCount' => $productCount,
    //         'userCount' => $userCount,
    //     ];
    // }

    // /**
    //  * Buy the given list of products for the given user.
    //  *
    //  * @param Bar $bar The bar to buy the products in.
    //  * @param EconomyMember $economy_member The economy member to buy the products for.
    //  * @param array $products [[quantity: int, product: Product]] List of
    //  *      products and quantities to buy.
    //  */
    // // TODO: support paying in multiple currencies for different products at the same time
    // // TODO: make a request when paying for other users
    // function buyProducts(Bar $bar, EconomyMember $economy_member, $products) {
    //     $products = collect($products);

    //     // Build a list of preferred currencies for the member, filter currencies
    //     // with no price
    //     $currencies = Self::userCurrencies($bar, $economy_member)
    //         ->filter(function($currency) use($products) {
    //             $product = $products[0]['product'];
    //             return $product->prices->contains('currency_id', $currency->id);
    //         });
    //     if($currencies->isEmpty())
    //         throw new \Exception("Could not quick buy product, no supported currencies");
    //     $currency_ids = $currencies->pluck('id');

    //     // Get or create a wallet for the economy member, get the price
    //     $wallet = $economy_member->getOrCreateWallet($currencies);
    //     $currency = $wallet->currency;

    //     // Select the price for each product, find the total price
    //     $products = $products->map(function($item) use($wallet, $currency) {
    //         // The quantity must be 1 or more
    //         if($item['quantity'] < 1)
    //             throw new \Exception('Cannot buy product with quantity < 1');

    //         // Select price for this product
    //         $price = $item['product']
    //             ->prices
    //             ->whereStrict('currency_id', $currency->id)
    //             ->first()
    //             ->price;
    //         if($price == null)
    //             throw new \Exception('Product does not have price in selected currency');
    //         $item['priceEach'] = $price * 1;
    //         $item['priceTotal'] = $price * $item['quantity'];

    //         return $item;
    //     });
    //     $price = $products->sum('priceTotal');

    //     // TODO: notify user if wallet is created?

    //     // Get the user ID
    //     $user_id = $economy_member->user_id;

    //     // Determine whether to set different initiating user
    //     $initiated_by_id = null;
    //     $initiated_by_other = $user_id != barauth()->getUser()->id;
    //     if($initiated_by_other)
    //         $initiated_by_id = barauth()->getUser()->id;

    //     // Start a database transaction for the product transaction
    //     // TODO: create a nice generic builder for the actions below
    //     $out = null;
    //     $productCount = 0;
    //     DB::transaction(function() use($bar, $products, $user_id, $wallet, $currency, $price, &$out, &$productCount, $initiated_by_id, $initiated_by_other) {
    //         // TODO: last_transaction is used here but never defined

    //         // Create the transaction or use last transaction
    //         $transaction = $last_transaction ?? Transaction::create([
    //             'state' => Transaction::STATE_SUCCESS,
    //             'owner_id' => $user_id,
    //             'initiated_by_id' => $initiated_by_id,
    //             'initiated_by_other' => $initiated_by_other,
    //         ]);

    //         // Determine whether the product was free
    //         $free = $price == 0;

    //         // Create the wallet mutation unless product is free
    //         $mut_wallet = null;
    //         if(!$free) {
    //             // Create a new wallet mutation or update the existing
    //             $mut_wallet = $transaction
    //                 ->mutations()
    //                 ->create([
    //                     'economy_id' => $bar->economy_id,
    //                     'mutationable_id' => 0,
    //                     'mutationable_type' => '',
    //                     'amount' => $price,
    //                     'currency_id' => $currency->id,
    //                     'state' => Mutation::STATE_SUCCESS,
    //                     'owner_id' => $user_id,
    //                 ]);
    //             $mut_wallet->setMutationable(
    //                 MutationWallet::create([
    //                     'wallet_id' => $wallet->id,
    //                 ])
    //             );
    //         }

    //         // Create a product mutation for each product type
    //         $products->each(function($product) use($transaction, $bar, $currency, $user_id, $mut_wallet, &$productCount) {
    //             // Get the quantity for this product, increase product count
    //             $quantity = $product['quantity'];
    //             $productCount += $quantity;

    //             // Create the product mutation
    //             $mut_product = $transaction
    //                 ->mutations()
    //                 ->create([
    //                     'economy_id' => $bar->economy_id,
    //                     'mutationable_id' => 0,
    //                     'mutationable_type' => '',
    //                     'amount' => -$product['priceTotal'],
    //                     'currency_id' => $currency->id,
    //                     'state' => Mutation::STATE_SUCCESS,
    //                     'owner_id' => $user_id,
    //                     'depend_on' => $mut_wallet != null ? $mut_wallet->id : null,
    //                 ]);
    //             $mut_product->setMutationable(
    //                 MutationProduct::create([
    //                     'product_id' => $product['product']->id,
    //                     'bar_id' => $bar->id,
    //                     'quantity' => $quantity,
    //                 ])
    //             );
    //         });

    //         // Update the wallet balance
    //         // TODO: do this by setting the mutation states instead
    //         if(!$free)
    //             $wallet->withdraw($price);

    //         // Return the transaction
    //         $out = $transaction;
    //     });

    //     // Return the transaction details
    //     return [
    //         'transaction' => $out,
    //         'productCount' => $productCount,
    //         'currency' => $currency,
    //         'price' => $price,
    //     ];
    // }
}
