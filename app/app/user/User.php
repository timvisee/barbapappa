<?php

namespace app\user;

use app\config\Config;
use app\database\Database;
use app\language\LanguageManager;
use app\mail\Mail;
use app\mail\MailManager;
use app\mailsender\MailSender;
use app\user\meta\UserMeta;
use app\user\meta\UserMetaManager;
use carbon\core\datetime\DateTime;
use carbon\core\hash\Hash;
use carbon\core\util\StringUtils;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class User {

    /** @var int The user ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id User ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the user ID.
     *
     * @return int The user ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific user.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list users with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . UserManager::getDatabaseTableName() . ' WHERE user_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the username.
     *
     * @return string User username.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getUsername() {
        return $this->getDatabaseValue('user_username');
    }

    /**
     * Get the password hash.
     *
     * @return string User password hash.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    private function getPasswordHash() {
        return $this->getDatabaseValue('user_pass_hash');
    }

    /**
     * Get the user's hash salt.
     *
     * @return string User hash salt.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    private function getHashSalt() {
        return $this->getDatabaseValue('user_hash_salt');
    }

    /**
     * Generate a password hash for a user.
     *
     * @param string $password The password to hash.
     * @param string $userSalt The salt for this user.
     *
     * @return string The hashed password.
     */
    public static function generatePasswordHash($password, $userSalt) {
        // Build the password salt
        $salt = Config::getValue('hash', 'salt') . $userSalt;

        // Hash and return the password
        return Hash::hash($password, null, $salt);
    }

    /**
     * Check whether this user has a specific password.
     *
     * @param string $password The password to compare the users password to, in plain text.
     *
     * @return bool True if the password is correct, false if not.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function isPassword($password) {
        // Hash the password
        $passwordHash = static::generatePasswordHash($password, $this->getHashSalt());

        // Compare the hashes, return the result
        return StringUtils::equals($passwordHash, $this->getPasswordHash(), false, true);
    }

    /**
     * Get the user's creation date and time.
     *
     * @return DateTime User's creation date and time.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getCreationDateTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getDatabaseValue('user_create_datetime'));
    }

    /**
     * Get the creation IP.
     *
     * @return string User creation IP.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getCreationIp() {
        return $this->getDatabaseValue('user_create_ip');
    }

    /**
     * Get the full name.
     *
     * @return string User's full name.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getFullName() {
        return $this->getDatabaseValue('user_name_full');
    }

    /**
     * Check if this user has at least one verified mail address.
     *
     * @return bool True if the user has a verified mail address.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function isVerified() {
        // Get the verified mails for this user
        $mails = MailManager::getMailsFromUser($this);

        // Return true if the user has at least one verified mail
        return sizeof($mails) > 0;
    }

    /**
     * Get the primary mail ID for a user if set.
     *
     * @return int Primary mail ID if set.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function getPrimaryMailId() {
        return $this->getDatabaseValue('user_primary_mail_id');
    }

    /**
     * Get the primary mail address, or null if none is set.
     *
     * @return Mail|null Primary mail address or null.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function getPrimaryMail() {
        // Get the mail ID
        $mailId = $this->getPrimaryMailId();

        // Return the mail as object or null
        if($mailId != null && $mailId != 0)
            return new Mail($mailId);
        return null;
    }

    /**
     * Check if this user has a primary mail.
     *
     * @return bool True if this user has a primary mail, false if not.
     */
    public function hasPrimaryMail() {
        return $this->getPrimaryMail() != null;
    }

    /**
     * Set the users primary mail address.
     *
     * @param Mail|null $mail The mail instance, or null to reset the instance.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function setPrimaryMail($mail = null) {
        // Make sure the mail is valid
        if($mail == 0)
            $mail = null;
        if($mail != null && !($mail instanceof Mail))
            throw new Exception('Invalid mail instance.');

        // Get the mail ID
        $mailId = null;
        if($mail instanceof Mail)
            $mailId = $mail->getId();

        // Prepare a query to set the primary mail address
        $statement = Database::getPDO()->prepare('UPDATE ' . UserManager::getDatabaseTableName() .
            ' SET user_primary_mail_id=:mail_id' .
            ' WHERE user_id=:user_id');
        $statement->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
        $statement->bindValue(':mail_id', $mailId, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }

    /**
     * Get a user meta value if it exists.
     *
     * @param string $key The meta key.
     *
     * @return string|null The meta value, or null if it doesn't exist.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function getMeta($key) {
        // Get the user meta with this key, and make sure it's valid
        if(($meta = UserMetaManager::getUserMeta($this, $key)) === null)
            return null;

        // Return the meta value
        return $meta->getValue();
    }

    /**
     * Set user meta. If the meta doesn't exist it is created.
     *
     * @param string $key The meta key.
     * @param string $value The meta value.
     *
     * @return UserMeta The user meta as object.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function setMeta($key, $value) {
        return UserMetaManager::setUserMeta($this, $key, $value);
    }

    /**
     * Check whether the user has meta with a specific key.
     *
     * @param string $key The meta key.
     *
     * @return bool True if this meta exists, false if not.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function hasMeta($key) {
        return UserMetaManager::isUserMetaWithKey($this, $key);
    }

    /**
     * Delete meta if it exists.
     *
     * @param string $key The meta key.
     *
     * @return bool True if any meta was deleted, false otherwise.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function deleteMeta($key) {
        // Get the user meta with this key, and make sure it's valid
        if(($meta = UserMetaManager::getUserMeta($this, $key)) === null)
            return false;

        // Delete the meta
        $meta->delete();
        return true;
    }

    /**
     * Send a welcome message to the user.
     *
     * @param string|null $mail The mail address to send the message to, or null.
     *
     * @throws Exception Throws if an error ocucrred.
     */
    public function sendWelcomeMessage($mail = null) {
        // Parse the mail
        if($mail === null)
            $mail = $this->getPrimaryMail()->getMail();

        // TODO: Determine the message language based on the preferred language of the user

        // Language
        $lang = null;

        // Set the preferred mail language
        MailSender::setPreferredLanguageTag(LanguageManager::getPreferredLanguage());

        // Determine the subject
        $subject = html_entity_decode(LanguageManager::getValue('mail', 'welcomeToOurService'));

        // Build the hello and lead sentences
        $hello = LanguageManager::getValue('general', 'welcome', '', $lang)  . ' ' . $this->getFullName();
        $lead = LanguageManager::getValue('mail', 'weWouldLikeToWelcomeYouAccountActivated', '', $lang);

        // Get the login link
        $loginLink = Config::getValue('general', 'site_url', '') . 'login.php?user=' . $mail;

        // Build part 1
        $part1 = LanguageManager::getValue('mail', 'justActivatedMailCanNowLogin', '', $lang);

        // Build part 2
        $part2 = LanguageManager::getValue('mail', 'clickLinkBellowToLogin') . '<br />';
        $part2 .= '<br />';
        $part2 .= MailSender::getMessageTextLink($loginLink, LanguageManager::getValue('account', 'loginOnAccount', '', $lang) . ' &raquo;');

        // Build part 3
        $part3 = LanguageManager::getValue('mail', 'thankYouJoiningService', '', $lang) . '<br />';
        $part3 .= '<br />';
        $part3 .= LanguageManager::getValue('app', 'theAppTeam', '', $lang) . '<br />';

        // Build the actual message
        $message = MailSender::getTop();
        $message .= MailSender::getHeader($subject);
        $message .= MailSender::getMessageTop();
        $message .= MailSender::getMessageHeader($hello, $lead);
        $message .= MailSender::getMessageText($part1);
        $message .= MailSender::getMessageNotice($part2);
        $message .= MailSender::getMessageText($part3);
        $message .= MailSender::getMessageBottom();
        $message .= MailSender::getFooter();
        $message .= MailSender::getBottom();

        // Send the message
        MailSender::sendMail($mail, $subject, $message);
    }
}
