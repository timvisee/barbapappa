<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KioskSessionController extends Controller {

    /**
     * Bar session index.
     *
     * @param Request $request The request.
     * @param string $barId The bar ID.
     *
     * @return Response
     */
    public function index($barId) {
        // Get bar and sessions
        $bar = \Request::get('bar');
        $activeSessions = $bar->kioskSessions()->active()->latest()->get();
        $expiredSessions = $bar->kioskSessions()->expired()->latest('expire_at')->get();

        return view('bar.kiosk.session.index')
            ->with('activeSessions', $activeSessions)
            ->with('expiredSessions', $expiredSessions);
    }

    /**
     * Session show page.
     *
     * @param Request $request The request.
     * @param string $barId The bar ID.
     *
     * @return Response
     */
    public function show($barId, $sessionId) {
        // Get bar and session
        $bar = \Request::get('bar');
        $session = $bar->kioskSessions()->findOrFail($sessionId);

        return view('bar.kiosk.session.show')
            ->with('session', $session);
    }

    /**
     * Do session expiry.
     *
     * @param Request $request The request.
     * @param string $barId The bar ID.
     * @param string $sessionId The session ID.
     *
     * @return Response
     */
    public function doExpire($barId, $sessionId) {
        // Get bar and session
        $bar = \Request::get('bar');
        $session = $bar->kioskSessions()->findOrFail($sessionId);

        // Invalidate session
        $session->invalidate();

        // Redirect to the sessions page, show a success message
        return redirect()
            ->route('bar.kiosk.sessions', ['barId' => $barId])
            ->with('success', __('account.invalidatedSession'));
    }

    /**
     * Session expire all page.
     *
     * @param Request $request The request.
     * @param string $barId The bar ID.
     *
     * @return Response
     */
    public function expireAll($barId) {
        return view('bar.kiosk.session.expireAll');
    }

    /**
     * Do expire all sessions.
     *
     * @param Request $request The request.
     * @param string $barId The bar ID.
     *
     * @return Response
     */
    public function doExpireAll($barId) {
        // Get bar and active sessions
        $bar = \Request::get('bar');
        $sessions = $bar->kioskSessions()->active()->get();

        // Loop through all sessions, invalidate
        foreach($sessions as $session)
            $session->invalidate();

        // Redirect to sessions or index if invalidating current session for our user
        return redirect()
            ->route('bar.kiosk.sessions', ['barId' => $barId])
            ->with('success', trans_choice('account.invalidatedSessions#', $sessions->count()));
    }
}
