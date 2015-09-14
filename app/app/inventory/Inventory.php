<?php

namespace app\inventory;

use app\database\Database;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class Inventory {

    /** @var int The inventory ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Inventory ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the inventory ID.
     *
     * @return int The inventory ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific inventory.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list inventories with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . InventoryManager::getDatabaseTableName() . ' WHERE inventory_id=:inventory_id');
        $statement->bindValue(':inventory_id', $this->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the inventory name.
     *
     * @return string Inventory name.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getName() {
        return $this->getDatabaseValue('inventory_name');
    }

    /**
     * Set the inventory name.
     *
     * @param string $name Inventory name.
     * @param bool $updateModificationDateTime [optional] True to update the modification date time, false if not.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function setName($name, $updateModificationDateTime = true) {
        // Prepare a query for the meta being updated
        $statement = Database::getPDO()->prepare('UPDATE ' . InventoryManager::getDatabaseTableName() .
            ' SET inventory_name=:inventory_name' .
            ' WHERE inventory_id=:inventory');
        $statement->bindValue(':inventory', $this->getId(), PDO::PARAM_INT);
        $statement->bindParam(':inventory_name', $name, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Update the modification date time
        if($updateModificationDateTime)
            $this->setModifiedDateTime();
    }

    /**
     * Get the raw inventories creation date.
     *
     * @return string Raw inventories creation date.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getCreationDateTimeRaw() {
        return $this->getDatabaseValue('inventory_creation_datetime');
    }

    /**
     * Get the inventories creation date.
     *
     * @return DateTime Inventories creation date.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getCreationDateTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getCreationDateTimeRaw());
    }

    /**
     * Get the raw inventories modification date.
     *
     * @return DateTime Raw inventories modification date.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getModificationDateTimeRaw() {
        return $this->getDatabaseValue('inventory_modification_datetime');
    }

    /**
     * Get the inventories modification date.
     *
     * @return DateTime Inventories modification date.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getModificationDateTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getModificationDateTimeRaw());
    }

    /**
     * Set the inventories modification date time.
     *
     * @param DateTime|null $dateTime [optional] The modification date time, or null to use the current.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function setModifiedDateTime($dateTime = null) {
        // Parse the date time and make sure it's valid
        if(($dateTime = DateTime::parse($dateTime)) === null)
            throw new Exception('Invalid date time.');

        // Prepare a query to set the inventory modification date time
        $statement = Database::getPDO()->prepare('UPDATE ' . InventoryManager::getDatabaseTableName() .
            ' SET inventory_modification_datetime=:modification_datetime' .
            ' WHERE inventory_id=:inventory_id');
        $statement->bindValue(':inventory_id', $this->getId(), PDO::PARAM_INT);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':modification_datetime', $dateTime->toString(), PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }

    /**
     * Delete this inventory.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function delete() {
        // Prepare a query for the inventory being deleted
        $statement = Database::getPDO()->prepare('DELETE FROM ' . InventoryManager::getDatabaseTableName() . ' WHERE inventory_id=:inventory_id');
        $statement->bindValue(':inventory_id', $this->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }
}
