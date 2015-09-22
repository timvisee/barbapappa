<?php

namespace app\user\permission;

use app\user\User;
use app\user\UserManager;
use Exception;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class UserPermission {

    /** @var User|null The user instance. */
    private $user = null;

    /**
     * Constructor.
     *
     * @param User|int $user The user, or the user ID as integer.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function __construct($user) {
        // Set the user instance
        $this->setUser($user);

        // TODO: Fetch the user permissions?
    }

    /**
     * Get the user instance.
     *
     * @return User|null The user instance, or null if the user is invalid.
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set the user instance.
     *
     * @param User $user The user instance.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function setUser($user) {
        // Parse the user and make sure it's valid
        if(($user = UserManager::parse($user, null)) === null)
            throw new Exception('Invalid user instance.');

        // Set the user instance
        $this->user = $user;
    }
}