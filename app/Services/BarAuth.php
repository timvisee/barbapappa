<?php

namespace App\Services;

use App\Session;
use Illuminate\Foundation\Application;

class BarAuth {

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
    }

    /**
     * Update the authentication state.
     * If a session is given, the user is successfully authenticated.
     *
     * @param Session|null $session
     * @param bool $mailVerified True if any of the mail addresses of the user is verified, false if not.
     */
    public function updateState(Session $session, $mailVerified) {
        $this->session = $session;
        $this->mailVerified = $mailVerified;
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