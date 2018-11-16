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
        $currencies = $economy->currencies()->withDisabled()->get();

        return view('community.economy.currency.index')
            ->with('economy', $economy)
            ->with('enabled', $currencies->filter(function($c) { return $c->enabled; }))
            ->with('disabled', $currencies->filter(function($c) { return !$c->enabled; }));
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
        $currency = $economy->currencies()->withDisabled()->findOrFail($supportedCurrencyId);

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
        $usedCurrencies = $economy->currencies()->withDisabled()->pluck('currency_id');
        $currencies = Currency::whereNotIn('id', $usedCurrencies)->get();

        // Make sure there's a currency that can be added
        if($currencies->isEmpty()) {
            return redirect()
                ->route('community.economy.currency.index', ['communityId' => $communityId, 'economyId' => $economyId])
                ->with('error', __('pages.currencies.' . ($usedCurrencies->isNotEmpty() ? 'noMoreCurrenciesToAdd' : 'noCurrenciesToAdd')));
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
        $usedCurrencies = $economy->currencies()->withDisabled()->pluck('currency_id');
        $currencies = Currency::whereNotIn('id', $usedCurrencies)->get();

        // Validate
        $this->validate($request, [
            'currency' => array_merge(['required'], ValidationDefaults::economySupportedCurrency($economy)),
        ]);

        // Create the supported currency configuration and save
        $currency = $economy->currencies()->create([
            'enabled' => is_checked($request->input('enabled')),
            'currency_id' => $request->input('currency'),
            'allow_wallet' => is_checked($request->input('allow_wallet')),
            // TODO: define the proper value here
            'product_price_default' => 1,
        ]);

        // Redirect to the show view after creation
        return redirect()
            ->route('community.economy.currency.index', ['communityId' => $communityId, 'economyId' => $economy->id])
            ->with('success', __('pages.currencies.currencyCreated'));
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
        $currency = $economy->currencies()->withDisabled()->findOrFail($supportedCurrencyId);

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
        $currency = $economy->currencies()->withDisabled()->findOrFail($supportedCurrencyId);

        // Update the properties
        $currency->enabled = is_checked($request->input('enabled'));
        $currency->allow_wallet = is_checked($request->input('allow_wallet'));
        $currency->save();

        // Redirect to the show view after editing
        return redirect()
            ->route('community.economy.currency.index', ['communityId' => $communityId, 'economyId' => $economyId])
            ->with('success', __('pages.currencies.currencyUpdated'));
    }

    /**
     * The page to delete a supported currency of an economy.
     *
     * @return Response
     */
    public function delete($communityId, $economyId, $supportedCurrencyId) {
        // Get the community, and the economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $currency = $economy->currencies()->withDisabled()->findOrFail($supportedCurrencyId);

        return view('community.economy.currency.delete')
            ->with('economy', $economy)
            ->with('currency', $currency);
    }

    /**
     * Delete a supported currency of an economy.
     *
     * @return Response
     */
    public function doDelete($communityId, $economyId, $supportedCurrencyId) {
        // Get the community, find the economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $currency = $economy->currencies()->withDisabled()->findOrFail($supportedCurrencyId);

        // TODO: ensure deletion is allowed

        // Delete the supported currency configuration
        $currency->delete();

        // Redirect to the index page after deleting
        return redirect()
            ->route('community.economy.currency.index', ['communityId' => $communityId, 'economyId' => $economy->id])
            ->with('success', __('pages.currencies.currencyDeleted'));
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
