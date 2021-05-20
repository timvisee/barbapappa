<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Perms\AppRoles;

class AccountController extends Controller {

    /**
     * Account page.
     *
     * @return Response
     */
    public function show(Request $request, $userId = null) {
        // To edit a different user, ensure we have administrator privileges
        if($userId !== null && barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
            return response(view('noPermission'));

        return view('account.overview');
    }
}
