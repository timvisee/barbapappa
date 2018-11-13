<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
use App\Models\Currency;
use App\Models\CurrencySupport;
use App\Perms\Builder\Config as PermsConfig;
use App\Perms\CommunityRoles;

class EconomyCurrencyController extends Controller {

    /**
     * Supported currency for community economy index.
     *
     * @return Response
     */
    public function index($communityId, $economyId) {
        // Get the community, find economy, query currencies
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $currencies = $economy->supportedCurrencies()->get();

        return view('community.economy.currency.index')
            ->with('economy', $economy)
            ->with('currencies', $currencies);
    }

    /**
     * Show the supported currencies for a community economy with the given ID.
     *
     * @return Response
     */
    public function show($communityId, $economyId, $supportedCurrencyId) {
        // Get the community, find economy and supported currency
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $currency = $economy->supportedCurrencies()->findOrFail($supportedCurrencyId);

        return view('community.economy.currency.show')
            ->with('economy', $economy)
            ->with('currency', $currency);
    }

    /**
     * Add a new supported currency for a community economy.
     *
     * @return Response
     */
    public function create($communityId, $economyId) {
        // Get the community, find economy, query currencies
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $usedCurrencies = $economy->supportedCurrencies()->pluck('currency_id');
        $currencies = Currency::whereNotIn('id', $usedCurrencies)->get();

        // Make sure there's a currency that can be added
        if($currencies->isEmpty()) {
            return redirect()
                ->route('community.economy.currency.index', ['communityId' => $communityId, 'economyId' => $economyId])
                ->with('error', __('pages.supportedCurrencies.' . ($usedCurrencies->isNotEmpty() ? 'noMoreCurrenciesToAdd' : 'noCurrenciesToAdd')));
        }

        return view('community.economy.currency.create')
            ->with('economy', $economy)
            ->with('currencies', $currencies);
    }

    /**
     * Create a community economy.
     *
     * @return Response
     */
    public function doCreate(Request $request, $communityId, $economyId) {
        // Get the community, find economy, query currencies
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $usedCurrencies = $economy->supportedCurrencies()->pluck('currency_id');
        $currencies = Currency::whereNotIn('id', $usedCurrencies)->get();

        // Validate
        $this->validate($request, [
            'currency' => array_merge(['required'], ValidationDefaults::economySupportedCurrency($economy)),
        ]);

        // Create the supported currency configuration and save
        $currency = $economy->supportedCurrencies()->create([
            'enabled' => is_checked($request->input('enabled')),
            'currency_id' => $request->input('currency'),
            'allow_wallet' => is_checked($request->input('allow_wallet')),
            // TODO: define the proper value here
            'product_price_default' => 1,
        ]);

        // Redirect to the show view after creation
        return redirect()
            ->route('community.economy.currency.show', ['communityId' => $communityId, 'economyId' => $economy->id, 'supportedCurrencyId' => $currency->id])
            ->with('success', __('pages.supportedCurrencies.currencyCreated'));
    }

    /**
     * The edit page for a supported currency of an economy.
     *
     * @return Response
     */
    public function edit($communityId, $economyId, $supportedCurrencyId) {
        // Get the community, find economy and supported currency
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $currency = $economy->supportedCurrencies()->findOrFail($supportedCurrencyId);

        // Show the edit view
        return view('community.economy.currency.edit')
            ->with('economy', $economy)
            ->with('currency', $currency);
    }

    /**
     * Edit a supported currency of an economy.
     *
     * @return Response
     */
    public function doEdit(Request $request, $communityId, $economyId, $supportedCurrencyId) {
        // TODO: validate future price default property
        // // Validate
        // $this->validate($request, [
        //     'name' => 'required|' . ValidationDefaults::NAME,
        // ]);

        // Get the community, find economy and supported currency
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $currency = $economy->supportedCurrencies()->findOrFail($supportedCurrencyId);

        // Update the properties
        $currency->enabled = is_checked($request->input('enabled'));
        $currency->allow_wallet = is_checked($request->input('allow_wallet'));
        $currency->save();

        // Redirect to the show view after editing
        return redirect()
            ->route('community.economy.currency.show', ['communityId' => $communityId, 'economyId' => $economyId, 'supportedCurrencyId' => $currency->id])
            ->with('success', __('pages.supportedCurrencies.currencyUpdated'));
    }

    /**
     * The page to delete a community economy.
     *
     * @return Response
     */
    public function delete($communityId, $economyId) {
        // Get the community, and the economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        return view('community.economy.currency.delete')
            ->with('economy', $economy);
    }

    /**
     * Delete a community economy.
     *
     * @return Response
     */
    public function doDelete($communityId, $economyId) {
        // Get the community, find the economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // TODO: ensure deletion is allowed (and no users are using it)

        // Delete the economy
        $economy->delete();

        // Redirect to the index page after deleting
        return redirect()
            ->route('community.economy.currency.index', ['communityId' => $communityId])
            ->with('success', __('pages.economies.economyDeleted'));
    }

    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return EconomyController::permsView();
    }

    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return EconomyController::permsManage();
    }
}
