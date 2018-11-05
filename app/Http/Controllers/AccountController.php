<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;

class AccountController extends Controller {

    /**
     * Account page.
     *
     * @return Response
     */
    public function show() {
        // TODO: make sure the user has enough permission to view other users

        return view('account.overview');
    }
}
