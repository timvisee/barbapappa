<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class BarProductController extends Controller {

    /**
     * Bar products page.
     *
     * @return Response
     */
    public function index($barId) {
        // Get the bar and session user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();
        $economy = $bar->economy;

        // Build a list of preferred currencies for the user
        // TODO: if there's only one currency, that is usable, use null to
        //       greatly simplify product queries
        $currencies = BarController::userCurrencies($bar, $user);

        // Search, or show top products
        $search = \Request::get('q');
        if(!($search === null || trim($search) === ''))
            $products = $bar->economy->searchProducts($search, null);
        else
            $products = $bar->economy->products->sortBy(function($p) {
                return $p->displayName();
            }, SORT_NATURAL | SORT_FLAG_CASE);

        // Show the products page
        return view('bar.product.index')
            ->with('products', $products)
            ->with('economy', $economy)
            ->with('currencies', $currencies);
    }

    /**
     * Bar product inspection page.
     *
     * @return Response
     */
    public function show($barId, $productId) {
        // Get the bar and session user
        $bar = \Request::get('bar');
        $product = $bar->economy->products()->findOrFail($productId);
        $user = barauth()->getSessionUser();

        // Build a list of preferred currencies for the user
        // TODO: if there's only one currency, that is usable, use null to
        //       greatly simplify product queries
        $currencies = BarController::userCurrencies($bar, $user);

        // Show the product inspection page
        return view('bar.product.show')
            ->with('product', $product)
            ->with('currencies', $currencies);
    }
}
