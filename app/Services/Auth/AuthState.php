<?php

namespace App\Services\Auth;

use App\Models\Session;
use App\Models\User;

class AuthState {

    /**
     * The user session.
     * @var Session|null Session instance.
     */
    private $session;

    /**
     * User cache.
     * @var User|null User instance.
     */
    private $user;

    /**
     * Session user cache.
     * @var User|null Session user instance.
     */
    private $sessionUser;

    /**
     * Define whether any of the mail addresses of the user is verified.
     * @var bool True if verified, false if not.
     */
    private $verified;

    /**
     * AuthState constructor.
     *
     * @param Session|null $session=null The user session, or null.
     * @param bool|null $verified=null True if the user has any verified email address, false if not. If null is given, the verified state is checked automatically.
     */
    public function __construct($session = null, $verified = null) {
        // Set the session
        $this->setSession($session);

        // Set the properties
        $this->verified = $verified;
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
     * Set the session.
     *
     * @param Session|null $session The session, or null to reset it.
     */
    private function setSession($session) {
        // Clear caches
        $this->user = null;
        $this->sessionUser = null;
        $this->verified = null;

        // Set the session
        $this->session = $session;

        \Debugbar::info("set session");
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
        // Return the session user for now
        // TODO: implement user switching
        return $this->getSessionUser();

        // // We must be authenticated
        // if(!$this->isAuth())
        //     return null;

        // // Return a cached user
        // if($this->user !== null)
        //     return $this->user;

        // // Query the session user, cache and return it
        // return $this->user = $this->session->user->firstOrFail();
    }

    /**
     * Get the session user.
     * If a linked user account is currently selected the session owner is
     * returned.
     *
     * If the user isn't authenticated, null is returned.
     *
     * @return User|null
     */
    public function getSessionUser() {
        // We must be authenticated
        if(!$this->isAuth())
            return null;

        // Return a cached user
        if($this->sessionUser !== null)
            return $this->sessionUser;

        // Query the session user, cache and return it
        return $this->sessionUser = $this->session->user()->firstOrFail();
    }

    /**
     * Check whether the user is authenticated.
     *
     * @return bool True if the user is authenticated, false if not.
     */
    public function isAuth() {
        return $this->getSession() !== null && $this->getSession() instanceof Session;
    }

    /**
     * Check whether any of the mail addresses of the user is verified.
     *
     * @return bool True if any of the mail addresses is verified, false if not.
     */
    public function isVerified() {
        // The user must be authenticated
        if(!$this->isAuth())
            return false;

        // Return cache
        if($this->verified !== null)
            return $this->verified;

        // Query the state, cache and return it
        return $this->verified = $this->getUser()->hasVerifiedEmail();
    }
}
