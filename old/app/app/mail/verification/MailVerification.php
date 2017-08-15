<?php

namespace app\mail\verification;

use app\config\Config;
use app\database\Database;
use app\language\LanguageManager;
use app\mail\Mail;
use app\mail\MailManager;
use app\mailsender\MailSender;
use app\user\User;
use carbon\core\datetime\DateTime;
use carbon\core\exception\datetime\zone\InvalidDateTimeZoneException;
use carbon\core\util\StringUtils;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class MailVerification {

    /** @var int The mail verification ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Mail verification ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the mail verification ID.
     *
     * @return int The mail verification ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific mail verification.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list mail verifications with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . MailVerificationManager::getDatabaseTableName() . ' WHERE mail_ver_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the mail verification user.
     *
     * @return User Mail verification user.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getUser() {
        return new User($this->getDatabaseValue('mail_ver_user_id'));
    }

    /**
     * Get the mail verification mail address.
     *
     * @return string Mail verification mail address.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getMail() {
        return $this->getDatabaseValue('mail_ver_mail');
    }

    /**
     * Get the mail verification key.
     *
     * @return string Mail verification mail address.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getKey() {
        return $this->getDatabaseValue('mail_ver_key');
    }

    /**
     * Check whether this mail verification has the given key.
     *
     * @param string $key The key to compare.
     *
     * @return bool True if this mail verification has the given key, false if not.
     */
    public function isKey($key) {
        // Make sure the key is a string
        if(!is_string($key))
            return false;

        // Trim and compare the keys, return the result
        return StringUtils::equals($key, $this->getKey(), false, true);
    }

    /**
     * Get the mail verification creation date time.
     *
     * @return DateTime Mail verification creation date time.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getCreationDateTime() {
        // TODO: Parse in the proper timezone!
        return new DateTime($this->getDatabaseValue('mail_ver_create_datetime'));
    }

    /**
     * Get the mail verification previous mail ID if there is any.
     *
     * @return int|null Mail verification previous mail ID, or null.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getPreviousMailId() {
        return $this->getDatabaseValue('mail_ver_previous_mail_id');
    }

    /**
     * Get the mail verification previous mail as mail object if there is any.
     *
     * @return Mail|null Mail verification previous mail as mail object, or null.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getPreviousMail() {
        // Get the mail ID and make sure it's valid
        if(($mailId = $this->getPreviousMailId()) === null)
            return null;

        // Return the mail as an object
        return new Mail($mailId);
    }

    /**
     * Check whether this mail verification has a previous mail.
     *
     * @return bool True if this mail verification has a previous mail, false if not.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function hasPreviousMail() {
        return is_numeric($this->getPreviousMailId());
    }

    /**
     * Get the mail verification expiration date time.
     *
     * @return DateTime Mail verification expiration date time.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getExpirationDateTime() {
        // TODO: Parse in the proper timezone!
        return new DateTime($this->getDatabaseValue('mail_ver_expire_datetime'));
    }

    /**
     * Delete this mail verification.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function delete() {
        // Prepare a query for the mail verification being deleted
        $statement = Database::getPDO()->prepare('DELETE FROM ' . MailVerificationManager::getDatabaseTableName() . ' WHERE mail_ver_id=:mail_ver_id');
        $statement->bindValue(':mail_ver_id', $this->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }

    /**
     * Send the verification message to the user.
     */
    public function sendVerificationMessage() {
        // Get the user
        $user = $this->getUser();

        // Get the language to use for this mail
        $lang = LanguageManager::getPreferredLanguageFromUser($user);

        // Determine the subject
        $subject = html_entity_decode(LanguageManager::getValue('mail', 'mailVerification'));

        // Set the preferred mail language and user
        MailSender::setPreferredLanguageTag(LanguageManager::getPreferredLanguage());
        MailSender::setUser($this->getUser());

        $hello = LanguageManager::getValue('general', 'hello', '', $lang)  . ' ' . $user->getFullName();
        $lead = LanguageManager::getValue('mail', 'youAreAboutToActivateMail', '', $lang);

        $activationLink = Config::getValue('general', 'site_url', '') . 'mailverification.php?a=verify&key=' . $this->getKey();

        $part2 = LanguageManager::getValue('mail', 'clickLinkBellowToActivate') . '<br />';
        $part2 .= '<br />';
        $part2 .= MailSender::getMessageTextLink($activationLink, LanguageManager::getValue('mail', 'activateMyMailAddress', '', $lang) . ' &raquo;');

        $part3 = LanguageManager::getValue('mail', 'userCredentialsAreAsFollows', '', $lang) . '<br />';
        $part3 .= LanguageManager::getValue('account', 'username', '', $lang) . ': <i>' . $user->getUsername() . '</i><br />';
        $part3 .= '<br />';
        $part3 .= LanguageManager::getValue('app', 'theAppTeam', '', $lang) . '<br />';

        $message = MailSender::getTop();
        $message .= MailSender::getHeader($subject);
        $message .= MailSender::getMessageTop();
        $message .= MailSender::getMessageHeader($hello, $lead);
        $message .= MailSender::getMessageText(LanguageManager::getValue('mail', 'beforeYouCanUseYouMustVerify', '', $lang));
        $message .= MailSender::getMessageNotice($part2);
        $message .= MailSender::getMessageText($part3);
        $message .= MailSender::getMessageBottom();
        $message .= MailSender::getFooter();
        $message .= MailSender::getBottom();

        // Send the message
        MailSender::sendMail($this->getMail(), $subject, $message);
    }

    /**
     * Verify the mail verification.
     *
     * @throws Exception|InvalidDateTimeZoneException Throws if an error occurred.
     */
    public function verify() {
        // Get the user, mail address and creation date
        $user = $this->getUser();
        $address = $this->getMail();
        $creationDateTime = $this->getCreationDateTime();

        // Get the current mails for this user
        $mails = MailManager::getMailsFromUser($user);
        $mailsCount = sizeof($mails);

        // Add the mail address
        $mail = MailManager::createMail($user, $address, $creationDateTime, DateTime::now(), null);

        // Make sure the mail is valid
        if($mail === null)
            throw new Exception('Failed to verify email address.');

        // Delete the previous mail if there is any
        if($this->hasPreviousMail()) {
            // Get the previous mail
            $previous = $this->getPreviousMail();

            // Delete the previous mail
            $previous->delete();
            $previous = null;
        }

        // Remove all mails waiting for verification with this mail address
        MailVerificationManager::deleteWithMail($address);

        // Delete the verification
        $this->delete();

        // Send a welcome message if this action activates the user's account
        if($mailsCount == 0)
            $user->sendWelcomeMessage($address);

        // Set this as primary mail address if the user doesn't have a primary address yet
        if(!$user->hasPrimaryMail())
            $user->setPrimaryMail($mail);

        // Return the mail as object
        return $mail;
    }
}
