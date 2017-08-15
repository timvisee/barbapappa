<?php

/**
 * URI.php
 * The URI class, which is used to manage URI's.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Tim Visee 2015. All rights reserved.
 */

namespace carbon\core\io\uri\userinfo;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * URI user info class.
 *
 * @package carbon\core\io
 * @author Tim Visee
 */
class URIUserInfo {

    /** @var string|null The user of the URI or null. */
    protected $user;
    /** @var string|null The password of the URI or null. */
    protected $password;

    /**
     * Constructor.
     *
     * @param string|null $user The URI user or null.
     * @param string|null $password The URI password or null.
     */
    public function __construct($user, $password) {
        // Set the URI segments
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Get the user of the URI if set.
     *
     * @return string|null The user of the URI or null.
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set the user of the URI.
     *
     * @param string|null $user The user of the URI or null.
     */
    public function setUser($user) {
        $this->user = $user;
    }

    /**
     * Check whether the URI has any user set.
     *
     * @return bool True if the URI has any user set, false if not.
     */
    public function hasUser() {
        return ($this->user !== null);
    }

    /**
     * Get the password of the URI if set.
     *
     * @return string|null The password of the URI or null.
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set the password of the URI.
     *
     * @param string|null $password The password of the URI or null.
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Check whether the URI has any password set.
     *
     * @return bool True if the URI has any password set, false if not.
     */
    public function hasPassword() {
        return ($this->password !== null);
    }

    /**
     * Get the user info as a string.
     *
     * @return string The user info as a string, false on failure.
     */
    public function getUserInfo() {
        // Make sure there's any user info
        if(!$this->hasUserInfo())
            return null;

        // Build the user info string
        $userInfo = '';

        // Set the user
        if($this->hasUser())
            $userInfo .= $this->getUser();

        // Set the password
        if($this->hasPassword())
            $userInfo .= ':' . $this->getPassword();

        // Return the user info
        return $userInfo;
    }

    /**
     * Check whether this instance has any user info set.
     *
     * @return bool True if any user info was set, false if not.
     */
    public function hasUserInfo() {
        return $this->hasUser() || $this->hasPassword();
    }

    /**
     * Get the user info as a string.
     *
     * @return string The user info, or an empty string if no user info was set.
     */
    public function toString() {
        // Get the user info, make sure it's valid
        $userInfo = $this->getUserInfo();
        if($userInfo === null)
            return '';

        // Return the user info
        return $userInfo;
    }

    /**
     * Get the user info as a string.
     *
     * @return string The user info, or an empty string if no user info was set.
     */
    public function __toString() {
        return $this->toString();
    }
}
