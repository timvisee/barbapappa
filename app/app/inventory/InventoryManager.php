<?php

namespace app\inventory;

use app\config\Config;
use app\database\Database;
use app\money\MoneyAmount;
use app\user\User;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class InventoryManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'inventory';

    /**
     * Get the database table name of the inventories.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get a list of all inventories.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array All inventories.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getInventories() {
        // Build a query to select the inventories
        $query = 'SELECT inventory_id FROM ' . static::getDatabaseTableName();

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // The list of inventories
        $inventories = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $inventories[] = new Inventory($data['inventory_id']);

        // Return the list of inventories
        return $inventories;
    }

    /**
     * Get the number of inventories.
     *
     * @return int Number of inventories.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getInventoryCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT inventory_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any inventory with the specified ID.
     *
     * @param int $id The ID of the inventory to check for.
     *
     * @return bool True if any inventory exists with this ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isInventoryWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid inventory ID.');

        // Prepare a query for the database to list inventories with this ID
        $statement = Database::getPDO()->prepare('SELECT inventory_id FROM ' . static::getDatabaseTableName() . ' WHERE inventory_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any inventory found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Create a new inventory.
     *
     * @param string $name Inventory name.
     *
     * @return Inventory The created inventory as object.
     *
     * @throws Exception throws if an error occurred.
     */
    public static function createInventory($name) {
        // Trim the name
        $name = trim($name);

        // Make sure the name is valid
        if(strlen($name) > 0)
            throw new Exception('Invalid inventory name.');

        // Determine the creation date and time
        $dateTime = DateTime::now();

        // Prepare a query for for the inventory being added
        $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
            ' (inventory_name, inventory_creation_datetime) ' .
            'VALUES (:inventory_name, :creation_datetime)');
        $statement->bindValue(':inventory_name', $name, PDO::PARAM_STR);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':creation_datetime', $dateTime->toString(), PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Get and return the inventory instance
        return new Inventory(Database::getPDO()->lastInsertId());
    }
}