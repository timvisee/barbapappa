<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\User;
use App\Perms\AppRoles;

class SessionController extends Controller {

    /**
     * User session index.
     *
     * @param Request $request The request.
     * @param string $userId The user ID.
     *
     * @return Response
     */
    public function index($userId) {
        // To edit a different user, ensure we have administrator privileges
        if(barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
            return response(view('noPermission'));

        // Get user
        $user = $userId != null ? User::findOrFail($userId) : barauth()->getSessionUser();

        // Fetch sessions
        $activeSessions = $user->sessions()->active()->latest()->get();
        $expiredSessions = $user->sessions()->expired()->latest('expire_at')->get();

        return view('account.session.index')
            ->with('activeSessions', $activeSessions)
            ->with('expiredSessions', $expiredSessions);
    }

    /**
     * Session show page.
     *
     * @param Request $request The request.
     * @param string $userId The user ID.
     *
     * @return Response
     */
    public function show($userId, $sessionId) {
        // To edit a different user, ensure we have administrator privileges
        if(barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
            return response(view('noPermission'));

        // Get user and session
        $user = $userId != null ? User::findOrFail($userId) : barauth()->getSessionUser();
        $session = $user->sessions()->findOrFail($sessionId);

        return view('account.session.show')
            ->with('session', $session);
    }

    /**
     * Do session expiry.
     *
     * @param Request $request The request.
     * @param string $userId The user ID.
     * @param string $sessionId The session ID.
     *
     * @return Response
     */
    public function doExpire($userId, $sessionId) {
        // To edit a different user, ensure we have administrator privileges
        if(barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
            return response(view('noPermission'));

        // Get user and session
        $user = $userId != null ? User::findOrFail($userId) : barauth()->getSessionUser();
        $session = $user->sessions()->findOrFail($sessionId);

        // Invalidate session
        $session->invalidate();

        // Redirect to the sessions page, show a success message
        return redirect()
            ->route('account.sessions', ['userId' => $userId])
            ->with('success', __('account.invalidatedSession'));
    }
}
