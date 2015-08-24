<?php

namespace app\team;

use app\config\Config;
use app\database\Database;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class TeamManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'teams';

    /**
     * Get the database table name of the teams.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get a list of all teams.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @param bool $sort [optional] True to sort the teams by score in descending order.
     *
     * @return array An array of teams.
     *
     * @throws \Exception Throws an exception on failure.
     */
    public static function getTeams($sort = false) {
        // Build a query to select the teams
        $query = 'SELECT team_id FROM ' . static::getDatabaseTableName();

        // Properly sort the teams to put the administrator users last
        $query .= ' ORDER BY team_admin ASC';

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new \Exception('Failed to query the database.');

        // The list of teams
        $teams = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $teams[] = new Team($data['team_id']);

        // Sort the teams
        if($sort)
            usort($teams, function(Team $a, Team $b) {
                return $b->getCachedPoints() - $a->getCachedPoints();
            });

        // Return the list of teams
        return $teams;
    }

    /**
     * Get the number of available teams.
     *
     * @return int Number of available teams.
     *
     * @throws \Exception Throws if an error occurred.
     */
    public static function getTeamCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT team_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new \Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any team with the specified ID.
     *
     * @param int $id The ID of the team to check for.
     *
     * @return bool True if any team exists with this ID.
     *
     * @throws \Exception Throws if an error occurred.
     */
    public static function isTeamWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new \Exception('Invalid team ID.');

        // Prepare a query for the database to list teams with this ID
        $statement = Database::getPDO()->prepare('SELECT team_id FROM ' . static::getDatabaseTableName() . ' WHERE team_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new \Exception('Failed to query the database.');

        // Return true if there's any team found with this ID
        return $statement->rowCount() > 0;
    }
}