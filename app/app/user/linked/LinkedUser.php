<?php

namespace app\user\meta;

use app\database\Database;
use app\user\User;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class LinkedUser {

    /** @var int The linked user ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Linked user ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the linked user ID.
     *
     * @return int The linked user ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific linked user.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list users with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . LinkedUserManager::getDatabaseTableName() . ' WHERE linked_id=:linked_id');
        $statement->bindParam(':linked_id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the user ID of the owner.
     *
     * @return int User ID of the owner.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getOwnerId() {
        return $this->getDatabaseValue('linked_owner_user_id');
    }

    /**
     * Get the user.
     *
     * @return User Linked User user.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getOwner() {
        return new User($this->getOwnerId());
    }

    /**
     * Get the user ID of the linked user.
     *
     * @return int User ID of the linked user.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getUserId() {
        return $this->getDatabaseValue('linked_owner_user_id');
    }

    /**
     * Get the linked user.
     *
     * @return User Linked user.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getUser() {
        return new User($this->getUserId());
    }

    /**
     * Get the raw linked user creation date.
     *
     * @return string Raw linked user creation date.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getCreationDateTimeRaw() {
        return $this->getDatabaseValue('linked_creation_datetime');
    }

    /**
     * Get the linked user creation date.
     *
     * @return DateTime Linked user creation date.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getCreationDateTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getCreationDateTimeRaw());
    }

    /**
     * Get the raw linked user last usage date.
     *
     * @return DateTime Raw linked user last usage date.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getLastUsageDateTimeRaw() {
        return $this->getDatabaseValue('linked_usage_datetime');
    }

    /**
     * Get the linked user last usage date.
     *
     * @return DateTime Linked user last usage date.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getLastUsageDateTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getLastUsageDateTimeRaw());
    }

    /**
     * Delete this linked user.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function delete() {
        // Prepare a query for the linked user being deleted
        $statement = Database::getPDO()->prepare('DELETE FROM ' . LinkedUserManager::getDatabaseTableName() . ' WHERE linked_id=:linked_id');
        $statement->bindValue(':linked_id', $this->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }
}
