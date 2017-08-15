<?php

namespace app\mail;

use app\config\Config;
use app\database\Database;
use app\user\User;
use app\util\AccountUtils;
use carbon\core\datetime\DateTime;
use carbon\core\util\IpUtils;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class MailManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'mail';

    /**
     * Get the database table name of the mails.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get a list of all mails.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array All mails.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getMails() {
        // Build a query to select the mails
        $query = 'SELECT mail_id FROM ' . static::getDatabaseTableName();

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // The list of mails
        $mails = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $mails[] = new Mail($data['mail_id']);

        // Return the list of mails
        return $mails;
    }

    /**
     * Get a list of all mails for a specific user.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @param User $user The user.
     *
     * @return array All mails.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getMailsFromUser($user) {
        // Make sure the user is valid
        if(!($user instanceof User))
            throw new Exception('Invalid user.');

        // Prepare a query to list all balances for this user
        $statement = Database::getPDO()->prepare('SELECT mail_id FROM ' . static::getDatabaseTableName() . ' WHERE mail_user_id=:user_id');
        $statement->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // The list of mails
        $mails = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $mails[] = new Mail($data['mail_id']);

        // Return the list of mails
        return $mails;
    }

    /**
     * Get the number of mails.
     *
     * @return int Number of mails.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getMailCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT mail_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any mail with the specified ID.
     *
     * @param int $id The ID of the mail to check for.
     *
     * @return bool True if any mail exists with this ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isMailWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid mail ID.');

        // Prepare a query for the database to list mails with this ID
        $statement = Database::getPDO()->prepare('SELECT mail_id FROM ' . static::getDatabaseTableName() . ' WHERE mail_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any mail found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Get a mail object by an mail address.
     *
     * @param string $mail The mail.
     *
     * @return Mail|null The mail, or null if there's no mail with this address.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getMailWithMail($mail) {
        // Make sure the mail is valid
        if(!AccountUtils::isValidMail($mail))
            return false;

        // Trim the mail
        $mail = trim($mail);

        // Prepare a query for the database to get the mail
        $statement = Database::getPDO()->prepare('SELECT mail_id FROM ' . static::getDatabaseTableName() . ' WHERE mail_mail LIKE :mail_mail');
        $statement->bindValue(':mail_mail', $mail, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the mail, or null if no mail was found
        if($statement->rowCount() > 0)
            return new Mail($statement->fetch(PDO::FETCH_ASSOC)['mail_id']);
        return null;
    }

    /**
     * Check whether there is a mail with a specific mail address.
     *
     * @param string $mail The mail.
     *
     * @return bool True if there is a mail with this address.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isMailWithMail($mail) {
        return static::getMailWithMail($mail) instanceof Mail;
    }

    /**
     * Create a new mail.
     *
     * @param User $user The user.
     * @param string $mail The mail.
     * @param DateTime|null $creationDateTime Creation date time or null.
     * @param DateTime|null $verificationDateTime Verification date time or null.
     * @param string|null $verificationIp Verification IP.
     *
     * @return Mail The created mail as object.
     *
     * @throws Exception throws if an error occurred.
     */
    public static function createMail($user, $mail, $creationDateTime = null, $verificationDateTime = null, $verificationIp = null) {
        // Validate the user instance
        if(!($user instanceof User))
            throw new Exception('The user is invalid.');

        // Validate the mail
        if(!AccountUtils::isValidMail($mail))
            throw new Exception('The mail is invalid.');

        // Make sure this mail is unique
        if(static::isMailWithMail($mail))
            throw new Exception('This mail already exists.');

        // Determine the creation date time
        if($creationDateTime === null)
            $creationDateTime = DateTime::now();

        // Determine the verification date time
        if($verificationDateTime === null)
            $verificationDateTime = DateTime::now();

        // Determine the IP
        if($verificationIp === null)
            $verificationIp = IpUtils::getClientIp();

        // Prepare a query for the picture being added
        $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
            ' (mail_user_id, mail_mail, mail_create_datetime, mail_verified_datetime, mail_verified_ip) ' .
            'VALUES (:user_id, :mail_mail, :mail_create_datetime, :mail_verified_datetime, :mail_verified_ip)');
        $statement->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);
        $statement->bindValue(':mail_mail', $mail, PDO::PARAM_STR);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':mail_create_datetime', $creationDateTime->toString(), PDO::PARAM_STR);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':mail_verified_datetime', $verificationDateTime->toString(), PDO::PARAM_STR);
        $statement->bindValue(':mail_verified_ip', $verificationIp, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the created mail as object
        return new Mail(Database::getPDO()->lastInsertId());
    }
}