<?php

namespace App\Services\Auth\Kiosk;

use App\Models\Bar;
use App\Models\KioskSession;

class AuthState {

    /**
     * The kiosk session.
     * @var KioskSession|null Kiosk session instance.
     */
    private $session;

    /**
     * Bar cache.
     * @var Bar|null Bar instance.
     */
    private $bar;

    /**
     * AuthState constructor.
     *
     * @param KioskSession|null $session=null The kiosk session, or null.
     */
    public function __construct($session = null) {
        // Set the session
        $this->setSession($session);
    }

    /**
     * Get the session.
     *
     * @return KioskSession|null
     */
    public function getSession() {
        return $this->session;
    }

    /**
     * Set the session.
     *
     * @param KioskSession|null $session The session, or null to reset it.
     */
    private function setSession($session) {
        // Clear caches
        $this->bar = null;

        // Set the session
        $this->session = $session;
    }

    /**
     * Get the bar for this kiosk session.
     *
     * If the session isn't authenticated, null is returned.
     *
     * @return Bar|null
     */
    public function getBar() {
        // // We must be authenticated
        // if(!$this->isAuth())
        //     return null;

        // Return a cached bar
        if($this->bar !== null)
            return $this->bar;

        // The session must not be null
        if($this->session == null)
            return null;

        // Query the session bar, cache and return it
        return $this->bar = $this->session->bar()->firstOrFail();
    }

    /**
     * Check whether the kiosk is authenticated.
     *
     * @return bool True if the kiosk is authenticated, false if not.
     */
    public function isAuth() {
        return $this->session != null
            && $this->session instanceof KioskSession
            && $this->getBar() != null;
    }
}
