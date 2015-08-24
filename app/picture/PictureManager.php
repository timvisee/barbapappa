<?php

namespace app\picture;

use app\config\Config;
use app\database\Database;
use app\session\SessionManager;
use app\station\Station;
use app\team\Team;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class PictureManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'pictures';
    /** The length of a picture name. */
    const PICTURE_NAME_LENGTH = 32;
    /** The characters a picture name can consist of. */
    const PICTURE_NAME_CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-';

    /**
     * Get the database table name of the pictures.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get the number of pictures.
     *
     * @return int Number of available pictures.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getPictureCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT picture_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Get the number of approved pictures.
     *
     * @return int Number of approved pictures.
     * @param Team $team The team to get the approved items for.
     * @param Station $station The station to get the approved items for.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getApprovedPictureCount($status = null, $team = null, $station = null) {
        // Build the where clause
        if($status === null)
            $where = 'WHERE picture_approval_status > 0';
        else if(is_numeric($status))
            $where = 'WHERE picture_approval_status=' . $status;
        else
            throw new Exception('Invalid approval status! ');

        if($team !== null)
            $where .= ' AND picture_team_id=' . $team->getId();

        if($station !== null)
            $where .= ' AND picture_station_id=' . $station->getId();

        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT picture_id FROM ' . static::getDatabaseTableName() . ' ' . $where);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any picture with the specified ID.
     *
     * @param int $id The ID of the picture to check for.
     *
     * @return bool True if any picture exists with this ID.
     *
     * @throws \Exception Throws if an error occurred.
     */
    public static function isPictureWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            return false;

        // Prepare a query for the database to list pictures with this ID
        $statement = Database::getPDO()->prepare('SELECT picture_id FROM ' . static::getDatabaseTableName() . ' WHERE picture_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any picture found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Check if there's any picture with the specified name.
     *
     * @param string $name The name of the picture to check for.
     *
     * @return bool True if any picture exists with this name.
     *
     * @throws \Exception Throws if an error occurred.
     */
    public static function isPictureWithName($name) {
        // Make sure the name isn't null
        if($name === null)
            return false;

        // Prepare a query for the database to list pictures with this name
        $statement = Database::getPDO()->prepare('SELECT picture_id FROM ' . static::getDatabaseTableName() . ' WHERE picture_file=:name');
        $statement->bindParam(':name', $name, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any picture found with this name
        return $statement->rowCount() > 0;
    }

    /**
     * Get the list of pictures for a station.
     *
     * @param Station $station The station to get the pictures for.
     * @param bool $order [optional] True to return in ascending order, false for descending order.
     *
     * @return Picture|null The pictures for this station.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getStationPictures($station, $order = false) {
        // Make sure the station isn't null
        if($station === null)
            throw new Exception('Station instance invalid!');

        // Build a query
        $query = 'SELECT picture_id FROM ' . static::getDatabaseTableName();

        // Select the correct station
        $query .= ' WHERE picture_station_id=:station_id';

        // Properly order and limit the result
        $query .= ' ORDER BY picture_time ' . ($order ? 'ASC' : 'DESC');

        // Prepare a query for the database to list pictures with this ID
        $statement = Database::getPDO()->prepare($query);
        $statement->bindValue(':station_id', $station->getId());

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Create a list of pictures
        $pictures = Array();

        // Return the first picture in the list
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $pictures[] = new Picture($data['picture_id']);

        // Return the list of pictures
        return $pictures;
    }

    /**
     * Get all pictures waiting for approval.
     *
     * @param Team $team The team to get the approved items for.
     *
     * @return array List of pictures waiting for approval.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getApprovalQueue($team = null) {
        // Build a query
        $query = 'SELECT picture_id FROM ' . static::getDatabaseTableName();

        // Make sure the only select pictures waiting for approval
        $query .= ' WHERE picture_approval_status=0';

        if($team !== null)
            $query .= ' AND picture_team_id=:picture_team_id';

        // Properly order the result
        $query .= ' ORDER BY picture_time ASC';

        // Prepare a query for the database to list pictures with this ID
        $statement = Database::getPDO()->prepare($query);

        if($team !== null)
            $statement->bindValue(':picture_team_id', $team->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // The list of pictures
        $pictures = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $pictures[] = new Picture($data['picture_id']);

        // Return the list of pictures
        return $pictures;
    }

    /**
     * Get the next picture in the approval queue.
     *
     * @param Team $team The team to get the approved items for.
     *
     * @return Picture Get the next picture in the approval queue.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getApprovalQueueNext($team = null) {
        // Build a query
        $query = 'SELECT picture_id FROM ' . static::getDatabaseTableName();

        // Make sure the only select pictures waiting for approval
        $query .= ' WHERE picture_approval_status=0';

        if($team !== null)
            $query .= ' AND picture_team_id=:picture_team_id';

        // Properly order the result
        $query .= ' ORDER BY picture_time ASC';

        // Limit the query
        $query .= ' LIMIT 0,1';

        // Prepare a query for the database to list pictures with this ID
        $statement = Database::getPDO()->prepare($query);
        if($team !== null)
            $statement->bindValue(':picture_team_id', $team->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the first item in queue
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            return new Picture($data['picture_id']);

        // No item in queue, return null
        return null;
    }

    /**
     * Get the number of pictures waiting for approval.
     *
     * @param Team $team The team to get the approved items for.
     *
     * @return int Number of pictures waiting for approval.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getApprovalQueueSize($team = null) {
        // Build a query
        $query = 'SELECT picture_id FROM ' . static::getDatabaseTableName();

        // Make sure the only select pictures waiting for approval
        $query .= ' WHERE picture_approval_status=0';

        if($team !== null)
            $query .= ' AND picture_team_id=:picture_team_id';

        // Properly order the result
        $query .= ' ORDER BY picture_time ASC';

        // Prepare a query for the database to list pictures with this ID
        $statement = Database::getPDO()->prepare($query);
        if($team !== null)
            $statement->bindValue(':picture_team_id', $team->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the number of pictures
        return $statement->rowCount();
    }

    /**
     * Get the last approved pictures. The last approved is returned first followed by the others.
     *
     * @param int $count The number of approvals to return.
     * @param int|null $status The approval status, 1 for accepted, 2 for rejected, null for both.
     * @param Team $team The team to get the approved items for.
     * @param Station $station The station to get the approved items for.
     *
     * @return array List of last approvals.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getLastApproved($count = 5, $status = null, $team = null, $station = null) {
        // Build a query
        $query = 'SELECT picture_id FROM ' . static::getDatabaseTableName();

        // Only select approved pictures
        if($status === null)
            $query .= ' WHERE picture_approval_status > 0';
        else if(is_numeric($status))
            $query .= ' WHERE picture_approval_status=:status';
        else
            throw new Exception('Invalid approval status!');

        if($team !== null)
            $query .= ' AND picture_team_id=:picture_team_id';
        if($station !== null)
            $query .= ' AND picture_station_id=:picture_station_id';

        // Properly order the result
        $query .= ' ORDER BY picture_approval_time DESC';

        // Limit the results
        $query .= ' LIMIT 0,:count';

        // Prepare a query for the database to list pictures with this ID
        $statement = Database::getPDO()->prepare($query);
        $statement->bindValue(':count', $count, PDO::PARAM_INT);
        if(is_numeric($status))
            $statement->bindValue(':status', $status, PDO::PARAM_INT);
        if($team !== null)
            $statement->bindValue(':picture_team_id', $team->getId(), PDO::PARAM_INT);
        if($station !== null)
            $statement->bindValue(':picture_station_id', $station->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // The list of pictures
        $pictures = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $pictures[] = new Picture($data['picture_id']);

        // Return the list of pictures
        return $pictures;
    }

    /**
     * Generate a random picture name.
     *
     * @param string $extension The picture extension, without a period.
     *
     * @return string Random picture name.
     */
    public static function generateRandomPictureName($extension) {
        // Get the characters a picture name can consist of
        $chars = static::PICTURE_NAME_CHARS;

        // Generate a random picture name, make sure it doesn't exist
        do {
            $randomPictureName = '';
            for ($i = 0; $i < static::PICTURE_NAME_LENGTH; $i++)
                $randomPictureName .= $chars[rand(0, strlen($chars) - 1)];

            // Add the extension
            $randomPictureName .= '.' . $extension;

            // Check whether this picture name exists already
            $exists = static::isPictureWithName($randomPictureName);

        } while($exists);

        // Return the random name
        return $randomPictureName;
    }

    /**
     * Add a new picture to the database.
     *
     * @param string $fileName The file name of the picture to add.
     * @param Team|null $team The team to add the picture for, null to use the current logged in team.
     * @param Station $station The station to add the picture for.
     *
     * @return Picture The added picture.
     *
     * @throws \Exception Throws if an error occurred.
     */
    public static function addPicture($fileName, $team = null, $station) {
        // Make sure the file name and station aren't null
        if($fileName === null || $station === null)
            throw new \Exception('Failed to add picture!');

        // Parse the team
        if($team === null)
            $team = SessionManager::getLoggedInTeam();

        // Get the picture upload time
        $pictureTime = DateTime::now();

        // Prepare a query for the picture being added
        $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
            ' (picture_team_id, picture_station_id, picture_file, picture_time) ' .
            'VALUES (:picture_team_id, :picture_station_id, :picture_file, :picture_time)');
        $statement->bindValue(':picture_team_id', $team->getId(), PDO::PARAM_INT);
        $statement->bindValue(':picture_station_id', $station->getId(), PDO::PARAM_INT);
        $statement->bindValue(':picture_file', $fileName, PDO::PARAM_STR);
        $statement->bindValue(':picture_time', $pictureTime->toString(), PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new \Exception('Failed to query the database.');

        // Return the inserted picture
        return new Picture(Database::getPDO()->lastInsertId());
    }
}
