<?php

namespace app\mail;

use app\database\Database;
use app\user\User;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class Mail {

    /** @var int The mail ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Mail ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the mail ID.
     *
     * @return int The mail ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific mail.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list mails with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . MailManager::getDatabaseTableName() . ' WHERE mail_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the user.
     *
     * @return User The user.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getUser() {
        return new User($this->getDatabaseValue('mail_user_id'));
    }

    /**
     * Get the mail.
     *
     * @return string Mail address.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getMail() {
        return $this->getDatabaseValue('mail_mail');
    }

    /**
     * Get the mail.
     *
     * @return string Mail address.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getCreationDateTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getDatabaseValue('mail_create_datetime'));
    }

    /**
     * Get the mail verification date time.
     *
     * @return DateTime Mail verification date time.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getVerificationDateTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getDatabaseValue('mail_verified_datetime'));
    }

    /**
     * Get the verification IP.
     *
     * @return string Verification IP.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getVerificationIp() {
        return $this->getDatabaseValue('mail_verified_ip');
    }

    /**
     * Delete this mail.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function delete() {
        // Prepare a query for the mail being deleted
        $statement = Database::getPDO()->prepare('DELETE FROM ' . MailManager::getDatabaseTableName() . ' WHERE mail_id=:mail_id');
        $statement->bindValue(':mail_id', $this->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }
}
