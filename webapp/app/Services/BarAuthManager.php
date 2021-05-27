<?php

namespace App\Services;

use App\Services\Auth\Authenticator;
use App\Services\Auth\AuthState;
use Illuminate\Foundation\Application;

class BarAuthManager {

    /**
     * Application instance.
     * @var Application
     */
    private $app;

    /**
     * Authentication state.
     * @type AuthState
     */
    private $authState;

    /**
     * Authenticator instance, used for authentication.
     * @var Authenticator
     */
    private $authenticator;

    /**
     * BarAuthManager constructor.
     *
     * @param Application $app Application instance.
     * @param bool $authRequest=true True to immediately authenticate the current request by a session cookie, false if not.
     */
    public function __construct(Application $app, $authRequest = true) {
        $this->app = $app;

        // Define the authentication state and authenticator
        $this->authState = new AuthState();
        $this->authenticator = new Authenticator();

        // Authenticate the request
        if($authRequest)
            $this->authState = $this->authenticator->authRequest()->getAuthState();
    }

    /**
     * Get the authentication state.
     *
     * @return AuthState Authentication state.
     */
    public function getAuthState() {
        return $this->authState;
    }

    /**
     * Set the authentication state.
     *
     * @param AuthState $authState Authentication state.
     */
    public function setAuthState(AuthState $authState) {
        $this->authState = $authState;
    }

    /**
     * Get the authenticator instance.
     *
     * @return Authenticator Authenticator instance.
     */
    public function getAuthenticator() {
        return $this->authenticator;
    }

    /**
     * Check whether the user is authenticated.
     *
     * @return bool True if the user is authenticated, false if not.
     */
    public function isAuth() {
        return $this->authState->isAuth();
    }

    /**
     * Get the current user.
     * This returns the current user.
     * If a linked user account is currently selected a different user than the session owner might be returned.
     *
     * If the user isn't authenticated, null is returned.
     *
     * @return \App\Models\User|null
     */
    public function getUser() {
        return $this->authState->getUser();
    }

    /**
     * Get the session user.
     * This returns the user that is owner of the current session.
     * Even if a linked account is currently selected, this always returns the user of the initial session.
     *
     * If the user isn't authenticated, null is returned.
     *
     * @return \App\Models\User|null
     */
    public function getSessionUser() {
        return $this->authState->getSessionUser();
    }

    /**
     * Check whether any of the mail addresses of the user is verified.
     *
     * @return bool True if any of the mail addresses is verified, false if not.
     */
    public function isVerified() {
        return $this->authState->isVerified();
    }
}
