<?php

namespace app\user\meta;

use app\database\Database;
use app\user\User;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class UserMeta {

    /** @var int The user meta ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id User meta ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the user meta ID.
     *
     * @return int The user meta ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific user meta.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list users with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . UserMetaManager::getDatabaseTableName() . ' WHERE user_meta_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the user ID.
     *
     * @return int User meta user ID.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getUserId() {
        return $this->getDatabaseValue('user_meta_user_id');
    }

    /**
     * Get the user.
     *
     * @return User User meta user.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getUser() {
        return new User($this->getUserId());
    }

    /**
     * Get the key.
     *
     * @return string User meta key.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getKey() {
        return $this->getDatabaseValue('user_meta_key');
    }

    /**
     * Get the value.
     *
     * @return string User meta value.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getValue() {
        return $this->getDatabaseValue('user_meta_value');
    }
}
