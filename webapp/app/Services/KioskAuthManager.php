<?php

namespace App\Services;

use App\Services\Auth\Kiosk\Authenticator;
use App\Services\Auth\Kiosk\AuthState;
use Illuminate\Foundation\Application;

class KioskAuthManager {

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
     * KioskAuthManager constructor.
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
     * Get the bar for this kiosk session.
     *
     * If the session isn't authenticated, null is returned.
     *
     * @return \App\Models\Bar|null
     */
    public function getBar() {
        return $this->authState->getBar();
    }
}
