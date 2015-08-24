<?php

namespace app\station;

use app\config\Config;
use app\database\Database;
use app\occupation\OccupationManager;
use app\team\Team;
use carbon\core\util\StringUtils;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class StationManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'stations';

    /**
     * Get the database table name of the stations.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get a list of all stations.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array An array of stations.
     *
     * @throws \Exception Throws an exception on failure.
     */
    public static function getStations($filter = null, $claimedOnly = false) {
        // Strip percent and hash characters
        $filter = str_replace(Array('#', '%'), ' ', $filter);

        // Build a query
        $query = 'SELECT station_id FROM ' . static::getDatabaseTableName();

        // Check whether a filter should be used
        if($filter != null) {
            $query .= ' WHERE (station_id LIKE :filter';
            $query .= ' OR station_code LIKE :filter';
            $query .= ' OR station_name LIKE :filter';
            $query .= ' OR station_name_middle LIKE :filter';
            $query .= ' OR station_name_short LIKE :filter';
            $query .= ' OR station_rdt_url LIKE :filter';
            $query .= ' OR station_geo_lat LIKE :filter';
            $query .= ' OR station_geo_long LIKE :filter)';
        }

        // Properly order the result
        $query .= ' ORDER BY station_name ASC';

        // Prepare a query for the database to list stations with this ID
        $statement = Database::getPDO()->prepare($query);
        if($filter != null) {
            $filter = '%' . $filter . '%';
            $statement->bindValue(':filter', $filter, PDO::PARAM_STR);
        }

        // Execute the prepared query
        if(!$statement->execute())
            throw new \Exception('Failed to query the database.');

        // The list of stations
        $stations = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data) {
            // Instantiate the station
            $station = new Station($data['station_id']);

            // Check whether it must be claimed
            if($claimedOnly && !$station->isOccupied())
                continue;

            // Add the station to the list
            $stations[] = $station;
        }

        // Return the list of stations
        return $stations;
    }

    /**
     * Get the number of available stations.
     *
     * @return int Number of available stations.
     */
    public static function getStationCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT station_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new \Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if theres any station with the specified ID.
     *
     * @param int $id The ID of the station to check for.
     *
     * @return bool True if any station exists with this ID.
     *
     * @throws \Exception Throws if an error occurred.
     */
    public static function isStationWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new \Exception('Invalid station ID.');

        // Prepare a query for the database to list stations with this ID
        $statement = Database::getPDO()->prepare('SELECT station_id FROM ' . static::getDatabaseTableName() . ' WHERE station_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new \Exception('Failed to query the database.');

        // Return true if there's any station found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Get the last occupied stations. The last occupied station is returned first followed by the others.
     *
     * @param int $count The number of stations to return.
     * @param Team $team The team to get the approved items for.
     *
     * @return array The last occupied stations.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getLastOccupiedStations($count = 5, $team = null) {
        // Build a query
        $query = 'SELECT DISTINCT occupation_station_id FROM ' . OccupationManager::getDatabaseTableName();

        if($team !== null)
            $query .= ' WHERE occupation_team_id=:occupation_team_id';

        // Properly order the result
        $query .= ' ORDER BY occupation_time DESC';

        // Limit the results
        $query .= ' LIMIT 0,:count';

        // Prepare a query for the database to list occupations with this ID
        $statement = Database::getPDO()->prepare($query);
        $statement->bindValue(':count', $count, PDO::PARAM_INT);
        if($team !== null)
            $statement->bindValue(':occupation_team_id', $team->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // The list of stations
        $stations = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data) {
            // Get the station instance
            $station = new Station($data['occupation_station_id']);

            // Make sure the station is occupied by the correct team
            if($team !== null && $station->getOccupation()->getTeam()->getId() !== $team->getId())
                continue;

            // Add the team to the list
            $stations[] = $station;
        }

        // Return the list of stations
        return $stations;
    }

    /**
     * Get the high value stations. The highest valued station is returned first.
     *
     * @param int $count The number of stations to return.
     * @param Team $team The team to get the stations for.
     *
     * @return array High value stations.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getHighValueStations($count = 5, $team = null) {
        // Build a query
        $query = 'SELECT DISTINCT station_id FROM ' . StationManager::getDatabaseTableName();

        $query .= ' WHERE station_cache_points IS NOT NULL';
        if($team !== null)
            $query .= ' AND station_team_id=:station_team_id';

        // Properly order the result
        $query .= ' ORDER BY station_cache_points DESC';

        // Limit the results
        $query .= ' LIMIT 0,:count';

        // Prepare a query for the database to list occupations with this ID
        $statement = Database::getPDO()->prepare($query);
        $statement->bindValue(':count', $count, PDO::PARAM_INT);
        if($team !== null)
            $statement->bindValue(':station_team_id', $team->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // The list of stations
        $stations = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data) {
            // Get the station instance
            $station = new Station($data['station_id']);

            // Make sure the station is occupied by the correct team
            if($team !== null && $station->getOccupation()->getTeam()->getId() !== $team->getId())
                continue;

            // Add the team to the list
            $stations[] = $station;
        }

        // Return the list of stations
        return $stations;
    }
}