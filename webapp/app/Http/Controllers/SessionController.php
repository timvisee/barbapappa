<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Perms\AppRoles;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SessionController extends Controller {

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
     * User session index.
     *
     * @param Request $request The request.
     * @param string $userId The user ID.
     *
     * @return Response
     */
    public function index($userId) {
        // Get user and sessions
        $user = $userId != null ? User::findOrFail($userId) : barauth()->getSessionUser();
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
        // Get user and active sessions
        $user = $userId != null ? User::findOrFail($userId) : barauth()->getSessionUser();
        $sessions = $user->sessions()->active()->get();

        // Get settings
        $expireCurrent = is_checked($request->input('expire_current'));
        $expireSameNetwork = is_checked($request->input('expire_same_network'));

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

        // Build redirect, redirect to sessions or index if invalidating current session for our user
        if($expireCurrent && barauth()->getSessionUser()->id == $userId)
            $redirect = redirect()
                ->route('index');
        else
            $redirect = redirect()
                ->route('account.sessions', ['userId' => $userId]);
        return $redirect
            ->with('success', trans_choice('account.invalidatedSessions#', $count));
    }
}
