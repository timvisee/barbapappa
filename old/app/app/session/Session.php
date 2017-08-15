<?php

namespace app\session;

use app\database\Database;
use app\user\User;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class Session {

    /** @var int The session ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Session ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the session ID.
     *
     * @return int The session ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific session.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list sessions with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . SessionManager::getDatabaseTableName() . ' WHERE session_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the session user.
     *
     * @return User Session user.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getUser() {
        return new User($this->getDatabaseValue('session_user_id'));
    }

    /**
     * Get the key of this session.
     *
     * @return string Session key.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getKey() {
        return $this->getDatabaseValue('session_key');
    }

    /**
     * Get the session creation client IP.
     *
     * @return string Creation client IP.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getCreationIp() {
        return $this->getDatabaseValue('session_create_ip');
    }

    /**
     * Get the session creation date time.
     *
     * @return DateTime Creation date time.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getCreationDateTime() {
        return new DateTime($this->getDatabaseValue('session_create_datetime'));
    }

    /**
     * Get the session expiration date time.
     *
     * @return DateTime Expiration date time.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getSessionDateExpire() {
        return new DateTime($this->getDatabaseValue('session_expire_datetime'));
    }

    /**
     * Check whether the session is expired.
     *
     * @return bool True if the session is expired, false if not.
     */
    public function isExpired() {
        return !$this->getSessionDateExpire()->isFuture();
    }
}
