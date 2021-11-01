<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\Inventory;
use App\Models\InventoryItemChange;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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
     * Show an inventory.
     *
     * @return Response
     */
    public function show($communityId, $economyId, $inventoryId) {
        // Get the community, find the inventory
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventory = $economy->inventories()->findOrFail($inventoryId);

        // Build list of (exhausted) products
        [$products, $exhaustedProducts] = Self::getProductList($inventory)
            ->partition(function($p) {
                return !$p['exhausted'];
            });

        return view('community.economy.inventory.show')
            ->with('economy', $economy)
            ->with('inventory', $inventory)
            ->with('products', $products)
            ->with('exhaustedProducts', $exhaustedProducts);
    }

    /**
     * Edit an inventory.
     *
     * @return Response
     */
    public function edit($communityId, $economyId, $inventoryId) {
        // Get the community, find the inventory
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventory = $economy->inventories()->findOrFail($inventoryId);

        return view('community.economy.inventory.edit')
            ->with('economy', $economy)
            ->with('inventory', $inventory);
    }

    /**
     * Inventory update endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doEdit(Request $request, $communityId, $economyId, $inventoryId) {
        // Get the community, find the inventory
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventory = $economy->inventories()->findOrFail($inventoryId);

        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Update inventory
        $inventory->name = $request->input('name');
        $inventory->save();

        // Redirect to inventory
        return redirect()
            ->route('community.economy.inventory.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'inventoryId' => $inventory->id,
            ])
            ->with('success', __('pages.inventories.changed'));
    }

    /**
     * Page for confirming the deletion of the inventory.
     *
     * @return Response
     */
    public function delete($communityId, $economyId, $inventoryId) {
        // Get the community, find the inventory
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventory = $economy->inventories()->findOrFail($inventoryId);

        return view('community.economy.inventory.delete')
            ->with('economy', $economy)
            ->with('inventory', $inventory);
    }

    /**
     * Delete a inventory.
     *
     * @return Response
     */
    public function doDelete(Request $request, $communityId, $economyId, $inventoryId) {
        // Get the community, find the inventory
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventory = $economy->inventories()->findOrFail($inventoryId);

        // Delete inventory
        $inventory->delete();

        // Redirect to the inventory index
        return redirect()
            ->route('community.economy.inventory.index', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
            ])
            ->with('success', __('pages.inventories.deleted'));
    }

    /**
     * Add/remove from/to an inventory.
     *
     * @return Response
     */
    public function addRemove($communityId, $economyId, $inventoryId) {
        // Get the community, find the inventory
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventory = $economy->inventories()->findOrFail($inventoryId);
        $products = $economy->products;

        // Build list of (exhausted) products
        [$products, $exhaustedProducts] = Self::getProductList($inventory)
            ->map(function($p) {
                $p['field'] = 'product_' . $p['product']->id;
                return $p;
            })
            ->partition(function($p) {
                return !$p['exhausted'];
            });

        return view('community.economy.inventory.addRemove')
            ->with('economy', $economy)
            ->with('inventory', $inventory)
            ->with('products', $products)
            ->with('exhaustedProducts', $exhaustedProducts);
    }

    /**
     * Do add/remove from/to an inventory.
     *
     * @return Response
     */
    public function doAddRemove(Request $request, $communityId, $economyId, $inventoryId) {
        // Get the community, find the inventory
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventory = $economy->inventories()->findOrFail($inventoryId);

        // Build list of products
        $products = Self::getProductList($inventory)
            ->map(function($p) {
                $p['field'] = 'product_' . $p['product']->id;
                return $p;
            });

        // Validate
        $rules = [
            'comment' => 'required|' . ValidationDefaults::DESCRIPTION,
            'confirm' => 'accepted',
            'type' => 'required|in:' . collect([
                InventoryItemChange::TYPE_BALANCE,
                InventoryItemChange::TYPE_ADD_REMOVE,
                InventoryItemChange::TYPE_SET,
            ])->join(','),
        ];
        $messages = [];
        foreach($products as $p) {
            $rules[$p['field'] . '_add'] = 'nullable|integer|min:0';
            $rules[$p['field'] . '_remove'] = 'nullable|integer|min:0';
        }
        $this->validate($request, $rules, $messages);

        $type = (int) $request->input('type');

        // Update quantities
        $count = 0;
        DB::transaction(function() use($products, $type, $request, $inventory, &$count) {
            foreach($products as $p) {
                $add = $request->input($p['field'] . '_add');
                $remove = $request->input($p['field'] . '_remove');

                // Update add/remove
                if($add != null) {
                    $inventory->changeProduct(
                        $p['product'],
                        $type,
                        (int) $add,
                        $request->input('comment'),
                        barauth()->getSessionUser(),
                        null,
                        null
                    );
                    $count += (int) $add;
                }
                if($remove != null) {
                    $inventory->changeProduct(
                        $p['product'],
                        $type,
                        -((int) $remove),
                        $request->input('comment'),
                        barauth()->getSessionUser(),
                        null,
                        null
                    );
                    $count += (int) $remove;
                }
            }
        });

        // Redirect to inventory
        return redirect()
            ->route('community.economy.inventory.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'inventoryId' => $inventory->id,
            ])
            ->with('success', trans_choice('pages.inventories.#productsAddedRemoved', $count) . '.');
    }

    /**
     * Balance an inventory.
     *
     * @return Response
     */
    public function balance($communityId, $economyId, $inventoryId) {
        // Get the community, find the inventory
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventory = $economy->inventories()->findOrFail($inventoryId);
        $products = $economy->products;

        // Build list of (exhausted) products
        [$products, $exhaustedProducts] = Self::getProductList($inventory)
            ->map(function($p) {
                $p['field'] = 'product_' . $p['product']->id;
                return $p;
            })
            ->partition(function($p) {
                return !$p['exhausted'];
            });

        return view('community.economy.inventory.balance')
            ->with('economy', $economy)
            ->with('inventory', $inventory)
            ->with('products', $products)
            ->with('exhaustedProducts', $exhaustedProducts);
    }

    /**
     * Do balance an inventory.
     *
     * @return Response
     */
    public function doBalance(Request $request, $communityId, $economyId, $inventoryId) {
        // Get the community, find the inventory
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventory = $economy->inventories()->findOrFail($inventoryId);
        $products = $economy->products;

        // Build list of products
        $products = Self::getProductList($inventory)
            ->map(function($p) {
                $p['field'] = 'product_' . $p['product']->id;
                return $p;
            });

        // Validate
        $rules = [
            'comment' => 'required|' . ValidationDefaults::DESCRIPTION,
            'confirm' => 'accepted',
            'type' => 'required|in:' . collect([
                InventoryItemChange::TYPE_BALANCE,
                InventoryItemChange::TYPE_ADD_REMOVE,
                InventoryItemChange::TYPE_SET,
            ])->join(','),
        ];
        $messages = [];
        foreach($products as $p) {
            $rules[$p['field'] . '_quantity'] = 'nullable|integer|empty_with:' . $p['field'] . '_delta';
            $rules[$p['field'] . '_delta'] = 'nullable|integer|empty_with:' . $p['field'] . '_quantity';
            $messages[$p['field'] . '_quantity.integer'] = __('pages.inventories.mustBeInteger');
            $messages[$p['field'] . '_delta.integer'] = __('pages.inventories.mustBeInteger');
        }
        $this->validate($request, $rules, $messages);

        $type = (int) $request->input('type');

        // Update quantities
        $count = 0;
        DB::transaction(function() use($products, $request, $inventory, $type, &$count) {
            foreach($products as $p) {
                $quantity = $request->input($p['field'] . '_quantity');
                $delta = $request->input($p['field'] . '_delta');

                // Update quantity or delta
                if($quantity != null) {
                    $inventory->setProductQuantity(
                        $p['product'],
                        $type,
                        (int) $quantity,
                        $request->input('comment'),
                        barauth()->getSessionUser(),
                        null,
                        null
                    );
                    $count += 1;
                } else if($delta != null) {
                    $inventory->changeProduct(
                        $p['product'],
                        $type,
                        (int) $delta,
                        $request->input('comment'),
                        barauth()->getSessionUser(),
                        null,
                        null
                    );
                    $count += 1;
                }
            }
        });

        // Redirect to inventory
        return redirect()
            ->route('community.economy.inventory.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'inventoryId' => $inventory->id,
            ])
            ->with('success', trans_choice('pages.inventories.#productsRebalanced', $count) . '.');
    }

    /**
     * Move products between inventories.
     *
     * @return Response
     */
    public function move($communityId, $economyId, $inventoryId) {
        // Get the community, find the inventory
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventory = $economy->inventories()->findOrFail($inventoryId);
        $products = $economy->products;

        // Build list of (exhausted) products
        [$products, $exhaustedProducts] = Self::getProductList($inventory)
            ->map(function($p) {
                $p['field'] = 'product_' . $p['product']->id;
                return $p;
            })
            ->partition(function($p) {
                return !$p['exhausted'];
            });

        return view('community.economy.inventory.move')
            ->with('economy', $economy)
            ->with('inventory', $inventory)
            ->with('products', $products)
            ->with('exhaustedProducts', $exhaustedProducts);
    }

    /**
     * Do move products between inventories.
     *
     * @return Response
     */
    public function doMove(Request $request, $communityId, $economyId, $inventoryId) {
        // Get the community, find the inventory
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $inventory = $economy->inventories()->findOrFail($inventoryId);
        $products = $economy->products;

        // Build list of products
        $products = Self::getProductList($inventory)
            ->map(function($p) {
                $p['field'] = 'product_' . $p['product']->id;
                return $p;
            });

        // Validate
        $rules = [
            'comment' => 'required|' . ValidationDefaults::DESCRIPTION,
            'confirm' => 'accepted',
            'inventory_from' => ['required', 'integer', ValidationDefaults::economyInventory($economy)],
            'inventory_to' => ['required', 'integer', 'different:inventory_from', ValidationDefaults::economyInventory($economy)],
        ];
        $messages = [];
        foreach($products as $p) {
            $rules[$p['field'] . '_quantity'] = 'nullable|integer';
        }
        $this->validate($request, $rules, $messages);

        // Get from/to inventory
        $from = $economy->inventories()->findOrFail($request->input('inventory_from'));
        $to = $economy->inventories()->findOrFail($request->input('inventory_to'));

        // Move products
        $count = 0;
        DB::transaction(function() use($products, $request, $from, $to, &$count) {
            foreach($products as $p) {
                $quantity = $request->input($p['field'] . '_quantity');
                if($quantity == null)
                    continue;
                $quantity = (int) $quantity;

                // Update quantities, link changes
                $fromChange = $from->changeProduct(
                    $p['product'],
                    InventoryItemChange::TYPE_MOVE,
                    -$quantity,
                    $request->input('comment'),
                    barauth()->getSessionUser(),
                    null,
                    null
                );
                $toChange = $to->changeProduct(
                    $p['product'],
                    InventoryItemChange::TYPE_MOVE,
                    $quantity,
                    $request->input('comment'),
                    barauth()->getSessionUser(),
                    $fromChange,
                    null
                );
                $fromChange->update(['related_id' => $toChange->id]);

                $count += abs($quantity);
            }
        });

        // Redirect to inventory
        return redirect()
            ->route('community.economy.inventory.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'inventoryId' => $inventory->id,
            ])
            ->with('success', trans_choice('pages.inventories.#productsRebalanced', $count) . '.');
    }

    /**
     * Get a product/item for an inventory.
     */
    private static function getProductList(Inventory $inventory) {
        return $inventory
            ->economy
            ->products
            ->map(function($product) use($inventory) {
                // TODO: this is inefficient, improve this
                $item = $inventory->getItem($product);
                $quantity = $item != null ? $item->quantity : 0;
                return [
                    'product' => $product,
                    'item' => $item,
                    'quantity' => $quantity,
                    'exhausted' => $quantity == 0,
                ];
            })
            ->sortBy('product.name');
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
