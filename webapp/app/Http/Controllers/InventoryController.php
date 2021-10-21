<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
use App\Models\Inventory;

class InventoryController extends Controller {

    /**
     * Inventory index page.
     * This shows the list of inventories in the current economy.
     *
     * @return Response
     */
    public function index(Request $request, $communityId, $economyId) {
        $search = \Request::get('q');
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventories = $economy->inventories;

        return view('community.economy.inventory.index')
            ->with('economy', $economy)
            ->with('inventories', $inventories);
    }

    /**
     * Inventory creation page.
     *
     * @return Response
     */
    public function create(Request $request, $communityId, $economyId) {
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        return view('community.economy.inventory.create')
            ->with('economy', $economy);
    }

    /**
     * Inventory create endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doCreate(Request $request, $communityId, $economyId) {
        // Get the community, find the products
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Create inventory
        $inventory = $economy->inventories()->create([
            'name' => $request->input('name'),
        ]);

        // Show inventory
        return redirect()
            ->route('community.economy.inventory.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'inventoryId' => $inventory->id,
            ])
            ->with('success', __('pages.inventories.created'));
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
