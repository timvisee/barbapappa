<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Perms\AppRoles;
use Illuminate\Http\Response;

class AccountController extends Controller {

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        // User must have permission to manage the current user
        $this->middleware(function($request, $next) {
            $userId = $request->route('userId');
            if($userId != null && barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
                return response(view('noPermission'));

            return $next($request);
        });
    }

    /**
     * Account page.
     *
     * @return Response
     */
    public function show($userId = null) {
        // Get user
        $user = $userId != null ? User::findOrFail($userId) : barauth()->getSessionUser();

        return view('account.overview')
            ->with('user', $user)
            ->with('activeSessions', $user->sessions()->active()->count())
            ->with('expiredSessions', $user->sessions()->expired()->count());
    }
}
