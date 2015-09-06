<?php

namespace app\registry;

use app\database\Database;
use carbon\core\util\StringUtils;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class RegistryValue{

    /** @var int The registry value ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Registry value ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the registry value ID.
     *
     * @return int The registry value ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific registry value.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list registry value with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . Registry::getDatabaseTableName() . ' WHERE registry_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the key.
     *
     * @return string Registry value key.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getKey() {
        return $this->getDatabaseValue('registry_key');
    }

    /**
     * Get the value.
     *
     * @return string Registry value value.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getValue() {
        return $this->getDatabaseValue('registry_value');
    }

    /**
     * Get the value as a boolean.
     *
     * @return bool Registry value.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getBoolValue() {
        // Get the value as a string
        $value = $this->getValue();

        // Parse and return the value
        if(StringUtils::equals($value, Array('true', 't', 'yes', 'y'), false, true))
            return true;
        if(StringUtils::equals($value, Array('false', 'f', 'no', 'n'), false, true))
            return false;

        // Try to cast the value to a boolean, return the result
        return (bool) $value;
    }

    /**
     * Set the value of this registry value.
     *
     * @param string|bool $value The new value.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function setValue($value) {
        // Parse boolean values
        if(is_bool($value))
            $value = $value ? 1 : 0;

        // Prepare a query to update the registry value
        $statement = Database::getPDO()->prepare('UPDATE ' . Registry::getDatabaseTableName() .
            ' SET registry_value=:value' .
            ' WHERE registry_id=:id');
        $statement->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $statement->bindParam(':value', $value, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }

    /**
     * Delete this registry value permanently.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function delete() {
        // Prepare a query for the being deleted
        $statement = Database::getPDO()->prepare('DELETE FROM ' . Registry::getDatabaseTableName() . ' WHERE registry_id=:id');
        $statement->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }
}
