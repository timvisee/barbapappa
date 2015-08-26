<?php

namespace app\user;

use app\config\Config;
use app\database\Database;
use app\user\meta\UserMeta;
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
        // TODO: Parse as GMT time!
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
}
