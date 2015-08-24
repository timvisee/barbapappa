<?php

namespace app\picture;

use app\config\Config;
use app\database\Database;
use app\occupation\OccupationManager;
use app\session\SessionManager;
use app\station\Station;
use app\team\Team;
use carbon\core\datetime\DateTime;
use carbon\core\io\filesystem\file\File;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class Picture {

    /** The approval status of pictures waiting for approval. */
    const APPROVAL_WAITING = 0;
    /** The approval status of accepted pictures. */
    const APPROVAL_ACCEPTED = 1;
    /** The approval status of rejected pictures. */
    const APPROVAL_REJECTED = 2;

    /** @var int The picture ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Picture ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the picture ID.
     *
     * @return int The picture ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific picture.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list pictures with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . PictureManager::getDatabaseTableName() . ' WHERE picture_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the picture team.
     *
     * @return Team Picture team.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getTeam() {
        return new Team($this->getDatabaseValue('picture_team_id'));
    }

    /**
     * Get the picture station.
     *
     * @return Station Picture station.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getStation() {
        return new Station($this->getDatabaseValue('picture_station_id'));
    }

    /**
     * Get the picture file.
     *
     * @return File Picture file.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getFile() {
        // Get the pictures directory
        $picturesDir = new File(CARBON_SITE_ROOT, Config::getValue('app', 'pictures_dir'));

        // Return the picture file
        return new File($picturesDir, $this->getDatabaseValue('picture_file'));
    }

    /**
     * Get the picture URL.
     *
     * @return string Picture URL.
     */
    public function getUrl() {
        // Get the site URL root
        $siteUrl = Config::getValue('general', 'site_url');

        // Get the picture directory
        $picturesDir = Config::getValue('app', 'pictures_dir', '');

        // TODO: Use Carbon's URL class here?
        // Return the URL
        return $siteUrl . $picturesDir . $this->getDatabaseValue('picture_file');
    }

    /**
     * Get the thumbnail URL for this picture.
     *
     * @param int $size [optional] The maximum size.
     * @param bool $square [optional] True to force the thumbnail to be squared.
     *
     * @return string The thumbnail URL.
     */
    public function getThumbnailUrl($size = 80, $square = true) {
        // Build the URL
        $url = Config::getvalue('general', 'size_url', '') . 'thumbnail.php?picture_id=' . $this->getId();

        // Set the picture size
        $url .= '&size=' . $size . 'x' . $size;

        // Make the thumbnail squared
        if($square)
            $url .= '&shape=fixed';

        // Return the URL
        return $url;
    }

    /**
     * Get the thumbnail URL for this picture.
     *
     * @param int $width The width of the image.
     * @param int $height The height of the image.
     *
     * @return string The thumbnail URL.
     */
    public function getResolutionUrl($width, $height) {
        // Build the URL
        $url = Config::getvalue('general', 'size_url', '') . 'thumbnail.php?picture_id=' . $this->getId();

        // Set the picture size
        $url .= '&size=' . $width . 'x' . $height;

        // Set the shape to dynamic
        $url .= '&shape=dynamic';

        // Return the URL
        return $url;
    }

    /**
     * Get the picture time.
     *
     * @return DateTime Picture time.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getTime() {
        return new DateTime($this->getDatabaseValue('picture_time'));
    }

    /**
     * Get picture approval status.
     *
     * @return int Picture approval status.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getPictureApprovalStatus() {
        return (int) $this->getDatabaseValue('picture_approval_status');
    }

    /**
     * Check whether the picture is approved (Accepted or declined).
     *
     * @return bool True if the picture is approved, false otherwise.
     */
    public function isApproved() {
        return $this->getPictureApprovalStatus() > 0;
    }

    /**
     * Check if the picture is accepted.
     *
     * @return bool True if the picture is accepted, false if not.
     */
    public function isAccepted() {
        return $this->getPictureApprovalStatus() == 1;
    }

    /**
     * Check if the picture is declined.
     *
     * @return bool True if the picture is declined, false otherwise.
     */
    public function isRejected() {
        return $this->getPictureApprovalStatus() == 2;
    }

    /**
     * Accept the current picture.
     *
     * @param Team|null $approvalTeam [optional] The team that approved the picture, or null to use the current logged
     * in team.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function accept($approvalTeam = null) {
        $this->setApproval(static::APPROVAL_ACCEPTED, $approvalTeam);
    }

    /**
     * Reject the current picture.
     *
     * @param Team|null $approvalTeam [optional] The team that approved the picture, or null to use the current logged
     * in team.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function reject($approvalTeam = null) {
        $this->setApproval(static::APPROVAL_REJECTED, $approvalTeam);
    }

    /**
     * Set the approval status of the picture.
     *
     * @param int $approvalStatus The approval status.
     * @param Team|null $approvalTeam [optional] The team that approved the picture, or null to use the current logged
     * in team.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function setApproval($approvalStatus, $approvalTeam = null) {
        // Use the current approval team if not set
        if($approvalTeam == null)
            $approvalTeam = SessionManager::getLoggedInTeam();

        // Make sure the approval status is valid
        if(!is_numeric($approvalStatus) || $approvalStatus < 0 || $approvalStatus > 2)
            throw new Exception('Invalid approval status');

        // Prepare a query for the session being created
        $statement = Database::getPDO()->prepare('UPDATE ' . PictureManager::getDatabaseTableName() .
            ' SET picture_approval_status=:status, picture_approval_team_id=:team_id, picture_approval_time=:time' .
            ' WHERE picture_id=:picture_id');
        $statement->bindValue(':status', $approvalStatus, PDO::PARAM_INT);
        $statement->bindValue(':team_id', $approvalTeam->getId(), PDO::PARAM_INT);
        $statement->bindValue(':time', DateTime::now()->toString(), PDO::PARAM_STR);
        $statement->bindValue(':picture_id', $this->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Update the occupations
        if($approvalStatus == 1) {
            // Make sure ths station is reclaimable
            if($this->getStation()->isReclaimable())
                OccupationManager::addOccupationForPicture($this);
        } else {
            // Remove all occupations for this picture
            OccupationManager::removeOccupationOfPicture($this);
        }
    }

    /**
     * Get the approval team if the picture is approved.
     *
     * @return Team|null Approval team, or null.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getApprovalTeam() {
        // Get the approval team ID
        $teamId = $this->getDatabaseValue('picture_approval_team_id');

        // Make sure the team ID isn't null
        if($teamId == null)
            return null;

        // Return the team
        return new Team($teamId);
    }

    /**
     * Get the approval time if the picture is approved.
     *
     * @return DateTime|null Approval time, or null.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getApprovalTime() {
        // Get the approval time
        $time = $this->getDatabaseValue('picture_approval_time');

        // Make sure the time isn't null
        if($time == null)
            return null;

        // Return the time
        return new DateTime($time);
    }

    /**
     * Delete the picture.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function delete() {
        // Prepare a query for the picture being added
        $statement = Database::getPDO()->prepare('DELETE FROM ' . PictureManager::getDatabaseTableName() . ' WHERE picture_id=:picture_id');
        $statement->bindValue(':picture_id', $this->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new \Exception('Failed to query the database.');
        return;
    }
}