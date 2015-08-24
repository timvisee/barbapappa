<?php

namespace app\occupation;

use app\database\Database;
use app\picture\Picture;
use app\station\Station;
use app\team\Team;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class Occupation {

    /** @var int The occupation ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Occupation ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the occupation ID.
     *
     * @return int The occupation ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific occupation.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list occupations with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . OccupationManager::getDatabaseTableName() . ' WHERE occupation_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the occupied station.
     *
     * @return Station Occupied station.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getStation() {
        return new Station($this->getDatabaseValue('occupation_station_id'));
    }

    /**
     * Get the occupying picture.
     *
     * @return Picture Occupying picture.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getPicture() {
        return new Picture($this->getDatabaseValue('occupation_picture_id'));
    }

    /**
     * Get the occupying team.
     *
     * @return Team Occupying team.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getTeam() {
        return new Team($this->getDatabaseValue('occupation_team_id'));
    }

    /**
     * Get the occupation time.
     *
     * @return DateTime Occupation time.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getTime() {
        return DateTime::parse($this->getDatabaseValue('occupation_time'));
    }
}