<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\EconomyCurrency;
use BarPay\Models\Service as PayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;

class BunqAccountController extends Controller {

    // TODO: make this controller generic, also support it for application
    //       glboal configuration?

    /**
     * Bunq account index page for communities.
     *
     * @return Response
     */
    public function index(Request $request, $communityId) {
        $user = barauth()->getUser();
        $community = \Request::get('community');
        // TODO: also list disabled accounts
        $accounts = $community->bunqAccounts;

        return view('community.bunqAccount.index')
            ->with('accounts', $accounts);
    }

    // TODO: set proper perms here!
    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return EconomyController::permsView();
    }

    // TODO: set proper perms here!
    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return EconomyController::permsManage();
    }
}
