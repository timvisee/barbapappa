<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\Mutation;
use App\Models\MutationProduct;
use App\Models\MutationWallet;
use App\Models\Transaction;
use App\Models\Bar;
use App\Perms\BarRoles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Validator;

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

        // Build a list of preferred currencies for the user
        // TODO: if there's only one currency, that is usable, use null to
        //       greatly simplify product queries
        $currencies = BarController::userCurrencies($bar, $user);

        // Search, or show top products
        $search = \Request::get('q');
        if(!empty($search))
            $products = $bar->economy->searchProducts($search, $currency_ids);
        else
            $products = $bar->economy->products->sortBy(function($p) {
                return $p->displayName();
            });

        // Show the products page
        return view('bar.product.index')
            ->with('products', $products)
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
