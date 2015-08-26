<?php

namespace app\user;

use app\config\Config;
use app\database\Database;
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
    }
}