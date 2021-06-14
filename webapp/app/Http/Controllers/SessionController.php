<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\User;
use App\Perms\AppRoles;

class SessionController extends Controller {

    /**
     * Sessions page.
     *
     * @param Request $request The request.
     * @param string $userId The user ID.
     *
     * @return Response
     */
    public function overview($userId) {
        // To edit a different user, ensure we have administrator privileges
        if(barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
            return response(view('noPermission'));

        // Get user
        $user = $userId != null ? User::findOrFail($userId) : barauth()->getSessionUser();

        // Fetch sessions
        $activeSessions = $user->sessions()->active()->latest()->get();
        $expiredSessions = $user->sessions()->expired()->latest('expire_at')->get();

        return view('account.session.overview')
            ->with('activeSessions', $activeSessions)
            ->with('expiredSessions', $expiredSessions);
    }
}
