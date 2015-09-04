<?php

namespace app\transaction\meta;

use app\database\Database;
use app\transaction\Transaction;
use app\user\User;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class TransactionMeta {

    /** @var int The transaction meta ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Transaction meta ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the transaction meta ID.
     *
     * @return int The transaction meta ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific transaction meta.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list transactions with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . TransactionMetaManager::getDatabaseTableName() . ' WHERE transaction_meta_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the transaction ID.
     *
     * @return int Transaction meta transaction ID.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getTransactionId() {
        return $this->getDatabaseValue('transaction_meta_transaction_id');
    }

    /**
     * Get the transaction.
     *
     * @return Transaction Transaction meta transaction.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getTransaction() {
        return new Transaction($this->getTransactionId());
    }

    /**
     * Get the transaction user.
     *
     * @return User Transaction user.
     */
    public function getTransactionUser() {
        return $this->getTransaction()->getUser();
    }

    /**
     * Get the key.
     *
     * @return string Transaction meta key.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getKey() {
        return $this->getDatabaseValue('transaction_meta_key');
    }

    /**
     * Get the value.
     *
     * @return string Transaction meta value.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getValue() {
        return $this->getDatabaseValue('transaction_meta_value');
    }

    /**
     * Set the value of this meta.
     *
     * @param string $value The new value.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function setValue($value) {
        // Prepare a query for the meta being updated
        $statement = Database::getPDO()->prepare('UPDATE ' . TransactionMetaManager::getDatabaseTableName() .
            ' SET transaction_meta_value=:meta_value' .
            ' WHERE transaction_meta_id=:meta_id');
        $statement->bindValue(':meta_id', $this->getId(), PDO::PARAM_INT);
        $statement->bindParam(':meta_value', $value, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }

    /**
     * Delete this meta permanently.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function delete() {
        // Prepare a query for the meta being deleted
        $statement = Database::getPDO()->prepare('DELETE FROM ' . TransactionMetaManager::getDatabaseTableName() . ' WHERE transaction_meta_id=:meta_id');
        $statement->bindValue(':meta_id', $this->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }
}
