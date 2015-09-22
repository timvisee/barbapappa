<?php

namespace app\user;

use app\config\Config;
use app\database\Database;
use app\language\LanguageManager;
use app\mail\MailManager;
use app\mail\verification\MailVerification;
use app\mail\verification\MailVerificationManager;
use app\registry\Registry;
use app\util\AccountUtils;
use carbon\core\datetime\DateTime;
use carbon\core\hash\Hash;
use carbon\core\util\IpUtils;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class UserManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'user';

    /** Registry key to define if users are allowed to login with their username. */
    const REG_ACCOUNT_LOGIN_ALLOW_USERNAME = 'account.login.allowUsername';
    /** Registry key to define if users are allowed to login with their mail address. */
    const REG_ACCOUNT_LOGIN_ALLOW_MAIL = 'account.login.allowMail';

    /**
     * Get the database table name of the users.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Parse a user instance.
     * Alias of User::parse();
     *
     * Valid instances:
     * - User instance.
     * - User ID as int.
     *
     * @param User|int $user The user instance, or the user ID as int.
     * @param mixed|null $default [optional] The default value returned if the user instance is invalid
     *
     * @return User|mixed The user instance or the default value if the user instance isn't valid.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function parse($user, $default = null) {
        return User::parse($user, $default);
    }

    /**
     * Get a list of all users.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array All users.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getUsers() {
        // Build a query to select the users
        $query = 'SELECT user_id FROM ' . static::getDatabaseTableName();

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // The list of users
        $users = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $users[] = new User($data['user_id']);

        // Return the list of users
        return $users;
    }

    /**
     * Get the number of users.
     *
     * @return int Number of users.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getUserCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT user_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any user with the specified ID.
     *
     * @param int $id The ID of the user to check for.
     *
     * @return bool True if any user exists with this ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isUserWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid user ID.');

        // Prepare a query for the database to list users with this ID
        $statement = Database::getPDO()->prepare('SELECT user_id FROM ' . static::getDatabaseTableName() . ' WHERE user_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any user found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Get the user with a username.
     *
     * @param string $username The username.
     *
     * @return User|null The user with this username, or null if there's no user with this username.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getUserWithUsername($username) {
        // Make sure the username is valid
        if(!AccountUtils::isValidUsername($username))
            return false;

        // Trim the username
        $username = trim($username);

        // Prepare a query for the database to list users with this ID
        $statement = Database::getPDO()->prepare('SELECT user_id FROM ' . static::getDatabaseTableName() . ' WHERE user_username LIKE :user_username');
        $statement->bindValue(':user_username', $username, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the user, or null if no user was found
        if($statement->rowCount() > 0)
            return new User($statement->fetch(PDO::FETCH_ASSOC)['user_id']);
        return null;
    }

    /**
     * Check whether there is a user with a specific username.
     *
     * @param string $username The username to check for.
     *
     * @return bool True if there is an user with this username.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isUserWithUsername($username) {
        return static::getUserWithUsername($username) instanceof User;
    }

    /**
     * Generate a random unique user ID.
     *
     * @return int User id.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function generateNewUserId() {
        // Generate a new, unique user ID
        while(true) {
            // Generate a new random user ID
            $userId = mt_rand(1, mt_getrandmax());

            // Return this ID if it's unique
            if(!static::isUserWithId($userId))
                return $userId;
        }
        return null;
    }

    /**
     * Create a new user.
     *
     * @param string $username The username.
     * @param string $password The password.
     * @param string $mail The mail.
     * @param string $nameFull The full name.
     *
     * @return User The created user as object.
     *
     * @throws Exception throws if an error occurred.
     */
    // TODO: Properly add full names with accents and such!
    public static function createUser($username, $password, $mail, $nameFull) {
        // Make sure the username is valid
        if(!AccountUtils::isValidUsername($username))
            throw new Exception('The username is invalid.');

        // Make sure the username is unique
        if(static::isUserWithUsername($username))
            throw new Exception('This username already exists.');

        // Make sure the mail is valid
        if(!AccountUtils::isValidMail($mail))
            throw new Exception('The mail is invalid.');

        // Make sure the mail is unique
        // TODO: Also check in the mail verification table!
        if(MailManager::isMailWithMail($mail))
            throw new Exception('The mail already exists.');

        // Make sure the full name is valid
        if(!AccountUtils::isValidFullName($nameFull))
            throw new Exception('The full name is invalid.');

        // Generate a random user ID
        $userId = UserManager::generateNewUserId();

        // Generate a user salt
        $userSalt = Hash::generateSalt();

        // Get the password hash
        $passwordHash = User::generatePasswordHash($password, $userSalt);

        // Determine the creation date time
        $createDateTime = DateTime::now();

        // Get the user IP
        $ip = IpUtils::getClientIp();

        // Prepare a query for the picture being added
        $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
            ' (user_id, user_username, user_pass_hash, user_hash_salt, user_create_datetime, user_create_ip, user_name_full) ' .
            'VALUES (:user_id, :user_username, :user_pass_hash, :user_hash_salt, :user_create_datetime, :user_create_ip, :user_name_full)');
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':user_username', $username, PDO::PARAM_STR);
        $statement->bindValue(':user_pass_hash', $passwordHash, PDO::PARAM_STR);
        $statement->bindValue(':user_hash_salt', $userSalt, PDO::PARAM_STR);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':user_create_datetime', $createDateTime->toString(), PDO::PARAM_STR);
        $statement->bindValue(':user_create_ip', $ip, PDO::PARAM_STR);
        $statement->bindValue(':user_name_full', $nameFull, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Get the user instance
        $user = new User($userId);

        // Start the verification process for the mail for this user
        MailVerificationManager::createMailVerification($user, $mail, null);

        // Set the preferred language of the user to the currently selected language
        LanguageManager::setUserLanguageTag(LanguageManager::getPreferredLanguage(), $user);

        // Return the created user as object
        return $user;
    }

    /**
     * Suggest a new username based on a full name, that has not been registered yet.
     *
     * @param string $name The full name.
     *
     * @return string The suggested username.
     */
    public static function getUsernameSuggestionByName($name) {
        // Trim the name
        $name = trim($name);

        // Translate special characters
        $name = iconv('utf-8','ASCII//IGNORE//TRANSLIT', $name);

        // Remove whitespaces
        $name = preg_replace('/\s+/', '', $name);

        // Remove all non-allowed characters
        $name = preg_replace('/[^A-Za-z0-9_]/', '', $name);

        // Lowercase the string
        $name = strtolower($name);

        // Return the suggested name if it's available
        if(!static::isUserWithUsername($name))
            return $name;

        // Put numbers behind the username until we get one that is available
        for($i = 1; $i < 100; $i++) {
            // Generate the username
            $newName = $name . $i;

            // Return the suggested name if it's available
            if(!static::isUserWithUsername($newName))
                return $newName;
        }

        // Generate random names until once is available
        while(true) {
            // Generate a random username
            $newName = substr(md5(mt_rand(0, mt_getrandmax())), 0, AccountUtils::USERNAME_LENGTH_MAX);

            // Return the suggested name if it's available
            if(!static::isUserWithUsername($newName))
                return $newName;
        }
        return null;
    }

    /**
     * Validate the login credentials for a user.
     *
     * @param string $loginUser The login user.
     * @param string $loginPassword The login password.
     *
     * @return User|null The user that logged in, or null if the user credentials were invalid.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function validateLogin($loginUser, $loginPassword) {
        // Define a variable to get the user
        $user = null;

        // Check whether a user exists with this username
        if(Registry::getValue(static::REG_ACCOUNT_LOGIN_ALLOW_USERNAME)->getBoolValue() && UserManager::isUserWithUsername($loginUser))
            $user = UserManager::getUserWithUsername($loginUser);

        elseif(Registry::getValue(static::REG_ACCOUNT_LOGIN_ALLOW_MAIL)->getBoolValue() && AccountUtils::isValidMail($loginUser)) {
            // Check whether this mail is registered and verified
            if(MailManager::isMailWithMail($loginUser)) {
                // Get the mail of the user
                $mail = MailManager::getMailWithMail($loginUser);

                // Get the corresponding user if valid
                if($mail !== null)
                    $user = $mail->getUser();

            } else {
                // Get all mails waiting for verification for this user
                $mails = MailVerificationManager::getMailVerificationsWithMail($loginUser);

                // Get the user of the unverified mail if one address is returned
                if(sizeof($mails) == 1) {
                    // Get the mail verification
                    $mailVerification = $mails[0];

                    // Validate the instance and get the user
                    if($mailVerification instanceof MailVerification)
                        $user = $mailVerification->getUser();
                }
            }
        }

        // Make sure a user is found and validate the password
        if(!($user instanceof User) || !$user->isPassword($loginPassword))
            return null;

        // Return the user
        return $user;
    }
}