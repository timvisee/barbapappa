<?php

/**
 * AccountUtils.php
 * Utilities class for account related things.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2013, All rights reserved.
 */

namespace app\util;

// Prevent direct requests to this set_file due to security reasons
defined('APP_INIT') or die('Access denied!');

/**
 * AccountUtils class.
 *
 * @package app\util
 * @author Tim Visee
 */
class AccountUtils {

    /** Username minimum length. */
    const USERNAME_LENGTH_MIN = 4;
    /** Username maximum length. */
    const USERNAME_LENGTH_MAX = 32;
    /** Password minimum length. */
    const PASSWORD_LENGTH_MIN = 4;
    /** Password maximum length. */
    const PASSWORD_LENGTH_MAX = 64;

    /**
     * Validate a username.
     *
     * @param string $username The username to validate.
     *
     * @return bool True if the username is valid, false otherwise.
     */
    public static function isValidUsername($username) {
        // Make sure allowed characters are used
        if(preg_match('/^[A-Za-z0-9_]+$/', $username) !== 1)
            return false;

        // Determine the username length
        $length = strlen($username);

        // Make sure the length is valid
        return ($length >= static::USERNAME_LENGTH_MIN && $length <= static::USERNAME_LENGTH_MAX);
    }

    /**
     * Validate a mail.
     *
     * @param string $mail The mail to validate.
     *
     * @return bool True if the mail is valid, false otherwise.
     */
    public static function isValidMail($mail) {
        return (bool) filter_var($mail, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate a full name.
     *
     * @param string $fullName The full name to validate.
     *
     * @return bool True if the full name is valid, false otherwise.
     */
    public static function isValidFullName($fullName) {
        // Make sure the name is a string
        if(!is_string($fullName))
            return false;

        // Trim the name
        $fullName = trim($fullName);

        // Make sure the name doesn't contain any numbers
        if(preg_match('/[0-9]+/', $fullName))
            return false;

        // Make sure the name is at least 3 chars long
        return strlen($fullName) >= 3;
    }

    /**
     * Validate a password.
     *
     * @param string $password The password to validate.
     *
     * @return bool True if the password is valid, false otherwise.
     */
    public static function isValidPassword($password) {
        // Make sure the password is a string
        if(!is_string($password))
            return false;

        // Determine the password length
        $length = strlen($password);

        // Make sure the length is valid
        return ($length >= static::PASSWORD_LENGTH_MIN && $length <= static::PASSWORD_LENGTH_MAX);
    }
}