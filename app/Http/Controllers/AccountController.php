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
     * Account page for the current user.
     *
     * @return Response
     */
    public function my() {
        return $this->show(
            barauth()->getSessionUser()->id
        );
    }

    /**
     * Account page.
     *
     * @param int $userId ID of the user to show the account for.
     *
     * @return Response
     */
    public function show($userId) {
        // Get the user
        /** @var User $user */
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $user = User::findOrFail($userId);

        // TODO: Make sure the current user has permission to view this user's account

        // Show the view
        return view('account.overview')
            ->with('user', $user);
    }
}
