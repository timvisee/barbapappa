<?php

namespace app\user\linked;

use app\config\Config;
use app\database\Database;
use app\session\SessionManager;
use app\user\User;
use app\user\UserManager;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class LinkedUserManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'user_linked';

    /**
     * Get the database table name of the linked users.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get a list of all linked users.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array All linked users.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getLinkedUsers() {
        // Build a query to select the users
        $query = 'SELECT linked_id FROM ' . static::getDatabaseTableName();

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // The list of linked users
        $linkedUsers = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $linkedUsers[] = new LinkedUser($data['linked_id']);

        // Return the list of linked users
        return $linkedUsers;
    }

    /**
     * Get the number of linked user.
     *
     * @return int Number of linked users.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getLinkedUserCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT linked_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any linked user with the specified ID.
     *
     * @param int $id The ID of the linked user to check for.
     *
     * @return bool True if any linked user exists with this ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isLinkedUserWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid user ID.');

        // Prepare a query for the database to list users with this ID
        $statement = Database::getPDO()->prepare('SELECT linked_id FROM ' . static::getDatabaseTableName() . ' WHERE linked_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any user found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Get all linked users for a specific owner.
     *
     * @param User $owner [optional] The owner user instance, or the user ID of the owner. Null to use the current logged in user.
     *
     * @return Array All linked users for this owner.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getLinkedUsersForOwner($owner = null) {
        // Parse the owner ID
        if($owner instanceof User)
            $userId = $owner->getId();

        else if(is_numeric($owner)) {
            // Get the user ID
            $userId = $owner;

            // Make sure the user ID is valid
            if(!UserManager::isUserWithId($userId))
                throw new Exception('Unknown user ID.');

        } else if($owner === null && SessionManager::isLoggedIn())
            $userId = SessionManager::getLoggedInUser()->getId();

        else
            throw new Exception('Invalid user instance.');

        // Prepare a query for the database to list the linked users
        $statement = Database::getPDO()->prepare('SELECT linked_id FROM ' . static::getDatabaseTableName() .
            ' WHERE linked_owner_user_id=:owner_user_id');
        $statement->bindValue(':owner_user_id', $userId, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // The list of linked users
        $linkedUsers = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $linkedUsers[] = new LinkedUser($data['linked_id']);

        // Return the list of linked users
        return $linkedUsers;
    }

    /**
     * Get the number of linked users for this specific owner.
     *
     * @param User $owner [optional] The owner user instance, or the user ID of the owner. Null to use the current logged in user.
     *
     * @return int Number of linked users.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getLinkedUsersForOwnerCount($owner = null) {
        // Parse the owner ID
        if($owner instanceof User)
            $userId = $owner->getId();

        else if(is_numeric($owner)) {
            // Get the user ID
            $userId = $owner;

            // Make sure the user ID is valid
            if(!UserManager::isUserWithId($userId))
                throw new Exception('Unknown user ID.');

        } else if($owner === null && SessionManager::isLoggedIn())
            $userId = SessionManager::getLoggedInUser()->getId();

        else
            throw new Exception('Invalid user instance.');

        // Prepare a query for the database to list the linked users
        $statement = Database::getPDO()->prepare('SELECT linked_id FROM ' . static::getDatabaseTableName() .
            ' WHERE linked_owner_user_id=:owner_user_id');
        $statement->bindValue(':owner_user_id', $userId, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the number of linked users
        return $statement->rowCount();
    }

    /**
     * Get all linked users for a specific user.
     *
     * @param User $user [optional] The user to get the linked users for. Null to use the current logged in user.
     *
     * @return Array All linked users for this user.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getLinkedUsersForUser($user = null) {
        // Parse the owner ID
        if($user instanceof User)
            $userId = $user->getId();

        else if(is_numeric($user)) {
            // Get the user ID
            $userId = $user;

            // Make sure the user ID is valid
            if(!UserManager::isUserWithId($userId))
                throw new Exception('Unknown user ID.');

        } else if($user === null && SessionManager::isLoggedIn())
            $userId = SessionManager::getLoggedInUser()->getId();

        else
            throw new Exception('Invalid user instance.');

        // Prepare a query for the database to list the linked users
        $statement = Database::getPDO()->prepare('SELECT linked_id FROM ' . static::getDatabaseTableName() .
            ' WHERE linked_user_id=:linked_user_id');
        $statement->bindValue(':linked_user_id', $userId, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // The list of linked users
        $linkedUsers = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $linkedUsers[] = new LinkedUser($data['linked_id']);

        // Return the list of linked users
        return $linkedUsers;
    }

    /**
     * Get the number of linked users for a specific user.
     *
     * @param User $user [optional] The user to get the linked users for. Null to use the current logged in user.
     *
     * @return int Number of linked users.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getLinkedUsersForUserCount($user = null) {
        // Parse the owner ID
        if($user instanceof User)
            $userId = $user->getId();

        else if(is_numeric($user)) {
            // Get the user ID
            $userId = $user;

            // Make sure the user ID is valid
            if(!UserManager::isUserWithId($userId))
                throw new Exception('Unknown user ID.');

        } else if($user === null && SessionManager::isLoggedIn())
            $userId = SessionManager::getLoggedInUser()->getId();

        else
            throw new Exception('Invalid user instance.');

        // Prepare a query for the database to list the linked users
        $statement = Database::getPDO()->prepare('SELECT linked_id FROM ' . static::getDatabaseTableName() .
            ' WHERE linked_user_id=:linked_user_id');
        $statement->bindValue(':linked_user_id', $userId, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the number of linked users
        return $statement->rowCount();
    }

    /**
     * Check whether there is a linked user for a specific owner and user.
     *
     * @param User $owner [optional] The owner, owner ID or null to use the current logged in user.
     * @param User $user [optional] The user, user ID or null to use the current logged in user.
     *
     * @return int Number of linked users.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function hasLinkedUser($owner = null, $user = null) {
        // Parse the owner ID
        if($owner instanceof User)
            $ownerId = $owner->getId();

        else if(is_numeric($owner)) {
            // Get the user ID
            $ownerId = $owner;

            // Make sure the user ID is valid
            if(!UserManager::isUserWithId($ownerId))
                throw new Exception('Unknown user ID.');

        } else if($owner === null && SessionManager::isLoggedIn())
            $ownerId = SessionManager::getLoggedInUser()->getId();

        else
            throw new Exception('Invalid user instance.');

        // Parse the owner ID
        if($user instanceof User)
            $userId = $user->getId();

        else if(is_numeric($user)) {
            // Get the user ID
            $userId = $user;

            // Make sure the user ID is valid
            if(!UserManager::isUserWithId($userId))
                throw new Exception('Unknown user ID.');

        } else if($user === null && SessionManager::isLoggedIn())
            $userId = SessionManager::getLoggedInUser()->getId();

        else
            throw new Exception('Invalid user instance.');

        // Prepare a query for the database to list the linked users
        $statement = Database::getPDO()->prepare('SELECT linked_id FROM ' . static::getDatabaseTableName() .
            ' WHERE linked_owner_user_id=:linked_owner_user_id AND linked_user_id=:linked_user_id');
        $statement->bindValue(':linked_owner_user_id', $ownerId, PDO::PARAM_INT);
        $statement->bindValue(':linked_user_id', $userId, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->rowCount() > 0;
    }

    /**
     * Create a new linked user.
     *
     * @param User $owner The owner.
     * @param User $user The user.
     *
     * @return LinkedUser The created user as object.
     *
     * @throws Exception throws if an error occurred.
     */
    public static function createLinkedUser($owner, $user) {
        // Make sure the owner and user are valid
        if(!($owner instanceof User) || !($user instanceof User))
            throw new Exception('Invalid user instance.');

        // Determine the creation date and time
        $dateTime = DateTime::now();

        // Prepare a query for for the inventory being added
        $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
            ' (linked_owner_user_id, linked_user_id, linked_creation_datetime) ' .
            'VALUES (:linked_owner_user_id, :linked_user_id, :linked_creation_datetime)');
        $statement->bindValue(':linked_owner_user_id', $owner->getId(), PDO::PARAM_INT);
        $statement->bindValue(':linked_user_id', $user->getId(), PDO::PARAM_INT);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':linked_creation_datetime', $dateTime->toString(), PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Get and return the linked user instance
        return new LinkedUser(Database::getPDO()->lastInsertId());
    }
}