<?php

namespace app\mail\verification;

use app\config\Config;
use app\database\Database;
use app\mail\Mail;
use app\user\User;
use app\util\AccountUtils;
use carbon\core\datetime\DateTime;
use carbon\core\hash\Hash;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class MailVerificationManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'mail_verification';
    /** The time it takes for a mail verification to expire. */
    // TODO: Move this value to the registry database to make it configurable
    const MAIL_VERIFICATION_EXPIRE = '+7 day';

    /**
     * Get the database table name of the mail verifications.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get a list of all mail verifications.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array All mail verifications.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getMailVerifications() {
        // Build a query to select the mail verifications
        $query = 'SELECT mail_ver_id FROM ' . static::getDatabaseTableName();

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // The list of mail verifications
        $verifications = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $verifications[] = new MailVerification($data['mail_ver_id']);

        // Return the list of mail verifications
        return $verifications;
    }

    /**
     * Get a list of all mail verifications for a specific user.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @param User $user The user.
     *
     * @return array All mail verifications.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getMailVerificationsFromUser($user) {
        // Make sure the user is valid
        if(!($user instanceof User))
            throw new Exception('Invalid user.');

        // Prepare a query to select the mail verifications
        $statement = Database::getPDO()->prepare('SELECT mail_ver_id FROM ' . static::getDatabaseTableName() . ' WHERE mail_ver_user_id=:user_id');
        $statement->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // The list of mail verifications
        $verifications = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $verifications[] = new MailVerification($data['mail_ver_id']);

        // Return the list of mail verifications
        return $verifications;
    }

    /**
     * Get a list of all mail verifications with a specific mail address.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @param string $mail The mail address.
     *
     * @return array All mail verifications.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getMailVerificationsWithMail($mail) {
        // Validate the mail
        if(!AccountUtils::isValidMail($mail))
            throw new Exception('Invalid mail.');

        // Prepare a query to select the mail verifications
        $statement = Database::getPDO()->prepare('SELECT mail_ver_id FROM ' . static::getDatabaseTableName() . ' WHERE mail_ver_mail LIKE :mail');
        $statement->bindParam(':mail', $mail, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // The list of mail verifications
        $verifications = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $verifications[] = new MailVerification($data['mail_ver_id']);

        // Return the list of mail verifications
        return $verifications;
    }

    /**
     * Get the number of mail verifications.
     *
     * @return int Number of mail verifications.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getMailVerificationCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT mail_ver_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any mail verifications with the specified ID.
     *
     * @param int $id The ID of the mail verifications with check for.
     *
     * @return bool True if any mail verifications with with this ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isMailVerificationWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid mail verification ID.');

        // Prepare a query for the database to list mail verifications with this ID
        $statement = Database::getPDO()->prepare('SELECT mail_ver_id FROM ' . static::getDatabaseTableName() . ' WHERE mail_ver_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any mail verifications found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Check if there's any mail verifications with the specified key.
     *
     * @param string $key The key of the mail verifications with check for.
     *
     * @return bool True if any mail verifications with with this key.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isMailVerificationWithKey($key) {
        // Make sure the key isn't null
        if($key === null)
            throw new Exception('Invalid mail verification ID.');

        // Prepare a query for the database to list mail verifications with this key
        $statement = Database::getPDO()->prepare('SELECT mail_ver_id FROM ' . static::getDatabaseTableName() . ' WHERE mail_ver_key LIKE :key');
        $statement->bindParam(':key', $key, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any mail verifications found with this key
        return $statement->rowCount() > 0;
    }

    /**
     * Get the mail verification with a specific key.
     *
     * @param string $key Mail verification key.
     *
     * @return MailVerification|null The mail verification as object, or null.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getMailVerificationWithKey($key) {
        // Make sure the key is a string
        if(!is_string($key))
            throw new Exception('Invalid key.');

        // Trim the key
        $key = trim($key);

        // Prepare a query for the database to get the mailverification
        $statement = Database::getPDO()->prepare('SELECT mail_ver_id FROM ' . static::getDatabaseTableName() . ' WHERE mail_ver_key LIKE :mail_ver_key');
        $statement->bindValue(':mail_ver_key', $key, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the mail verification, or null if no mail verification was found
        if($statement->rowCount() > 0)
            return new MailVerification($statement->fetch(PDO::FETCH_ASSOC)['mail_ver_id']);
        return null;
    }

    /**
     * Create a new mail verification.
     *
     * @param User $user Mail verification user.
     * @param string $mail Mail verification mail.
     * @param Mail|null $previous [optional] Mail verification previous mail.
     *
     * @return MailVerification Created mail verification as object.
     *
     * @throws Exception throws if an error occurred.
     */
    public static function createMailVerification($user, $mail, $previous = null) {
        // Validate the user instance
        if(!($user instanceof User))
            throw new Exception('The user is invalid.');

        // Validate the mail
        if(!AccountUtils::isValidMail($mail))
            throw new Exception('Invalid mail.');

        // Validate the previous
        if($previous !== null && !($previous instanceof Mail))
            throw new Exception('Invalid previous mail instance.');

        // Generate a random mail key
        $key = static::generateNewKey();

        // Determine the creation and expiration date time
        $createDateTime = DateTime::now();
        $expireDateTime = DateTime::parse(static::MAIL_VERIFICATION_EXPIRE);

        // Prepare a query for the picture being added
        $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
            ' (mail_ver_user_id, mail_ver_mail, mail_ver_key, mail_ver_create_datetime, mail_ver_previous_mail_id, mail_ver_expire_datetime) ' .
            'VALUES (:user_id, :mail, :mail_ver_key, :create_datetime, :previous_mail_id, :expire_datetime)');
        $statement->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);
        $statement->bindValue(':mail', $mail, PDO::PARAM_STR);
        $statement->bindValue(':mail_ver_key', $key, PDO::PARAM_STR);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':create_datetime', $createDateTime->toString(), PDO::PARAM_STR);
        $statement->bindValue(':previous_mail_id', $previous, PDO::PARAM_INT);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':expire_datetime', $expireDateTime->toString(), PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Get the mail verification object
        $mailVerification = new MailVerification(Database::getPDO()->lastInsertId());

        // Send the verification message
        $mailVerification->sendVerificationMessage();

        // Return the mail verification object
        return $mailVerification;
    }

    /**
     * Generate a random mail verification key.
     *
     * @return string Mail verification key.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function generateNewKey() {
        // Generate a new, unique mail verification key
        while(true) {
            // Generate a new random key (based on a random hash salt)
            $key = substr(Hash::generateSalt(), 0, 32);

            // Return this key if it's unique
            if(!static::isMailVerificationWithKey($key))
                return $key;
        }
        return null;
    }
}