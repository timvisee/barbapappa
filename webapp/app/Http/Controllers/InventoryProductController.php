<?php

namespace App\Http\Controllers;

use App\Models\InventoryItemChange;
use Illuminate\Http\Response;

class InventoryProductController extends Controller {

    /**
     * Show an inventory product.
     *
     * @return Response
     */
    public function show($communityId, $economyId, $inventoryId, $productId) {
        // Get the community, find the inventory
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventory = $economy->inventories()->findOrFail($inventoryId);
        $product = $economy->products()->findOrFail($productId);
        $item = $inventory->getItem($product);

        // Find last balance date
        $lastBalanced = $item != null ? $item
            ->changes()
            ->type(InventoryItemChange::TYPE_BALANCE)
            ->first() : null;
        if($lastBalanced != null)
            $lastBalanced = $lastBalanced->created_at;

        // Build list of quantities by inventory
        $quantities = $economy
            ->inventories
            ->map(function($i) use($product) {
                $item = $i->getItem($product);
                return [
                    'inventory' => $i,
                    'quantity' => $item != null ? $item->quantity : 0,
                ];
            })
            ->sortByDesc('quantity');

        return view('community.economy.inventory.product.show')
            ->with('economy', $economy)
            ->with('inventory', $inventory)
            ->with('product', $product)
            ->with('item', $item)
            ->with('lastBalanced', $lastBalanced)
            ->with('quantities', $quantities)
            ->with('changes', $item != null ? $item->changes()->limit(10)->get() : collect());
    }

    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return InventoryController::permsView();
    }

    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return InventoryController::permsManage();
    }
}
