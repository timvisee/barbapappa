<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller {

    // TODO: action: edit
    // TODO: action: doEdit
    // TODO: action: delete
    // TODO: action: doDelete

    /**
     * Products index page.
     * This shows the list of products in the current economy.
     *
     * @return Response
     */
    public function index($communityId, $economyId) {
        // Get the user, community, find the economy and wallet
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
        // Get the user, community, find the economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $product = $economy->products()->findOrFail($productId);

        return view('community.economy.product.show')
            ->with('economy', $economy)
            ->with('product', $product);
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
