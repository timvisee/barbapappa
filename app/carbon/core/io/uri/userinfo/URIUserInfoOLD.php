<?php

/**
 * URIUserInfo.php
 * The URI user info class.
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
 * @package carbon\core\io\uri\userinfo
 * @author Tim Visee
 */
class URIUserInfoOLD {

    /** @var string|null The user info. */
    private $info;

    /**
     * Constructor.
     *
     * @param string|null $userInfo The user info to set, or null.
     */
    public function __construct($userInfo) {
        // Set the user info
        // TODO: Add some error validation!?
        $this->setUserInfo($userInfo, false);
    }

    /**
     * Get the user info as a string.
     *
     * @param bool $validate [optional] True to validate the user info, false to skip the validation.
     *
     * @return string|null The user info as a string, or null if not user info was set. Null will also be returned if
     * the user info isn't valid when validation is used.
     */
    public function getUserInfo($validate = false) {
        // Validate the user info if needed
        if($validate)
            if(!$this->isValid())
                return null;

        // Get the user info
        $userInfo = $this->toString();

        // Return the user info
        if(strlen($userInfo) == 0)
            return null;
        return $userInfo;
    }

    /**
     * Set the user info.
     *
     * @param string|URIUserInfo|null $userInfo The user info as a string, or as an user info instance. Null to reset
     * the user info.
     * @param bool $validate [optional] True to make sure the user info is valid before it's set, false otherwise.
     * Null will also count as valid user info since this resets the user info.
     *
     * @return bool True if the user info was set, false otherwise. True will also be returned if the user info was
     * reset.
     */
    public function setUserInfo($userInfo, $validate = false) {
        // Check whether the user info should be reset
        if($userInfo === null) {
            $this->info = null;
            return true;
        }

        // Get the user info as a string, and make sure it's valid
        if(($userInfo = URIUserInfoHelper::asString($userInfo, $validate, null)) === null)
            return false;

        // Set the user info, return the result
        $this->info = $userInfo;
        return true;
    }

    /**
     * Get the user info as a string.
     *
     * @return string The user info as a string. An empty string will be returned if no user info is set.
     */
    public function toString() {
        // Make sure the user info is set
        if($this->info === null)
            return '';

        // Return the user info
        return $this->info;
    }

    /**
     * Validate the user info.
     *
     * @return bool True if the user info is valid, false otherwise.
     */
    public function isValid() {
        return URIUserInfoHelper::isValid($this);
    }
}