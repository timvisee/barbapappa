<?php

namespace App\Traits;

/**
 * A trait for models that have a password.
 *
 * TODO: rename this to has code?
 * TODO: force that this is implemented on Eloquent models
 */
trait HasPassword {

    /**
     * Check whether this community has a password specified.
     *
     * @return bool True if specified, false if not or if empty.
     */
    public function hasPassword() {
        return !empty($this->password);
    }

    /**
     * Check whether the given password is correct.
     *
     * Note: this always tries to compare and doesn't check whether a password
     * is required.
     *
     * @param string $password The password to check.
     * @return boolean True if the password is correct, false if not.
     */
    public function isPassword($password) {
        return $this->password == $password;
    }

    /**
     * Check whether the given user needs a password to join this community.
     *
     * @return bool True if a password is required, false if not.
     */
    public function needsPassword($user) {
        // There must be a password
        if(!$this->hasPassword())
            return false;

        // TODO: some password determining logic here

        return true;
    }
}
