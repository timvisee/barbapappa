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

    /**
     * Get the user meta for a specific user and key.
     *
     * @param User $user The meta user.
     * @param string $key The meta key.
     *
     * @return UserMeta|null The user meta as object, or null if no meta was found.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getUserMeta($user, $key) {
        // Trim the key
        $key = trim($key);

        // Make sure a value exists for this user with this key
        if(!static::isUserMetaWithKey($user, $key))
            return null;

        // Prepare a query for the database to list users with this ID
        $statement = Database::getPDO()->prepare('SELECT user_meta_id FROM ' . UserManager::getDatabaseTableName() .
            ' WHERE user_meta_user_id=:user_id AND user_meta_key=:meta_key');
        $statement->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);
        $statement->bindValue(':meta_key', $key, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the meta as object
        return new UserMeta($statement->fetch(PDO::FETCH_ASSOC)['user_meta_id']);
    }

    /**
     * Set a meta value for a user. If the meta value doesn't exist yet, it will be created.
     *
     * @param User $user The user to set the value for.
     * @param string $key The meta key.
     * @param string $value The meta value.
     *
     * @return UserMeta The user meta as object.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function setUserMeta($user, $key, $value) {
        // Trim the key
        $key = trim($key);

        // Create this meta if it hasn't been set already
        if(!static::isUserMetaWithKey($user, $key)) {
            // Prepare a query for the meta being created
            $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
                ' (user_meta_user_id, user_meta_key, user_meta_value) VALUES (:user_id, :meta_key, :meta_value)');
            $statement->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);
            $statement->bindValue(':meta_key', $key, PDO::PARAM_STR);
            $statement->bindValue(':meta_value', $value, PDO::PARAM_STR);

            // Execute the prepared query
            if(!$statement->execute())
                throw new Exception('Failed to query the database.');

            // Return the created user meta as object
            return new UserMeta(Database::getPDO()->lastInsertId());
        }

        // Get the meta object
        $meta = static::getUserMeta($user, $key);

        // Set the value
        $meta->setValue($value);

        // Return the object
        return $meta;
    }

    /**
     * Check if there's any meta for a user with a specific key.
     *
     * @param User $user The user of the meta.
     * @param string $key The meta key.
     *
     * @return bool True if any meta with this key exists for this user.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isUserMetaWithKey($user, $key) {
        // Make sure the user instance is valid
        if(!($user instanceof User))
            throw new Exception('Invalid user.');

        // Trim the key
        $key = trim($key);

        // Make sure the meta key is valid
        if(strlen($key) <= 0)
            throw new Exception('Invalid meta key.');

        // Prepare a query for the database to list users with this ID
        $statement = Database::getPDO()->prepare('SELECT user_meta_id FROM ' . static::getDatabaseTableName() . ' WHERE user_meta_user_id=:user_id AND user_meta_key=:meta_key');
        $statement->bindParam(':user_id', $user->getId(), PDO::PARAM_INT);
        $statement->bindParam(':meta_key', $key, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any user found with this ID
        return $statement->rowCount() > 0;
    }
}