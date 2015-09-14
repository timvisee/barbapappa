<?php

namespace app\inventory\content;

use app\config\Config;
use app\database\Database;
use app\inventory\Inventory;
use app\inventory\InventoryManager;
use app\product\Product;
use app\product\ProductManager;
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
     * Check if there's any inventory content with the specified ID.
     * Note: This method might be very resource intensive and expensive to execute.
     *
     * @param Inventory|int|null $inventory [optional] The inventory instance, inventory ID or null to not specify the inventory.
     * @param Product|int|null $product [optional] The product instance, the product ID or null to not specify the product.
     *
     * @return Array The inventory contents for this query.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getContents($inventory = null, $product = null) {
        // Get the inventory ID
        $inventoryId = null;
        if($inventory !== null) {
            // Get the inventory ID if it's an inventory instance
            if($inventory instanceof Inventory)
                $inventoryId = $inventory->getId();

            else {
                // Make sure the inventory is a numerical value
                if(!is_numeric($inventory))
                    throw new Exception('Invalid inventory instance.');

                // Check whether the inventory ID exists
                if(!InventoryManager::isInventoryWithId($inventory))
                    throw new Exception('Unknown inventory ID.');

                // Set the inventory ID
                $inventoryId = $inventory;
            }
        }

        // Get the product ID
        $productId = null;
        if($product !== null) {
            // Get the product ID if it's an product instance
            if($product instanceof Product)
                $productId = $product->getId();

            else {
                // Make sure the product is a numerical value
                if(!is_numeric($product))
                    throw new Exception('Invalid product instance.');

                // Check whether the product ID exists
                if(!ProductManager::isProductWithId($product))
                    throw new Exception('Unknown product ID.');

                // Set the product ID
                $productId = $product;
            }
        }

        // Build the query
        $query = 'SELECT content_id FROM ' . static::getDatabaseTableName();

        // Check whether to add a where clause
        if($inventoryId !== null || $productId !== null) {
            // Add the where clause
            $query .= ' WHERE';

            // Check weather to add the inventory ID
            if($inventory !== null)
                $query .= ' content_inventory_id=:inventory_id';

            // Check weather to add the product ID
            if($productId !== null) {
                // Should we add the AND keyword first
                if($inventory !== null)
                    $query .= ' AND';

                // Add the product ID
                $query .= 'content_product_id=:product_id';
            }
        }

        // Prepare a query for the database to list inventories with this ID
        $statement = Database::getPDO()->prepare($query);

        // Bind the values
        if($inventoryId !== null)
            $statement->bindValue(':inventory_id', $inventoryId, PDO::PARAM_INT);
        if($productId !== null)
            $statement->bindValue(':product_id', $productId, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // The list of inventory contents
        $contents = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $contents[] = new InventoryContent($data['content_id']);

        // Return the list of inventory contents
        return $contents;
    }

    /**
     * Get the number of inventory content.
     *
     * @return int Number of inventory content.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getContentCount() {
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
    public static function isContentWithId($id) {
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

    /**
     * Get all inventory contents from the specified inventory.
     * Note: This method might be very resource intensive and expensive to execute.
     *
     * @param Inventory|int|null $inventory [optional] The inventory instance, inventory ID or null to get the contents
     * from all inventories.
     *
     * @return Array The inventory contents of the specified inventory.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getContentsFromInventory($inventory = null) {
        return static::getContents($inventory, null);
    }

    /**
     * Get all inventory contents from the specified inventory.
     * Note: This method might be very resource intensive and expensive to execute.
     *
     * @param Product|int|null $product [optional] The product instance, product ID or null to get the contents for all
     * products.
     *
     * @return Array The inventory contents with the specified product.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getContentsWithProduct($product = null) {
        return static::getContents(null, $product);
    }
}