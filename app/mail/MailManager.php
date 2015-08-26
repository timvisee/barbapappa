<?php

namespace app\mail;

use app\config\Config;
use app\database\Database;
use app\user\User;
use app\util\AccountUtils;
use carbon\core\datetime\DateTime;
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
     * Get a list of all mails.
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

        // Build a query to select the mails
        $query = 'SELECT mail_id FROM ' . static::getDatabaseTableName() . ' WHERE mail_user_id=' . $user->getId();

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
     * Create a new mail.
     *
     * @param User $user The user.
     * @param string $mail The mail.
     *
     * @return Mail The created mail as object.
     *
     * @throws Exception throws if an error occurred.
     */
    // TODO: Send a validation mail!
    public static function createMail($user, $mail) {
        // Validate the user instance
        if(!($user instanceof User))
            throw new Exception('The user is invalid.');

        // Validate the mail
        if(!AccountUtils::isValidMail($mail))
            throw new Exception('The mail is invalid.');

        // TODO: Make sure the mail is unique

        // Determine the creation date time
        $createDateTime = DateTime::now();

        // Prepare a query for the picture being added
        $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
            ' (mail_user_id, mail_mail, mail_create_datetime) ' .
            'VALUES (:user_id, :mail_mail, :mail_create_datetime)');
        $statement->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);
        $statement->bindValue(':mail_mail', $mail, PDO::PARAM_STR);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':mail_create_datetime', $createDateTime->toString(), PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the created mail as object
        return new Mail(Database::getPDO()->lastInsertId());
    }
}