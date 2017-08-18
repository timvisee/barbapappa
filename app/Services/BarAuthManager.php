<?php

namespace App\Services;

use App\Services\Auth\Authenticator;
use App\Services\Auth\AuthState;
use App\User;
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
     * @return User|null
     */
    public function getUser() {
        return $this->authState->getUser();
    }

    /**
     * Check whether any of the mail addresses of the user is verified.
     *
     * @return bool True if any of the mail addresses is verified, false if not.
     */
    public function isVerified() {
        return $this->authState->isVerified();
    }

    /**
     * Get the authenticator instance.
     *
     * @return Authenticator Authenticator instance.
     */
    public function getAuthenticator() {
        return $this->authenticator;
    }
}