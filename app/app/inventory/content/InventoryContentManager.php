<?php

namespace app\inventory\content;

use app\config\Config;
use app\database\Database;
use app\inventory\Inventory;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class InventoryContentManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'inventory_content';

    /**
     * Get the database table name of the inventory content.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get a list of all inventory content.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array All inventory content.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getInventories() {
        // Build a query to select the inventories
        $query = 'SELECT content_id FROM ' . static::getDatabaseTableName();

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // The list of inventory content
        $inventories = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $inventories[] = new InventoryContent($data['content_id']);

        // Return the list of inventory content
        return $inventories;
    }

    /**
     * Get the number of inventory content.
     *
     * @return int Number of inventory content.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getInventoryContentCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT content_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any inventory content with the specified ID.
     *
     * @param int $id The ID of the inventory content to check for.
     *
     * @return bool True if any inventory content exists with this ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isInventoryContentWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid inventory ID.');

        // Prepare a query for the database to list inventories with this ID
        $statement = Database::getPDO()->prepare('SELECT content_id FROM ' . static::getDatabaseTableName() . ' WHERE content_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any inventory found with this ID
        return $statement->rowCount() > 0;
    }
}