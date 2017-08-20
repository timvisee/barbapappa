<?php

namespace App\Http\Controllers;

class AccountController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must be authenticated
        $this->middleware('auth');
    }

    /**
     * Account overview page.
     *
     * @return $this
     */
    public function overview() {
        // Get the session user
        $user = barauth()->getSessionUser();

        // Get the user data
        $userData = Array(
            'first_name' => $user->first_name,
            'last_name' => $user->last_name
        );

        // Show the view
        return view('account.overview')
            ->with('userData', $userData);
    }
}
