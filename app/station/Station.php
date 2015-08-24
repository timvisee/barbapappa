<?php

namespace app\station;

use app\database\Database;
use app\occupation\Occupation;
use app\occupation\OccupationManager;
use app\picture\Picture;
use app\picture\PictureManager;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class Station {

    /** @var int The station ID. */
    private $id;

    /** The time a team needs to wait to reclaim this station. */
    const STATION_RECLAIM_TIME = '+30 minutes';
    /** The facter the station score is increase with each time it has been reclaimed. */
    const STATION_SCORE_INCREASE = 1.15;
    /** @var array The base points for each type of station. */
    private static $STATION_TYPE_POINTS = Array(
        'megastation' => 100,
        'knooppuntintercitystation' => 75,
        'knooppuntsneltreinstation' => 70,
        'knooppuntstoptreinstation' => 65,
        'intercitystation' => 40,
        'sneltreinstation' => 35,
        'stoptreinstation' => 30,
        'facultatiefstation' => 50,
        'default' => 50
    );

    /**
     * Constructor.
     *
     * @param int $id Station ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the station ID.
     *
     * @return int The station ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific station.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws \Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list stations with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . StationManager::getDatabaseTableName() . ' WHERE station_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new \Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the letter code of the station.
     *
     * @return string Letter code of the station.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getCode() {
        return strtoupper($this->getDatabaseValue('station_code'));
    }

    /**
     * Get the UIC of the station.
     *
     * @return int UIC of the station.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getUIC() {
        return (int) $this->getDatabaseValue('station_uic');
    }

    /**
     * Get the full name of the station.
     *
     * @return string Full name of the station.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getName() {
        return $this->getDatabaseValue('station_name');
    }

    /**
     * Get the middle name of the station.
     *
     * @return string Middle name of the station.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getNameMiddle() {
        return $this->getDatabaseValue('station_name_middle');
    }

    /**
     * Get the short name of the station.
     *
     * @return string Short name of the station.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getNameShort() {
        return $this->getDatabaseValue('station_name_short');
    }

    /**
     * Get the RDT URL of the station.
     *
     * @return string RDT URL of the station.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getRdtUrl() {
        return $this->getDatabaseValue('station_rdt_url');
    }

    /**
     * Get the type of the station.
     *
     * @return string Type of the station.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getType() {
        return $this->getDatabaseValue('station_type');
    }

    /**
     * Get the latitude of the station.
     *
     * @return mixed Latitude of the station.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getGeoLatitude() {
        return $this->getDatabaseValue('station_geo_lat');
    }

    /**
     * Get the longitude of the station.
     *
     * @return mixed Longitude of the station.
     *
     * @throws \Exception Throws an exception if an error occurred.
     */
    public function getGeoLongitude() {
        return $this->getDatabaseValue('station_geo_long');
    }

    /**
     * Get the occupation if the station is occupied.
     *
     * @return Occupation|null The occupation, or null if the station isn't occupied.
     *
     * @throws Exception Throws in an error occurred.
     */
    public function getOccupation() {
        return OccupationManager::getStationOccupation($this);
    }

    /**
     * Check whether the station is occupied.
     *
     * @return bool True if the station is occupied, false otherwise.
     */
    public function isOccupied() {
        return OccupationManager::isStationOccupied($this);
    }

    /**
     * Get the occupation color of a station if it's occupied.
     *
     * @return string|null The occupation color if the station is occupied, null if the station isn't occupied.
     */
    public function getStationColor() {
        // Get the station occupation
        $occupation = $this->getOccupation();

        // Make sure the station is occupied
        if($occupation === null)
            return null;

        // Get the team and return it's color
        return $occupation->getTeam()->getColorHex();
    }

    /**
     * Get the display name for this station.
     *
     * @param bool $icon [optional] True to include the color icon for this team if available.\
     * @param bool $smallIcon [optional] True to use a small icon, false otherwise.
     *
     * @return string The display name of the station.
     */
    public function getDisplayName($icon = true, $smallIcon = false) {
        // Define a return variable
        $return = '';

        // Get the station color if it exists
        $stationColor = $this->getStationColor();

        // Add an icon if a station color is determined
        if($stationColor !== null && $icon)
            $return .= '<img src="style/image/icon/16/transparent.png" class="ui-li-icon group-icon-text' . ($smallIcon ? '-small' : '') . '" style="background: #' . $stationColor . '" />';

        // Add the station name
        $return .= $this->getName();

        // Return the display name
        return $return;
    }

    /**
     * Get the time the station will be claimable at again if the station is occupied.
     * If the station isn't occupied null will be returned.
     *
     * @return DateTime|null The time the station will be claimable at again, or null if the station isn't occupied.
     */
    public function getReclaimableTime() {
        // Make sure the station is occupied
        if(!$this->isOccupied())
            return null;

        // Get the time the picture was approved
        $pictureApprovalTime = $this->getOccupation()->getPicture()->getApprovalTime();

        // Add the reclaim time
        $reclaimAt = $pictureApprovalTime;
        $reclaimAt = $reclaimAt->add(static::STATION_RECLAIM_TIME);

        // Return the reclaim time
        return $reclaimAt;
    }

    /**
     * Check whether the station is reclaimable. True is also returned if the station isn't claimed.
     *
     * @return bool True if the station is reclaimable. True will also be returned if the station isn't claimed.
     */
    public function isReclaimable() {
        // Get the reclaimable time
        $time = $this->getReclaimableTime();

        // Make sure the time isn't null
        if($time === null)
            return true;

        // Check whether the reclaimable time is exceed
        return $time->isPast();
    }

    /**
     * Get the number of times the station has been occupied and reclaimed.
     *
     * @return int The number of times the station has been occupied and reclaimed. Zero will be returned if the station isn't occupied yet.
     */
    public function getOccupationCount() {
        // Get the pictures for this station
        $pictures = PictureManager::getStationPictures($this, true);

        // Make sure any picture is returned
        if(sizeof($pictures) == 0)
            return 0;

        // Define the variable for the count
        $count = 0;

        // Store the last picture used
        $lastReclaimTime = null;

        // Loop through the pictures to calculate the claim count
        foreach($pictures as $picture) {
            // Verify the instance type
            if(!($picture instanceof Picture))
                continue;

            // Make sure the picture is accepted
            if(!$picture->isAccepted())
                continue;

            // Get the picture time and approval time
            $time = $picture->getTime();
            $approvalTime = $picture->getApprovalTime();

            // Calculate the reclaim time
            $reclaimTime = $approvalTime->copy();
            $reclaimTime = $reclaimTime->add(static::STATION_RECLAIM_TIME);

            // Make sure this picture reclaims the previous
            if($count > 0 && $time->isLessThan($lastReclaimTime))
                continue;

            // Increase the occupation counter
            $count++;

            // Store the last time and reclaim time
            $lastReclaimTime = $reclaimTime->copy();
        }

        // Return the number of occupations the station has
        return $count;
    }
























    /**
     * Get the total number of points for this station from cache.
     * If the number of points isn't cached it will be calculated at runtime, which might be expensive.
     *
     * @return int The total score for this team.
     *
     * @throws Exception Throws an exception on failure.
     */
    public function getCachedPoints() {
        // Get the cached points
        $cachedPoints = $this->getDatabaseValue('station_cache_points');

        // Return the cached points
        if(is_numeric($cachedPoints))
            return $cachedPoints;

        // Update the points and return the result
        return $this->updateCachedPoints();
    }

    /**
     * Check whether this station has cached points.
     *
     * @return bool True if this station has cached points.
     *
     * @throws Exception Trows on failure.
     */
    public function hasCachedPoints() {
        return $this->getDatabaseValue('station_cache_points') !== null;
    }

    /**
     * Calculate the total number of points for this station.
     * Note: This method is expensive!
     *
     * @param int|null $occupationOffset [optional] The occupation offset for the points.
     *
     * @return int The number of points.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function calculatePoints($occupationOffset = 0) {
        // Get the base points for this station
        $baseScore = static::$STATION_TYPE_POINTS[$this->getType()];

        // Get the increase count based on the number of times the station is occupied
        $increaseCount = max($this->getOccupationCount() - 1 + $occupationOffset, 0);

        // Calculate and return the points for this station
        $score = $baseScore * (pow(static::STATION_SCORE_INCREASE, $increaseCount));
        return round($score);
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
        $statement = Database::getPDO()->prepare('UPDATE ' . StationManager::getDatabaseTableName() .
            ' SET station_cache_points=:points WHERE station_id=:station_id');
        $statement->bindValue(':points', $points, PDO::PARAM_INT);
        $statement->bindValue(':station_id', $this->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new \Exception('Failed to query the database.');

        // Return the number of points
        return $points;
    }
}