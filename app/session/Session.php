<?php

namespace app\session;

use app\database\Database;
use app\team\Team;
use carbon\core\datetime\DateTime;
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
     * @throws \Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list sessions with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . SessionManager::getDatabaseTableName() . ' WHERE session_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new \Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the session team.
     *
     * @return Team Session team.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getTeam() {
        return new Team($this->getDatabaseValue('session_team_id'));
    }

    /**
     * Get the key of this session.
     *
     * @return string Session key.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getKey() {
        return $this->getDatabaseValue('session_key');
    }

    /**
     * Get the IP of this session.
     *
     * @return string Session IP.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getIp() {
        return $this->getDatabaseValue('session_ip');
    }

    /**
     * Get the sign in date of the session.
     *
     * @return DateTime Sign in date of the session.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getSessionDate() {
        return new DateTime($this->getDatabaseValue('session_date'));
    }

    /**
     * Get the expire date of the session.
     *
     * @return DateTime Expire date of the session.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getSessionDateExpire() {
        return new DateTime($this->getDatabaseValue('session_date_expire'));
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
