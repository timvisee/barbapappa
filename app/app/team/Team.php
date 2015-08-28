<?php

namespace app\team;

use app\database\Database;
use app\station\Station;
use app\station\StationManager;
use carbon\core\datetime\DateTime;
use carbon\core\util\StringUtils;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class Team {

    /** @var int The team ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Team ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the team ID.
     *
     * @return int The team ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific team.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws \Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list teams with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . TeamManager::getDatabaseTableName() . ' WHERE team_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new \Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the name of the team.
     *
     * @return string Team name.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getName() {
        return $this->getDatabaseValue('team_name');
    }

    /**
     * Get the name of the team.
     *
     * @param string $password The password to compare the users password to, in plain text.
     *
     * @return string Team name.
     *
     * @throws Exception
     */
    public function isPassword($password) {
        // Hash the password
        $hash = md5($password);

        // Compare the hashes
        return StringUtils::equals($hash, $this->getDatabaseValue('team_pass_hash'), false, true);
    }

    /**
     * Check whether this team is administrator.
     *
     * @return bool True if this team is administrator, false otherwise.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function isAdmin() {
        return $this->getDatabaseValue('team_admin') == '1';
    }

    /**
     * Get the HEX color of the team.
     *
     * @return string HEX color of the team.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getColorHex() {
        return $this->getDatabaseValue('team_color_hex');
    }

    /**
     * Check whether this team has a HEX color set.
     *
     * @return bool True if this team has a HEX color, false otherwise.
     */
    public function hasColorHex() {
        return strlen(trim($this->getColorHex())) > 0;
    }

    /**
     * Get the sign in date of the team.
     *
     * @return DateTime Sign in date of the team.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getTeamDate() {
        return new DateTime($this->getDatabaseValue('team_date'));
    }

    /**
     * Get the expire date of the team.
     *
     * @return DateTime Expire date of the team.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getTeamDateExpire() {
        return new DateTime($this->getDatabaseValue('team_date_expire'));
    }

    /**
     * Check whether the team is expired.
     *
     * @return bool True if the team is expired, false if not.
     */
    public function isExpired() {
        return !$this->getTeamDate()->isFuture();
    }

    /**
     * Get the display name for this team.
     *
     * @param bool $icon [optional] True to include the color icon for this team if available.
     * @param bool $smallIcon [optional] True to use a small icon, false otherwise.
     *
     * @return string The display name of the team.
     */
    public function getDisplayName($icon = true, $smallIcon = false) {
        // Define a return variable
        $return = '';

        // Get the team color if it exists
        $teamColor = $this->getColorHex();

        // Add an icon if a team color is determined
        if($teamColor !== null && $icon)
            $return .= '<img src="style/image/icon/16/transparent.png" class="ui-li-icon group-icon-text' . ($smallIcon ? '-small' : '') . '" style="background: #' . $teamColor . '" />';

        // Add the team name
        $return .= (!$this->isAdmin() ? 'Team ' : '') . ucfirst($this->getName());

        // Return the display name
        return $return;
    }

    /**
     * Get the total number of points for this team from cache.
     * If the number of points isn't cached it will be calculated at runtime, which might be expensive.
     *
     * @return int The total score for this team.
     *
     * @throws Exception Throws an exception on failure.
     */
    public function getCachedPoints() {
        // Get the cached points
        $cachedPoints = $this->getDatabaseValue('team_cache_points');

        // Return the cached points
        if(is_numeric($cachedPoints))
            return $cachedPoints;

        // Update the points and return the result
        return $this->updateCachedPoints();
    }

    /**
     * Check whether this team has cached points.
     *
     * @return bool True if this team has cached points.
     *
     * @throws Exception Trows on failure.
     */
    public function hasCachedPoints() {
        return $this->getDatabaseValue('team_cache_points') !== null;
    }

    /**
     * Calculate the total number of points for this team.
     * Note: This method is expensive!
     *
     * @return int The number of points.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function calculatePoints() {
        // Get the occupations for this team
        $occupations = StationManager::getLastOccupiedStations(99999, $this);

        // Calculate the total number of points for all occupations recursively
        $points = 0;
        foreach($occupations as $station) {
            // Validate the instance
            if(!($station instanceof Station))
                continue;

            // Get and add the points to the total
            $points += $station->calculatePoints();
        }

        // Return the number of points
        return $points;
    }

    /**
     * Update the cached points.
     *
     * @return int The number of points.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function updateCachedPoints() {
        // Calculate and cache the number of points for this team
        $points = $this->calculatePoints();

        // Cache the points
        $statement = Database::getPDO()->prepare('UPDATE ' . TeamManager::getDatabaseTableName() .
            ' SET team_cache_points=:points WHERE team_id=:team_id');
        $statement->bindValue(':points', $points, PDO::PARAM_INT);
        $statement->bindValue(':team_id', $this->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new \Exception('Failed to query the database.');

        // Return the number of points
        return $points;
    }
}
