<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
use App\Models\Product;

class ProductController extends Controller {

    /**
     * Products index page.
     * This shows the list of products in the current economy.
     *
     * @return Response
     */
    public function index($communityId, $economyId) {
        // Get the user, community, find the products
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $products = $economy->products;

        return view('community.economy.product.index')
            ->with('economy', $economy)
            ->with('products', $products);
    }

    /**
     * Product creation page.
     *
     * @return Response
     */
    public function create($communityId, $economyId) {
        // Get the user, community, find the products
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        return view('community.economy.product.create')
            ->with('economy', $economy);
    }

    /**
     * Product create endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doCreate(Request $request, $communityId, $economyId) {
        // Get the user, community, find the products
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Create the product
        $bar = $economy->products()->create([
            'economy_id' => $economy->id,
            'type' => Product::TYPE_NORMAL,
            'name' => $request->input('name'),
            'enabled' => is_checked($request->input('enabled')),
            'archived' => is_checked($request->input('archived')),
        ]);

        // Redirect the user to the product index
        return redirect()
            ->route('community.economy.product.index', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
            ])
            ->with('success', __('pages.products.created'));
    }

    /**
     * Show a product.
     *
     * @return Response
     */
    public function show($communityId, $economyId, $productId) {
        // Get the user, community, find the product
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $product = $economy->products()->findOrFail($productId);

        return view('community.economy.product.show')
            ->with('economy', $economy)
            ->with('product', $product);
    }

    /**
     * Edit a product.
     *
     * @return Response
     */
    public function edit($communityId, $economyId, $productId) {
        // Get the user, community, find the product
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $product = $economy->products()->findOrFail($productId);

        return view('community.economy.product.edit')
            ->with('economy', $economy)
            ->with('product', $product);
    }

    /**
     * Product update endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doEdit(Request $request, $communityId, $economyId, $productId) {
        // Get the user, community, find the product
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $product = $economy->products()->findOrFail($productId);

        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Change properties
        $product->name = $request->input('name');
        $product->enabled = is_checked($request->input('enabled'));
        $product->archived = is_checked($request->input('archived'));

        // Save the product
        $product->save();

        // Redirect the user to the account overview page
        return redirect()
            ->route('community.economy.product.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'productId' => $product->id,
            ])
            ->with('success', __('pages.products.changed'));
    }

    /**
     * Page for confirming the deletion of the product.
     *
     * @return Response
     */
    public function delete($communityId, $economyId, $productId) {
        // TODO: suggest to archive instead!

        // Get the user, community, find the product
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $product = $economy->products()->findOrFail($productId);

        // TODO: ensure there are no other constraints that prevent deleting the
        // product

        return view('community.economy.product.delete')
            ->with('economy', $economy)
            ->with('product', $product);
    }

    /**
     * Delete a product.
     *
     * @return Response
     */
    public function doDelete($communityId, $economyId, $productId) {
        // Get the user, community, find the product
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $product = $economy->products()->findOrFail($productId);

        // TODO: ensure there are no other constraints that prevent deleting the
        // product

        // Delete the product
        $product->delete();

        // Redirect to the product index
        return redirect()
            ->route('community.economy.product.index', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id
            ])
            ->with('success', __('pages.products.deleted'));
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
