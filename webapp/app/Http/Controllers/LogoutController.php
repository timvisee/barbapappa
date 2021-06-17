<?php

namespace App\Http\Controllers;

use App\Services\Auth\Authenticator as UserAuthenticator;
use App\Services\Auth\Kiosk\Authenticator as KioskAuthenticator;
use Illuminate\Support\Facades\Cookie;

class LogoutController extends Controller {

    /**
     * Log out.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout() {
        // Redirect the user to the index page if not authenticated
        if(!barauth()->isAuth() && !kioskauth()->isAuth())
            return $this->finishAndRedirect();

        // Invalidate user session
        $session = barauth()->getAuthState()->getSession();
        if($session != null)
            $session->invalidate();

        // Invalidate kiosk session
        $session = kioskauth()->getAuthState()->getSession();
        if($session != null)
            $session->invalidate();

        // Finish
        return $this
            ->finishAndRedirect()
            ->with('success', __('auth.loggedOut'));
    }

    /**
     * Finish the logout process and redirect the user to the index page of the application.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function finishAndRedirect() {
        // Redirect the user, and forget the session cookie
        return redirect()
            ->route('index')
            ->withCookie(Cookie::forget(UserAuthenticator::AUTH_COOKIE))
            ->withCookie(Cookie::forget(KioskAuthenticator::AUTH_COOKIE));
    }
}
