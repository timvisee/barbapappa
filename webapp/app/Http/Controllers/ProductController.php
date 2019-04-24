<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;

class ProductController extends Controller {

    // TODO: action: delete
    // TODO: action: doDelete

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
