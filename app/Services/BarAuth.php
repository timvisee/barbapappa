<?php

namespace App\Services;

use App\Session;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cookie;

class BarAuth {

    /**
     * The name of the authentication token cookie.
     */
    const AUTH_COOKIE = 'session_token';

    /**
     * Application instance.
     * @var Application
     */
    private $app;

    /**
     * @var Session|null Session instance.
     */
    private $session = null;

    /**
     * Define whether any of the mail addresses of the user is verified.
     * @var bool True if verified, false if not.
     */
    private $mailVerified = false;

    /**
     * BarAuth constructor.
     *
     * @param Application $app Application instance.
     */
    public function __construct(Application $app) {
        $this->app = $app;

        // Authenticate the session
        $this->authenticate();
    }

    /**
     * Authenticate.
     */
    private function authenticate() {
        // Reset the current state
        $this->session = null;
        $this->mailVerified = false;

        // Define whether to forget the authentication cookie
        $forget = false;

        // The session token cookie must exist
        if(!Cookie::has(self::AUTH_COOKIE))
            return;

        // Get the session token, it must be valid
        $sessionToken = Cookie::get(self::AUTH_COOKIE);
        if($sessionToken == null)
            $forget = true;

        else {
            // Find the user session, it must be valid and may not be expired
            $session = Session::where('token', '=', $sessionToken)
                ->first();
            if ($session == null || $session->isExpired())
                $forget = true;

            else {
                // Count the number of verified email accounts
                $verifiedMailCount = $session->user()->first()
                    ->emails()->where('verified_at', '!=', null)
                    ->count();

                // Set the session and mail verification state
                $this->session = $session;
                $this->mailVerified = $verifiedMailCount > 0;
            }
        }

        // Forget the authentication cookie
        if($forget)
            Cookie::forget(self::AUTH_COOKIE);
    }

    /**
     * Get the session.
     *
     * @return Session|null
     */
    public function getSession() {
        return $this->session;
    }

    /**
     * Get the current user.
     * This returns the current user.
     * If a linked user account is currently selected a different user than the session owner might be returned.
     *
     * If the user isn't authenticated, null is returned.
     *
     * @return User|null
     */
    public function getUser() {
        // The session must not be null
        if($this->session == null)
            return null;

        // Get the current user
        // TODO: Maybe this must be user_id
        return $this->session->user;
    }

    /**
     * Check whether the user is authenticated.
     *
     * @return bool True if the user is authenticated, false if not.
     */
    public function isAuth() {
        return $this->getSession() != null && $this->getSession() instanceof Session;
    }

    /**
     * Check whether any of the mail addresses of the user is verified.
     *
     * @return bool True if any of the mail addresses is verified, false if not.
     */
    public function isVerified() {
        return $this->mailVerified;
    }
}