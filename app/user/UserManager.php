<?php

namespace app\user;

use app\config\Config;
use app\database\Database;
use carbon\core\datetime\DateTime;
use carbon\core\hash\Hash;
use carbon\core\util\IpUtils;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class UserManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'users';

    /**
     * Get the database table name of the users.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
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
    // TODO: Add the user mail, and send an approval message!
    // TODO: Validate the username, password, mail and name!
    public static function createUser($username, $password, $mail, $nameFull) {
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
            'VALUES (:user_id, :user_username, :user_pass_hash, :user_hash_salt, :user_create_datetime, :user_create_ip, :user_name_full');
        $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $statement->bindValue(':user_username', $username, PDO::PARAM_STR);
        $statement->bindValue(':user_pass_hash', $passwordHash, PDO::PARAM_STR);
        $statement->bindValue(':user_hash_salt', $userSalt, PDO::PARAM_STR);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':user_create_datetime', $createDateTime, PDO::PARAM_STR);
        $statement->bindValue(':user_create_ip', $ip, PDO::PARAM_STR);
        $statement->bindValue(':user_name_full_hash', $nameFull, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the created user as object
        return new User($userId);
    }
}