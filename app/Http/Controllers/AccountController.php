<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;

class AccountController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must be authenticated
        $this->middleware('auth');
    }

    /**
     * Account page.
     *
     * @return Response
     */
    public function show() {
        return view('account.overview');
    }
}
