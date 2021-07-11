<?php

namespace App\Http\Controllers;

use App\Perms\Builder\Config as PermsConfig;
use BarPay\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EconomyPaymentController extends Controller {

    const PAGINATE_ITEMS = 50;

    /**
     * Community economy payments index.
     *
     * @return Response
     */
    public function index($communityId, $economyId) {
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $payments = $economy
            ->payments()
            ->with('user')
            ->paginate(self::PAGINATE_ITEMS);

        return view('community.economy.payment.index')
            ->with('economy', $economy)
            ->with('payments', $payments);
    }
}
