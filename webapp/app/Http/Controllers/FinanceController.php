<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Validator;

use App\Helpers\ValidationDefaults;

class FinanceController extends Controller {

    /**
     * Economy finance overview page.
     *
     * @return Response
     */
    public function overview(Request $request, $communityId, $economyId) {
        // Get the user, community, find the products
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        $wallets = $economy->wallets;
        $walletSum = $economy->sumAmounts($wallets, 'balance');
        $paymentsProgressing = $economy->payments()->inProgress()->get();
        $paymentProgressingSum = $economy->sumAmounts($paymentsProgressing, 'money');

        return view('community.economy.finance.overview')
            ->with('economy', $economy)
            ->with('walletSum', $walletSum)
            ->with('paymentProgressingSum', $paymentProgressingSum);
    }

    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return EconomyController::permsView();
    }
}
