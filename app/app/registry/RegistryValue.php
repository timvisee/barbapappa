<?php

namespace app\registry;

use app\database\Database;
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
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . RegistryManager::getDatabaseTableName() . ' WHERE registry_id=:id');
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
     * Set the value of this registry value.
     *
     * @param string $value The new value.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function setValue($value) {
        // Prepare a query to update the registry value
        $statement = Database::getPDO()->prepare('UPDATE ' . RegistryManager::getDatabaseTableName() .
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
        $statement = Database::getPDO()->prepare('DELETE FROM ' . RegistryManager::getDatabaseTableName() . ' WHERE registry_id=:id');
        $statement->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }
}
