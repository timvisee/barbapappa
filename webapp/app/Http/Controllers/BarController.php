<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\Bar;
use App\Models\Mutation;
use App\Models\MutationProduct;
use App\Models\MutationWallet;
use App\Models\Transaction;
use App\Perms\BarRoles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Validator;

class BarController extends Controller {

    /**
     * Bar overview page.
     *
     * @return Response
     */
    public function overview() {
        return view('bar.overview')
            ->with('bars', Bar::visible()->get());
    }

    /**
     * Bar creation page.
     *
     * @return Response
     */
    public function create() {
        // Get the community
        $community = \Request::get('community');

        // An economy must be created first if not available
        if($community->economies()->count() == 0) {
            // TODO: redirect the user back to this page after economy creation
            return redirect()
                ->route('community.economy.create', ['communityId' => $community->id])
                ->with('error', __('pages.bar.mustCreateEconomyFirst'));
        }

        return view('bar.create');
    }

    /**
     * Bar create page.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doCreate(Request $request) {
        // Get the community
        $community = \Request::get('community');

        // Validate
        $this->validate($request, [
            'economy' => ['required', ValidationDefaults::communityEconomy($community)],
            'name' => 'required|' . ValidationDefaults::NAME,
            'slug' => 'nullable|' . ValidationDefaults::barSlug(),
            'password' => 'nullable|' . ValidationDefaults::SIMPLE_PASSWORD,
        ], [
            'slug.regex' => __('pages.bar.slugFieldRegexError'),
        ]);

        // Create the bar
        $bar = $community->bars()->create([
            'economy_id' => $request->input('economy'),
            'name' => $request->input('name'),
            'slug' => $request->has('slug') ? $request->input('slug') : null,
            'password' => $request->has('password') ? $request->input('password') : null,
            'visible' => is_checked($request->input('visible')),
            'public' => is_checked($request->input('public')),
        ]);

        // Automatically join if checked
        if(is_checked($request->input('join')))
            $bar->join(barauth()->getUser());

        // Redirect the user to the account overview page
        return redirect()
            ->route('bar.show', ['barId' => $bar->human_id])
            ->with('success', __('pages.bar.created'));
    }

    /**
     * Bar show page.
     *
     * @return Response
     */
    public function show($barId) {
        // Show info page if user does not have user role
        if(!perms(Self::permsUser()))
            return $this->info($barId);

        // Get the bar and session user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Update the visit time for this member
        $member = $bar->users(['visited_at'], false)
            ->where('user_id', $user->id)
            ->first();
        if($member != null) {
            $member->pivot->visited_at = new \DateTime();
            $member->pivot->save();
        }

        // Build a list of preferred currencies for the user
        // TODO: if there's only one currency, that is usable, use null to
        //       greatly simplify product queries
        $currencies = Self::userCurrencies($bar, $user);
        $currency_ids = $currencies->pluck('id');

        // Search, or show top products
        $search = \Request::get('q');
        if(!empty($search))
            $products = $bar->economy->searchProducts($search, $currency_ids);
        else
            $products = $bar->economy->quickBuyProducts($currency_ids);

        // Show the bar page
        return view('bar.show')
            ->with('economy', $bar->economy)
            ->with('joined', $bar->isJoined($user))
            ->with('products', $products)
            ->with('currencies', $currencies);
    }

    /**
     * Bar info page.
     *
     * @return Response
     */
    public function info($barId) {
        // Get the bar and session user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Show the bar page
        return view('bar.info')
            ->with('economy', $bar->economy)
            ->with('page', last(explode('.', \Request::route()->getName())))
            ->with('joined', $bar->isJoined($user));
    }

    /**
     * Bar stats page.
     *
     * @return Response
     */
    public function stats($barId) {
        // Get the bar and session user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Gather some stats
        $memberCountHour = $bar
            ->users()
            ->wherePivot('visited_at', '>=', Carbon::now()->subHour())
            ->count();
        $memberCountDay = $bar
            ->users()
            ->wherePivot('visited_at', '>=', Carbon::now()->subDay())
            ->count();
        $memberCountMonth = $bar
            ->users()
            ->wherePivot('visited_at', '>=', Carbon::now()->subMonth())
            ->count();
        $productCount = $bar->economy->products()->count();
        // TODO: only count products with mutation having success state
        $soldProductCount = $bar->productMutations()->sum('quantity');
        $transactionCount = $bar->transactions()->count();
        $soldProductCountHour = $bar
            ->productMutations()
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->sum('quantity');
        $soldProductCountDay = $bar
            ->productMutations()
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->sum('quantity');
        $soldProductCountMonth = $bar
            ->productMutations()
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->sum('quantity');

        // Show the bar page
        return view('bar.stats')
            ->with('memberCountHour', $memberCountHour)
            ->with('memberCountDay', $memberCountDay)
            ->with('memberCountMonth', $memberCountMonth)
            ->with('productCount', $productCount)
            ->with('soldProductCount', $soldProductCount)
            ->with('transactionCount', $transactionCount)
            ->with('soldProductCountHour', $soldProductCountHour)
            ->with('soldProductCountDay', $soldProductCountDay)
            ->with('soldProductCountMonth', $soldProductCountMonth);
    }

    /**
     * Bar edit page.
     *
     * @return Response
     */
    public function edit() {
        return view('bar.edit');
    }

    /**
     * Bar update endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function update(Request $request) {
        // Get the community, bar and session user
        $community = \Request::get('community');
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Validate
        $this->validate($request, [
            'economy' => ['required', ValidationDefaults::communityEconomy($community)],
            'name' => 'required|' . ValidationDefaults::NAME,
            'slug' => 'nullable|' . ValidationDefaults::barSlug($bar),
            'password' => 'nullable|' . ValidationDefaults::SIMPLE_PASSWORD,
        ], [
            'slug.regex' => __('pages.bar.slugFieldRegexError'),
        ]);

        // Change the name properties
        $bar->economy_id = $request->input('economy');
        $bar->name = $request->input('name');
        $bar->slug = $request->has('slug') ? $request->input('slug') : null;
        $bar->password = $request->has('password') ? $request->input('password') : null;
        $bar->visible = is_checked($request->input('visible'));
        $bar->public = is_checked($request->input('public'));

        // Save the bar
        $bar->save();

        // Redirect the user to the account overview page
        return redirect()
            ->route('bar.show', ['barId' => $bar->human_id])
            ->with('success', __('pages.bar.updated'));
    }

    /**
     * The bar join confirmation page.
     *
     * @return Response
     */
    public function join($barId) {
        // Get the bar and user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Redirect to the bar page if the user has already joined
        if($bar->isJoined($user))
            return redirect()
                ->route('bar.show', ['barId' => $barId]);

        // Redirect to the bar page
        return view('bar.join');
    }

    /**
     * Make a user join the bar.
     *
     * @return Response
     */
    public function doJoin(Request $request, $barId) {
        // Get the bar, community and user
        $bar = \Request::get('bar');
        $community = \Request::get('community');
        $user = barauth()->getSessionUser();

        // Handle the password if required
        if($bar->needsPassword($user)) {
            // Validate password field input
            $this->validate($request, [
                'code' => 'required|' . ValidationDefaults::CODE,
            ]);

            // Test the password
            if(!$bar->isPassword($request->input('code'))) {
                // Mark the error and retur
                $validator = Validator::make([], []);
                $validator->errors()->add('code', __('pages.bar.incorrectCode'));
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // Join the community
        if(!$community->isJoined($user)) {
            // TODO: ensure the user has permission to join this community

            // Check whether to join their community
            $joinCommunity = is_checked($request->input('join_community'));

            // Join the community
            if($joinCommunity)
                $community->join($user);
        }

        // Join the user
        $bar->join($user);

        // Redirect to the bar page
        return redirect()
            ->route('bar.show', ['barId' => $barId])
            ->with('success', __('pages.bar.joinedThisBar'));
    }

    /**
     * The bar leave confirmation page.
     *
     * @return Response
     */
    public function leave($barId) {
        // TODO: make sure the user can leave this bar

        // Get the bar and user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Redirect to the bar page if the user isn't joined
        if(!$bar->isJoined($user))
            return redirect()
                ->route('bar.show', ['barId' => $barId]);

        // Redirect to the bar page
        return view('bar.leave');
    }

    /**
     * Make a user leave the bar.
     *
     * @return Response
     */
    public function doLeave($barId) {
        // TODO: make sure the user can leave the bar

        // Get the bar and user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Leave the user
        $bar->leave($user);

        // Redirect to the bar page
        return redirect()
            ->route('bar.show', ['barId' => $barId])
            ->with('success', __('pages.bar.leftThisBar'));
    }

    /**
     * Quick buy a product.
     *
     * @return Response
     */
    public function quickBuy($barId) {
        // Get the bar
        $bar = \Request::get('bar');
        $product = $bar->economy->products()->findOrFail(\Request::input('product_id'));

        // Quick buy the product, format the price
        $details = $this->quickBuyProduct($bar, $product);
        $transaction = $details['transaction'];
        $cost = balance($details['price'], $details['currency']->code);

        // Build a success message
        $msg = __('pages.bar.boughtProductForPrice', [
            'product' => $product->displayName(),
            'price' => $cost,
        ]) . '.';
        $msg .= ' <a href="' . route('transaction.undo', [
            'transactionId' => $transaction->id
        ]) . '">' . __('misc.undo') . '</a>';

        // Redirect back to the bar
        return redirect()
            ->route('bar.show', ['barId' => $bar->human_id])
            ->with('successHtml', $msg);
    }

    // TODO: describe
    // TODO: merges with recent product transactions
    // TODO: returns [transaction, currency, price]
    function quickBuyProduct(Bar $bar, $product) {
        // Get some parameters
        $user = barauth()->getUser();

        // Build a list of preferred currencies for the user, filter currencies
        // with no price
        $currencies = Self::userCurrencies($bar, $user)
            ->filter(function($currency) use($product) {
                return $product->prices->contains('currency_id', $currency->id);
            });
        if($currencies->isEmpty())
            throw new \Exception("Could not quick buy product, no supported currencies");
        $currency_ids = $currencies->pluck('id');

        // Find the most recent product transaction within the quick buy merge
        // time
        //
        // It must be:
        // - Recent, within configured time
        // - Owned by the current user
        // - Only contain (from) wallet and (to) product mutations
        // - Product mutations must be in the current bar
        $last_transaction = $user
            ->transactions()
            ->where('created_at', '>=', Carbon::now()->subSeconds(config('bar.quick_buy_merge_timeout')))
            ->whereNotExists(function($query) use($bar) {
                $query->selectRaw('1')
                    ->fromRaw('mutations')
                    ->leftJoin('mutations_product', 'mutations_product.mutation_id', '=', 'mutations.id')
                    ->whereRaw('mutations.transaction_id = transactions.id')
                    ->where(function($query) {
                        $query->where('type', '<>', Mutation::TYPE_WALLET)
                            ->orWhere('amount', '<=', 0);
                    })
                    ->where(function($query) use($bar) {
                        $query->where('type', '<>', Mutation::TYPE_PRODUCT)
                            ->orWhere('amount', '>', 0)
                            ->orWhere('mutations_product.bar_id', '<>', $bar->id);
                    });
            })
            ->latest()
            ->first();

        // Get or create a wallet for the user, get the price
        $wallet = $user->getOrCreateWallet($bar->economy, $currencies);
        $currency = $wallet->currency;
        $price = $product
            ->prices
            ->whereStrict('currency_id', $wallet->economyCurrency->id)
            ->first()
            ->price;

        // TODO: notify user if wallet is created?

        // Start a database transaction for the product transaction
        // TODO: create a nice generic builder for the actions below
        $out = null;
        DB::transaction(function() use($bar, $product, $user, $wallet, $currency, $price, $last_transaction, &$out) {
            // Create the transaction or use last transaction
            $transaction = $last_transaction ?? Transaction::create([
                'state' => Transaction::STATE_SUCCESS,
                'owner_id' => $user->id,
            ]);

            // Determine whether the product was free
            $free = $price == 0;

            // Create the wallet mutation unless product is free
            if(!$free) {
                // Find an mutation for the wallet in this transaction
                $mut_wallet = $last_transaction == null ? null : $transaction
                    ->mutations()
                    ->where('type', Mutation::TYPE_WALLET)
                    ->whereExists(function($query) use($wallet) {
                        $query->selectRaw('1')
                            ->from('mutations_wallet')
                            ->whereRaw('mutations.id = mutations_wallet.mutation_id')
                            ->where('wallet_id', $wallet->id);
                    })
                    ->first();

                // Create a new wallet mutation or update the existing
                if($mut_wallet == null) {
                    $mut_wallet = $transaction
                        ->mutations()
                        ->create([
                            'economy_id' => $bar->economy_id,
                            'type' => Mutation::TYPE_WALLET,
                            'amount' => $price,
                            'currency_id' => $currency->id,
                            'state' => Mutation::STATE_SUCCESS,
                            'owner_id' => $user->id,
                        ]);
                    MutationWallet::create([
                        'mutation_id' => $mut_wallet->id,
                        'wallet_id' => $wallet->id,
                    ]);
                } else
                    $mut_wallet->incrementAmount($price);
            }

            // Find an mutation for the product in this transaction
            $mut_product = $last_transaction == null ? null : $transaction
                ->mutations()
                ->where('type', Mutation::TYPE_PRODUCT)
                ->whereExists(function($query) use($product) {
                    $query->selectRaw('1')
                        ->from('mutations_product')
                        ->whereRaw('mutations.id = mutations_product.mutation_id')
                        ->where('product_id', $product->id);
                })
                ->first();

            // Create a new product mutation or update the existing one
            if($mut_product == null) {
                // Create the product mutation
                $mut_product = $transaction
                    ->mutations()
                    ->create([
                        'economy_id' => $bar->economy_id,
                        'type' => Mutation::TYPE_PRODUCT,
                        'amount' => -$price,
                        'currency_id' => $currency->id,
                        'state' => Mutation::STATE_SUCCESS,
                        'owner_id' => $user->id,
                    ]);
                MutationProduct::create([
                    'mutation_id' => $mut_product->id,
                    'product_id' => $product->id,
                    'bar_id' => $bar->id,
                    'quantity' => 1,
                ]);
            } else {
                $mut_product->decrementAmount($price);
                $mut_product->mutationData()->increment('quantity');
            }

            // Update the wallet balance
            if(!$free)
                $wallet->withdraw($price);

            // Return the transaction
            $out = $transaction;
        });

        // Return the transaction details
        return [
            'transaction' => $out,
            'currency' => $currency,
            'price' => $price,
        ];
    }

    /**
     * Build a list of preferred currencies for the given user.
     * The first currency in the returned list is the most preferred currency.
     *
     * Products may be bought using any of these currencies.
     * The list may be used to determine what product price to show if multiple
     * prices are available in different currencies.
     *
     * @param Bar $bar The bar the user is in.
     * @param User $user|null The user or null for the current user.
     *
     * @return [EconomyCurrency] A list of preferred currencies.
     */
    // TODO: move this function to some other class, user class?
    static function userCurrencies($bar, $user) {
        // TODO: optimize queries here!

        // Select the user
        if($user === null)
            $user = barauth()->getUser();

        // Get the user wallets, sort by preferred
        $wallets = $bar->economy->userWallets($user)->get();
        $currencies = $wallets
            ->map(function($w) use($bar) {
                return $bar
                    ->economy
                    ->currencies()
                    ->where('currency_id', $w->currency_id)
                    ->first();
            })
            ->filter(function($c) {
                return $c != null && $c->enabled;
            })
            ->unique('id');

        // Add other available currencies to list user has no wallet for yet
        // TODO: somehow sort this by relevance, or let bar owners sort
        $barCurrencies = $bar
            ->economy
            ->currencies()
            ->where('enabled', true)
            ->where('allow_wallet', true)
            ->whereNotIn('id', $currencies->pluck('id'))
            ->get();
        $currencies = $currencies->merge($barCurrencies);

        // Return the list of currencies
        return $currencies;
    }

    /**
     * The permission required for basic user interaction such as viewing and
     * buying products.
     * @return PermsConfig The permission configuration.
     */
    public static function permsUser() {
        return BarRoles::presetUser();
    }

    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return BarRoles::presetAdmin();
    }

    /**
     * The permission required creating a new bar.
     * @return PermsConfig The permission configuration.
     */
    public static function permsCreate() {
        return CommunityController::permsManage();
    }
}
