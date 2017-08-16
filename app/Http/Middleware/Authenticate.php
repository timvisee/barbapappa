<?php

namespace App\Http\Middleware;

use App\Session;

use Closure;
use Illuminate\Http\Request;

class Authenticate {

    const AUTH_COOKIE = 'session_token';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        // Get the session token
        $sessionToken = $this->getSessionTokenCookie($request);
        if($sessionToken == null)
            return $this->setAuthenticated($request, null, false, $next);

        // Find the session object
        $session = Session::where('token', '=', $sessionToken)->first();
        if($session == null)
            return $this->setAuthenticated($request, null, false, $next);

        // Check whether the session has expired
        if($session->isExpired())
            return $this->setAuthenticated($request, null, false, $next);

        // Determine whether the user has any mail address that is validated
        $verifiedMailCount = $session->user()->first()
            ->emails()->where('verified_at', '!=', null)
            ->count();
        $mailVerified = $verifiedMailCount > 0;

        // Set the session
        return $this->setAuthenticated($request, $session, $mailVerified, $next);
    }

    /**
     * Get the session token cookie value.
     *
     * @param Request $request Request instance.
     *
     * @return string|null Session token or null if the cookie didn't exist.
     */
    private function getSessionTokenCookie(Request $request) {
        // Make sure the request isn't null
        if($request == null)
            return null;

        // Get the cookie
        return $request->cookie(self::AUTH_COOKIE);
    }

    /**
     * Set whether the user is authenticated.
     *
     * @param Request $request
     * @param Session|null $session Session instance if authenticated, or null if not authenticated.
     * @param bool $mailVerified True if any of the mail addresses of the user is verified, false if not.
     * @param Closure $next
     * @return mixed|null
     */
    private function setAuthenticated(Request $request, $session, $mailVerified, Closure $next = null) {
        // Set the session in the authentication service
        barauth()->updateState($session, $mailVerified);

        // Define the response variable
        $response = null;

        // Do the request, get the response
        if($next != null)
            $response = $next($request);

        // Set or forget the cookie if the state changed
        if($next != null && (($this->getSessionTokenCookie($request) == null) != ($session == null))) {
            if($session == null)
                $response->withCookie(cookie()->forget(self::AUTH_COOKIE));
            else
                $response->withCookie(cookie(self::AUTH_COOKIE, $session->token, 60));
        }

        // Return the response
        return $response;
    }
}
