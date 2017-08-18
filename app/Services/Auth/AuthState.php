<?php

namespace App\Services\Auth;

use App\Session;
use App\User;

class AuthState {

    /**
     * @var Session|null Session instance.
     */
    private $session;

    /**
     * Define whether any of the mail addresses of the user is verified.
     * @var bool True if verified, false if not.
     */
    private $emailVerified;

    /**
     * AuthState constructor.
     *
     * @param Session|null $session=null The user session, or null.
     * @param bool $emailVerified=false True if the user has any verified email address, false if not.
     */
    public function __construct(Session $session = null, $emailVerified = false) {
        $this->session = $session;
        $this->emailVerified = $emailVerified;
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
        // The user must be authenticated
        if(!$this->isAuth())
            return false;

        return $this->emailVerified;
    }
}