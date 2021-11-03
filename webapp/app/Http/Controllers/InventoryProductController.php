<?php

namespace App\Http\Controllers;

use App\Models\InventoryItemChange;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InventoryProductController extends Controller {

    const PAGINATE_ITEMS = 50;

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

        // Find last balance event
        $lastBalance = $item != null ? $item
            ->changes()
            ->type(InventoryItemChange::TYPE_BALANCE)
            ->first() : null;

        // Get monthly purchase volume
        // TODO: use proper period here, in case period is shorter!
        $purchaseVolumeMonth = $item == null
            ? 0
            : -$item
                ->changes()
                ->period(now()->subMonth(), null)
                ->type(InventoryItemChange::TYPE_PURCHASE)
                ->sum('quantity');

        // Count quantity in other inventories
        $quantityInOthers = $economy
            ->inventories()
            ->where('id', '!=', $inventory->id)
            ->get()
            ->map(function($i) use($product) {
                $item = $i->getItem($product);
                return $item != null ? max($item->quantity, 0) : 0;
            })
            ->sum();

        // Drain estimate
        $drainEstimate = null;
        if($item != null && $item->quantity <= 0)
            $drainEstimate = now();
        else if($item != null && $item->quantity >= 0 && $purchaseVolumeMonth > 0) {
            $seconds = max(round($item->quantity / $purchaseVolumeMonth * 2629800), 0);
            $drainEstimate = now()->addSeconds($seconds);
        }

        // Drain estimate with other inventories
        $drainEstimateOthers = null;
        $quantityWithOthers = ($item != null ? $item->quantity : 0) + $quantityInOthers;
        if($item != null && $quantityWithOthers >= 0 && $purchaseVolumeMonth > 0) {
            $seconds = max(round($quantityWithOthers / $purchaseVolumeMonth * 2629800), 0);
            $drainEstimateOthers = now()->addSeconds($seconds);
        }

        // Build list of quantities by inventory
        // TODO: shared with ProductController::show
        $quantities = $economy
            ->inventories
            ->map(function($i) use($product) {
                $item = $i->getItem($product);
                return [
                    'inventory' => $i,
                    'item' => $item,
                    'quantity' => $item != null ? $item->quantity : 0,
                ];
            })
            ->sortByDesc('quantity');

        return view('community.economy.inventory.product.show')
            ->with('economy', $economy)
            ->with('inventory', $inventory)
            ->with('product', $product)
            ->with('item', $item)
            ->with('lastBalance', $lastBalance)
            ->with('purchaseVolumeMonth', $purchaseVolumeMonth)
            ->with('quantityInOthers', $quantityInOthers)
            ->with('drainEstimate', $drainEstimate)
            ->with('drainEstimateOthers', $drainEstimateOthers)
            ->with('quantities', $quantities)
            ->with('changes', $item != null ? $item->changes()->limit(10)->get() : collect());
    }

    /**
     * List product changes.
     *
     * @return Response
     */
    public function changes(Request $request, $communityId, $economyId, $inventoryId, $productId) {
        // Get the community, find the inventory
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventory = $economy->inventories()->findOrFail($inventoryId);
        $product = $economy->products()->findOrFail($productId);
        $item = $inventory->getItem($product);

        // Create filter list
        $types = collect(InventoryItemChange::TYPES)
            ->filter(function($t) use($request) {
                $query = $request->query('filter_' . $t);
                return $query == null || is_checked($query);
            });

        return view('community.economy.inventory.product.changes')
            ->with('economy', $economy)
            ->with('inventory', $inventory)
            ->with('product', $product)
            ->with('item', $item)
            ->with('changes', $item != null ? $item->changes()->whereIn('type', $types)->paginate(self::PAGINATE_ITEMS) : collect());
    }

    /**
     * Show a product change.
     *
     * @return Response
     */
    public function change($communityId, $economyId, $inventoryId, $productId, $changeId) {
        // Get the community, find the inventory
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventory = $economy->inventories()->findOrFail($inventoryId);
        $product = $economy->products()->findOrFail($productId);
        $item = $inventory->getItem($product);
        $change = $item->changes()->findOrFail($changeId);

        return view('community.economy.inventory.product.change')
            ->with('economy', $economy)
            ->with('inventory', $inventory)
            ->with('product', $product)
            ->with('item', $item)
            ->with('change', $change);
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
