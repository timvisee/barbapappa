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

    /**
     * Session expire all page.
     *
     * @param Request $request The request.
     * @param string $userId The user ID.
     *
     * @return Response
     */
    public function expireAll($userId) {
        // To edit a different user, ensure we have administrator privileges
        if(barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
            return response(view('noPermission'));

        return view('account.session.expireAll');
    }

    /**
     * Do expire all sessions.
     *
     * @param Request $request The request.
     * @param string $userId The user ID.
     *
     * @return Response
     */
    public function doExpireAll(Request $request, $userId) {
        // To edit a different user, ensure we have administrator privileges
        if(barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
            return response(view('noPermission'));

        // Get user and active sessions
        $user = $userId != null ? User::findOrFail($userId) : barauth()->getSessionUser();
        $sessions = $user->sessions()->active()->get();

        // Get settings
        $expireCurrent = is_checked($request->input('expire_current'));
        $expireSameNetwork = is_checked($request->input('expire_same_network'));
        $expireOther =is_checked($request->input('expire_other'));

        // Loop through all sessions, invalidate based on settings
        $count = 0;
        foreach($sessions as $session) {
            $expire = $session->isCurrent()
                ? $expireCurrent
                : (!$session->isSameIp() || $expireSameNetwork);
            if($expire) {
                $session->invalidate();
                $count++;
            }
        }

        // Build redirect, redirect to sessions or index if invalidating current
        if($expireCurrent)
            $redirect = redirect()
                ->route('index');
        else
            $redirect = redirect()
                ->route('account.sessions', ['userId' => $userId]);
        return $redirect
            ->with('success', trans_choice('account.invalidatedSessions#', $count));
    }
}
