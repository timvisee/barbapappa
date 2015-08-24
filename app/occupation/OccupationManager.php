<?php

namespace app\occupation;

use app\config\Config;
use app\database\Database;
use app\picture\Picture;
use app\session\SessionManager;
use app\station\Station;
use app\team\Team;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class OccupationManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'occupations';

    /**
     * Get the database table name of the occupations.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get the number of occupations.
     *
     * @return int Number of available occupations.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getOccupationCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT occupation_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any occupation with the specified ID.
     *
     * @param int $id The ID of the occupation to check for.
     *
     * @return bool True if any occupation exists with this ID.
     *
     * @throws \Exception Throws if an error occurred.
     */
    public static function isOccupationWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid occupation ID.');

        // Prepare a query for the database to list occupations with this ID
        $statement = Database::getPDO()->prepare('SELECT occupation_id FROM ' . static::getDatabaseTableName() . ' WHERE occupation_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any occupation found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Check if there's any occupation with the specified picture.
     *
     * @param Picture $picture The Picture of the occupation to check for.
     *
     * @return bool True if any occupation exists with this ID.
     *
     * @throws \Exception Throws if an error occurred.
     */
    public static function isOccupationWithPicture($picture) {
        // Make sure the ID isn't null
        if($picture === null)
            throw new Exception('Invalid occupation picture.');

        // Prepare a query for the database to list occupations with this ID
        $statement = Database::getPDO()->prepare('SELECT occupation_id FROM ' . static::getDatabaseTableName() . ' WHERE occupation_picture_id=:picture_id');
        $statement->bindValue(':picture_id', $picture->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any occupation found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Get the last occupation for a station.
     *
     * @param Station $station The station to get the occupation for.
     *
     * @return Occupation|null The occupation for this station.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getStationOccupation($station) {
        return static::getStationOccupations($station, 1, false)[0];
    }

    /**
     * Get the last occupation for a station.
     *
     * @param Station $station The station to get the occupation for.
     * @param int|null $limit The limit of occupations to return, null to disable the limit.
     * @param bool $sortAsc True to sort in acceding order, false to sort in reversed other.
     *
     * @return Array List of occupations.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getStationOccupations($station, $limit = null, $sortAsc = true) {
        // Make sure the station isn't null
        if($station === null)
            throw new Exception('Station instance invalid!');

        // Build a query
        $query = 'SELECT occupation_id FROM ' . static::getDatabaseTableName();

        // Select the correct station
        $query .= ' WHERE occupation_station_id=:station_id';

        // Properly order and limit the result
        if($sortAsc)
            $query .= ' ORDER BY occupation_time ASC';
        else
            $query .= ' ORDER BY occupation_time DESC';

        // Set the limit
        if($limit !== null)
            $query .= ' LIMIT 0,:limit';

        // Prepare a query for the database to list occupations with this ID
        $statement = Database::getPDO()->prepare($query);
        $statement->bindValue(':station_id', $station->getId());

        // Set the limit param if set
        if($limit !== null)
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Create a list to put the occupations in
        $occupations = Array();

        // Return the first occupation in the list
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $occupations[] = new Occupation($data['occupation_id']);

        // Return the list of occupations
        return $occupations;
    }

    /**
     * Check weather a station is occupied.
     *
     * @param Station $station The station to check for.
     *
     * @return bool True if the station is occupied, false if not.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isStationOccupied($station) {
        return static::getStationOccupation($station) != null;
    }

    /**
     * Get the last occupations. The last occupation is returned first followed by the others.
     *
     * @param int $count The number of occupations to return.
     *
     * @return array List of last occupations.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getLastOccupations($count = 5) {
        // Build a query
        $query = 'SELECT occupation_id FROM ' . static::getDatabaseTableName();

        // Properly order the result
        $query .= ' ORDER BY occupation_time DESC';

        // Limit the results
        $query .= ' LIMIT 0,:count';

        // Prepare a query for the database to list occupations with this ID
        $statement = Database::getPDO()->prepare($query);
        $statement->bindValue(':count', $count, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // The list of occupations
        $occupations = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $occupations[] = new Occupation($data['occupation_id']);

        // Return the list of occupations
        return $occupations;
    }

    /**
     * Add a new occupation to the database.
     *
     * @param Station $station The station being occupied.
     * @param Picture $picture The occupying picture.
     * @param Team|null $team [optional] The occupying team, or null to use the current team.
     *
     * @return Occupation The added picture.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function addOccupation($station, $picture, $team = null) {
        // MMake sure the required parameters are set
        if($station === null || $picture === null)
            throw new Exception('Failed to add picture!');

        // Parse the team
        if($team === null)
            $team = SessionManager::getLoggedInTeam();

        // Get the occupation time
        $pictureTime = $picture->getTime();

        // Get the old occupation if there is any
        $oldOccupation = $station->getOccupation();

        // Prepare a query for the picture being added
        $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
            ' (occupation_station_id, occupation_picture_id, occupation_team_id, occupation_time) ' .
            'VALUES (:occupation_station_id, :occupation_picture_id, :occupation_team_id, :occupation_time)');
        $statement->bindValue(':occupation_station_id', $station->getId(), PDO::PARAM_INT);
        $statement->bindValue(':occupation_picture_id', $picture->getId(), PDO::PARAM_INT);
        $statement->bindValue(':occupation_team_id', $team->getId(), PDO::PARAM_INT);
        $statement->bindValue(':occupation_time', $pictureTime->toString(), PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Update the cached points of the station and team
        $station->updateCachedPoints();
        $team->updateCachedPoints();

        // If the station was previously occupied by a different team, update it's score too
        if($oldOccupation !== null && $oldOccupation->getTeam()->getId() != $team->getId())
            $oldOccupation->getTeam()->updateCachedPoints();

        // Return the inserted picture
        return new Occupation(Database::getPDO()->lastInsertId());
    }

    /**
     * Add a new occupation to the database.
     *
     * @param Picture $picture The occupying picture.
     *
     * @return Occupation The added picture.
     *
     * @throws \Exception Throws if an error occurred.
     */
    public static function addOccupationForPicture($picture) {
        // Make sure the required parameters are set
        if($picture === null)
            throw new Exception('Invalid picture!');

        // Add the occupation, return the result
        return static::addOccupation($picture->getStation(), $picture, $picture->getTeam());
    }

    /**
     * Delete the occupation of a specific picture.
     *
     * @param Picture $picture The picture to delete the occupation for.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function removeOccupationOfPicture($picture) {
        // MMake sure the required parameters are set
        if($picture === null)
            throw new \Exception('Failed to add picture!');

        // Get the station and team of the picture
        $station = $picture->getStation();
        $team = $picture->getTeam();

        // Prepare a query for the picture being added
        $statement = Database::getPDO()->prepare('DELETE FROM ' . static::getDatabaseTableName() . ' WHERE occupation_picture_id=:occupation_picture_id');
        $statement->bindValue(':occupation_picture_id', $picture->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Update the station and team score
        $station->updateCachedPoints();
        $team->updateCachedPoints();
        return;
    }
}
