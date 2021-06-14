<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\User;
use App\Perms\AppRoles;

class AccountController extends Controller {

    /**
     * Account page.
     *
     * @return Response
     */
    public function show($userId = null) {
        // To edit a different user, ensure we have administrator privileges
        if($userId !== null && barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
            return response(view('noPermission'));

        // Get user
        $user = $userId != null ? User::findOrFail($userId) : barauth()->getSessionUser();

        return view('account.overview')
            ->with('user', $user)
            ->with('activeSessions', $user->sessions()->active()->count())
            ->with('expiredSessions', $user->sessions()->expired()->count());
    }
}
