<?php

namespace app\user;

use app\config\Config;
use app\database\Database;
use app\user\meta\UserMeta;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class UserMetaManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'users_meta';

    /**
     * Get the database table name of the user meta.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get a list of all user meta.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array All user meta.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getUsers() {
        // Build a query to select the users
        $query = 'SELECT user_meta_id FROM ' . static::getDatabaseTableName();

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // The list of user meta
        $users = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $users[] = new UserMeta($data['user_meta_id']);

        // Return the list of user meta
        return $users;
    }

    /**
     * Get the number of user meta.
     *
     * @return int Number of user meta.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getUserMetaCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT user_meta_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any user meta with the specified ID.
     *
     * @param int $id The ID of the user meta to check for.
     *
     * @return bool True if any user meta exists with this ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isUserMetaWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid user ID.');

        // Prepare a query for the database to list users with this ID
        $statement = Database::getPDO()->prepare('SELECT user_meta_id FROM ' . static::getDatabaseTableName() . ' WHERE user_meta_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any user found with this ID
        return $statement->rowCount() > 0;
    }
}