<?php

namespace app\inventory\content;

use app\database\Database;
use app\inventory\Inventory;
use app\product\Product;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class InventoryContent {

    /** @var int The inventory content ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Inventory content ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the inventory content ID.
     *
     * @return int The inventory content ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific inventory content.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list inventories with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . InventoryContentManager::getDatabaseTableName() . ' WHERE content_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the inventory ID.
     *
     * @return int Inventory content inventory ID.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getInventoryId() {
        return $this->getDatabaseValue('content_inventory_id');
    }

    /**
     * Get the inventory.
     *
     * @return Inventory Inventory content inventory.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getInventory() {
        return new Inventory($this->getInventoryId());
    }

    /**
     * Get the product ID.
     *
     * @return int Product content product ID.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getProductId() {
        return $this->getDatabaseValue('content_product_id');
    }

    /**
     * Get the product.
     *
     * @return Product Product content product.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getProduct() {
        return new Product($this->getProductId());
    }

    /**
     * Get the product quantity.
     *
     * @return int Product quantity.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getQuantity() {
        return $this->getDatabaseValue('content_quantity');
    }

    /**
     * Get the content creation date.
     *
     * @return DateTime Inventory content creation date.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getCreationDateTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getDatabaseValue('content_creation_datetime'));
    }

    /**
     * Get the content modification date.
     *
     * @return DateTime Inventory content modification date.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getModificationDateTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getDatabaseValue('content_modification_datetime'));
    }

    /**
     * Delete this inventory content.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function delete() {
        // Prepare a query for the content being deleted
        $statement = Database::getPDO()->prepare('DELETE FROM ' . InventoryContentManager::getDatabaseTableName() . ' WHERE content_id=:content_id');
        $statement->bindValue(':content_id', $this->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }
}
