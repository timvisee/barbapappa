<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
use App\Perms\CommunityRoles;
use App\Perms\Builder\Config as PermsConfig;

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
        // Get the community, find economy, query currencies
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
        $currency = $economy->supportedCurrencies()->findOrFail($supportedCurrencyId);

        return view('community.economy.currency.create')
            ->with('economy', $economy)
            ->with('currency', $currency);
    }

    /**
     * Create a community economy.
     *
     * @return Response
     */
    public function doCreate(Request $request, $communityId) {
        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Get the community
        $community = \Request::get('community');

        // Create an economy and save
        $economy = $community->economies()->create([
            'name' => $request->input('name'),
        ]);

        // Redirect to the show view after editing
        return redirect()
            ->route('community.economy.currency.show', ['communityId' => $communityId, 'economyId' => $economy->id])
            ->with('success', __('pages.economies.economyCreated'));
    }

    /**
     * The edit page for a community economy.
     *
     * @return Response
     */
    public function edit($communityId, $economyId) {
        // Get the community, find the economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Show the edit view
        return view('community.economy.currency.edit')
            ->with('economy', $economy);
    }

    /**
     * Edit a community economy.
     *
     * @return Response
     */
    public function doEdit(Request $request, $communityId, $economyId) {
        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Get the community, find the economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Update the properties
        $economy->name = $request->input('name');
        $economy->save();

        // Redirect to the show view after editing
        return redirect()
            ->route('community.economy.currency.show', ['communityId' => $communityId, 'economyId' => $economyId])
            ->with('success', __('pages.economies.economyUpdated'));
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
