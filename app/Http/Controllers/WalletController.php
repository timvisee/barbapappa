<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
use App\Models\Currency;
use App\Models\EconomyCurrency;
use App\Perms\Builder\Config as PermsConfig;
use App\Perms\CommunityRoles;

class WalletController extends Controller {

    /**
     * Wallet index page for a community user.
     * This shows a list of economies wallets may be created in.
     * A user clicks on an economy to go to a specialized page for wallet
     * management.
     *
     * @return Response
     */
    public function index($communityId) {
        // Get the community, find the economies
        $community = \Request::get('community');
        // TODO: only get community economies having at least one bar or user wallet
        $economies = $community->economies();

        // Immediately redirect to specific economy if there's only one
        if($economies->limit(2)->count() == 1) {
            $economy = $economies->firstOrFail();
            return redirect()
                ->route('community.wallet.list', ['communityId' => $communityId, 'economyId' => $economy->id]);
        }

        return view('community.wallet.index')
            ->with('economies', $economies->get());
    }

    /**
     * List the user wallets for the specified economy.
     *
     * @return Response
     */
    public function list($communityId, $economyId) {
        // Get the user, community, find economy and user wallets
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallets = $user->wallets()->where('economy_id', $economyId);

        return view('community.wallet.list')
            ->with('economy', $economy)
            ->with('wallets', $wallets->get());
    }

    /**
     * Show a user wallet.
     *
     * @return Response
     */
    public function show($communityId, $economyId, $walletId) {
        // Get the user, community, find the economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $user
            ->wallets()
            ->where('economy_id', $economyId)
            ->findOrFail($walletId);

        return view('community.wallet.show')
            ->with('economy', $economy)
            ->with('wallet', $wallet);
    }

    // /**
    //  * Add a new economy currency for a community economy.
    //  *
    //  * @return Response
    //  */
    // public function create($communityId, $economyId) {
    //     // Get the community, find economy, query currencies
    //     $community = \Request::get('community');
    //     $economy = $community->economies()->findOrFail($economyId);
    //     $usedCurrencies = $economy->currencies()->withDisabled()->pluck('currency_id');
    //     $currencies = Currency::whereNotIn('id', $usedCurrencies)->get();

    //     // Make sure there's a currency that can be added
    //     if($currencies->isEmpty()) {
    //         return redirect()
    //             ->route('community.economy.currency.index', ['communityId' => $communityId, 'economyId' => $economyId])
    //             ->with('error', __('pages.currencies.' . ($usedCurrencies->isNotEmpty() ? 'noMoreCurrenciesToAdd' : 'noCurrenciesToAdd')));
    //     }

    //     return view('community.economy.currency.create')
    //         ->with('economy', $economy)
    //         ->with('currencies', $currencies);
    // }

    // /**
    //  * Create a community economy.
    //  *
    //  * @return Response
    //  */
    // public function doCreate(Request $request, $communityId, $economyId) {
    //     // Get the community, find economy, query currencies
    //     $community = \Request::get('community');
    //     $economy = $community->economies()->findOrFail($economyId);
    //     $usedCurrencies = $economy->currencies()->withDisabled()->pluck('currency_id');
    //     $currencies = Currency::whereNotIn('id', $usedCurrencies)->get();

    //     // Validate
    //     $this->validate($request, [
    //         'currency' => array_merge(['required'], ValidationDefaults::economyCurrency($economy)),
    //     ]);

    //     // Create the economy currency configuration and save
    //     $currency = $economy->currencies()->create([
    //         'enabled' => is_checked($request->input('enabled')),
    //         'currency_id' => $request->input('currency'),
    //         'allow_wallet' => is_checked($request->input('allow_wallet')),
    //         // TODO: define the proper value here
    //         'product_price_default' => 1,
    //     ]);

    //     // Redirect to the show view after creation
    //     return redirect()
    //         ->route('community.economy.currency.index', ['communityId' => $communityId, 'economyId' => $economy->id])
    //         ->with('success', __('pages.currencies.currencyCreated'));
    // }

    /**
     * The edit page for a user wallet.
     *
     * @return Response
     */
    public function edit($communityId, $economyId, $walletId) {
        // Get the user, community, find economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $user
            ->wallets()
            ->where('economy_id', $economyId)
            ->findOrFail($walletId);

        // Show the edit view
        return view('community.wallet.edit')
            ->with('economy', $economy)
            ->with('wallet', $wallet);
    }

    /**
     * Edit a user wallet.
     *
     * @return Response
     */
    public function doEdit(Request $request, $communityId, $economyId, $walletId) {
        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Get the user, community, find economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $user
            ->wallets()
            ->where('economy_id', $economyId)
            ->findOrFail($walletId);

        // Update the name
        $wallet->name = $request->input('name');
        $wallet->save();

        // Redirect to the show view after editing
        return redirect()
            ->route('community.wallet.list', ['communityId' => $communityId, 'economyId' => $economyId])
            ->with('success', __('pages.wallets.walletUpdated'));
    }

    // /**
    //  * The page to delete a economy currency of an economy.
    //  *
    //  * @return Response
    //  */
    // public function delete($communityId, $economyId, $economyCurrencyId) {
    //     // Get the community, and the economy
    //     $community = \Request::get('community');
    //     $economy = $community->economies()->findOrFail($economyId);
    //     $currency = $economy->currencies()->withDisabled()->findOrFail($economyCurrencyId);

    //     return view('community.economy.currency.delete')
    //         ->with('economy', $economy)
    //         ->with('currency', $currency);
    // }

    // /**
    //  * Delete a economy currency of an economy.
    //  *
    //  * @return Response
    //  */
    // public function doDelete($communityId, $economyId, $economyCurrencyId) {
    //     // Get the community, find the economy
    //     $community = \Request::get('community');
    //     $economy = $community->economies()->findOrFail($economyId);
    //     $currency = $economy->currencies()->withDisabled()->findOrFail($economyCurrencyId);

    //     // TODO: ensure deletion is allowed

    //     // Delete the economy currency configuration
    //     $currency->delete();

    //     // Redirect to the index page after deleting
    //     return redirect()
    //         ->route('community.economy.currency.index', ['communityId' => $communityId, 'economyId' => $economy->id])
    //         ->with('success', __('pages.currencies.currencyDeleted'));
    // }
}
