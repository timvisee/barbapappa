<?php

namespace app\registry;

use app\config\Config;
use app\database\Database;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class Registry {

    /** The database table name. */
    const DB_TABLE_NAME = 'registry';

    /**
     * Get the database table name of the registry.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get a list of all registry values.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array All registry values.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getValues() {
        // Build a query to select all registry values
        $query = 'SELECT registry_id FROM ' . static::getDatabaseTableName();

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // The list of registry values
        $values = Array();

        // Add each registry value to the list
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $values[] = new RegistryValue($data['registry_id']);

        // Return the list of registry values
        return $values;
    }

    /**
     * Get the number of registry values.
     *
     * @return int Number of registry values.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getValuesCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT registry_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any registry value with the specified ID.
     *
     * @param int $id The ID of the registry to check for.
     *
     * @return bool True if any registry exists with this ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isValueWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid registry ID.');

        // Prepare a query for the database to list the registry values for this ID
        $statement = Database::getPDO()->prepare('SELECT registry_id FROM ' . static::getDatabaseTableName() . ' WHERE registry_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any registry found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Get a registry value by it's keys.
     *
     * @param string $key The key.
     *
     * @return RegistryValue|null The registry value as object, or null if no was found.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getValue($key) {
        // Trim the key
        // TODO: Parse the registry key
        $key = trim($key);

        // Make sure a value exists for this registry with this key
        if(!static::isValueWithKey($key))
            return null;

        // Prepare a query for the database to list registrys with this ID
        $statement = Database::getPDO()->prepare('SELECT registry_id FROM ' . Registry::getDatabaseTableName() .
            ' WHERE registry_key=:registry_key');
        $statement->bindValue(':registry_key', $key, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the as object
        return new RegistryValue($statement->fetch(PDO::FETCH_ASSOC)['registry_id']);
    }

    /**
     * Set a registry value. If the value doesn't exist yet, it will be created.
     *
     * @param string $key The key.
     * @param string $value The value.
     *
     * @return RegistryValue The registry as object.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function setValue($key, $value) {
        // Trim the key
        // TODO: Parse the registry key
        $key = trim($key);

        // Create this if it hasn't been set already
        if(!static::isValueWithKey($key)) {
            // Prepare a query for the being created
            $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
                ' (registry_key, registry_value, registry_modified_datetime) VALUES (:registry_key, :registry_value, :modified_datetime)');
            $statement->bindValue(':registry_key', $key, PDO::PARAM_STR);
            $statement->bindValue(':registry_value', $value, PDO::PARAM_STR);
            // TODO: Use the UTC/GMT timezone!
            $statement->bindValue(':modified_datetime', DateTime::now()->toString(), PDO::PARAM_STR);

            // Execute the prepared query
            if(!$statement->execute())
                throw new Exception('Failed to query the database.');

            // Return the created registry as object
            return new RegistryValue(Database::getPDO()->lastInsertId());
        }

        // Get the object
        $registryValue = static::getValue($key);

        // Set the registry value
        $registryValue->setValue($value);

        // Return the object
        return $registryValue;
    }

    /**
     * Check if there's a registry value with a specific key.
     *
     * @param string $key The key.
     *
     * @return bool True if any registry value exists with this key.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isValueWithKey($key) {
        // Trim the key
        // TODO: Parse the registry key
        $key = trim($key);

        // Make sure the key is valid
        if(strlen($key) <= 0)
            throw new Exception('Invalid key.');

        // Prepare a query for the database to list all registry values with the specified key
        $statement = Database::getPDO()->prepare('SELECT registry_id FROM ' . static::getDatabaseTableName() . ' WHERE registry_key=:registry_key');
        $statement->bindValue(':registry_key', $key, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any registry found with this ID
        return $statement->rowCount() > 0;
    }
}