<?php

namespace app\transaction\meta;

use app\config\Config;
use app\database\Database;
use app\transaction\Transaction;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class TransactionMetaManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'transaction_meta';

    /**
     * Get the database table name of the transaction meta.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get a list of all transaction meta.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array All transaction meta.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getTransactions() {
        // Build a query to select the transactions
        $query = 'SELECT transaction_meta_id FROM ' . static::getDatabaseTableName();

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // The list of transaction meta
        $transactions = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $transactions[] = new TransactionMeta($data['transaction_meta_id']);

        // Return the list of transaction meta
        return $transactions;
    }

    /**
     * Get the number of transaction meta.
     *
     * @return int Number of transaction meta.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getTransactionMetaCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT transaction_meta_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any transaction meta with the specified ID.
     *
     * @param int $id The ID of the transaction meta to check for.
     *
     * @return bool True if any transaction meta exists with this ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isTransactionMetaWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid transaction ID.');

        // Prepare a query for the database to list transactions with this ID
        $statement = Database::getPDO()->prepare('SELECT transaction_meta_id FROM ' . static::getDatabaseTableName() . ' WHERE transaction_meta_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any transaction found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Get the transaction meta for a specific transaction and key.
     *
     * @param Transaction $transaction The meta transaction.
     * @param string $key The meta key.
     *
     * @return TransactionMeta|null The transaction meta as object, or null if no meta was found.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getTransactionMeta($transaction, $key) {
        // Trim the key
        $key = trim($key);

        // Make sure a value exists for this transaction with this key
        if(!static::isTransactionMetaWithKey($transaction, $key))
            return null;

        // Prepare a query for the database to list transactions with this ID
        $statement = Database::getPDO()->prepare('SELECT transaction_meta_id FROM ' . TransactionMetaManager::getDatabaseTableName() .
            ' WHERE transaction_meta_transaction_id=:transaction_id AND transaction_meta_key=:meta_key');
        $statement->bindValue(':transaction_id', $transaction->getId(), PDO::PARAM_INT);
        $statement->bindValue(':meta_key', $key, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the meta as object
        return new TransactionMeta($statement->fetch(PDO::FETCH_ASSOC)['transaction_meta_id']);
    }

    /**
     * Set a meta value for a transaction. If the meta value doesn't exist yet, it will be created.
     *
     * @param Transaction $transaction The transaction to set the value for.
     * @param string $key The meta key.
     * @param string $value The meta value.
     *
     * @return TransactionMeta The transaction meta as object.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function setTransactionMeta($transaction, $key, $value) {
        // Trim the key
        $key = trim($key);

        // Create this meta if it hasn't been set already
        if(!static::isTransactionMetaWithKey($transaction, $key)) {
            // Prepare a query for the meta being created
            $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
                ' (transaction_meta_transaction_id, transaction_meta_key, transaction_meta_value) VALUES (:transaction_id, :meta_key, :meta_value)');
            $statement->bindValue(':transaction_id', $transaction->getId(), PDO::PARAM_INT);
            $statement->bindValue(':meta_key', $key, PDO::PARAM_STR);
            $statement->bindValue(':meta_value', $value, PDO::PARAM_STR);

            // Execute the prepared query
            if(!$statement->execute())
                throw new Exception('Failed to query the database.');

            // Return the created transaction meta as object
            return new TransactionMeta(Database::getPDO()->lastInsertId());
        }

        // Get the meta object
        $meta = static::getTransactionMeta($transaction, $key);

        // Set the value
        $meta->setValue($value);

        // Return the object
        return $meta;
    }

    /**
     * Check if there's any meta for a transaction with a specific key.
     *
     * @param Transaction $transaction The transaction of the meta.
     * @param string $key The meta key.
     *
     * @return bool True if any meta with this key exists for this transaction.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isTransactionMetaWithKey($transaction, $key) {
        // Make sure the transaction instance is valid
        if(!($transaction instanceof Transaction))
            throw new Exception('Invalid transaction.');

        // Trim the key
        $key = trim($key);

        // Make sure the meta key is valid
        if(strlen($key) <= 0)
            throw new Exception('Invalid meta key.');

        // Prepare a query for the database to list transactions with this ID
        $statement = Database::getPDO()->prepare('SELECT transaction_meta_id FROM ' . static::getDatabaseTableName() . ' WHERE transaction_meta_transaction_id=:transaction_id AND transaction_meta_key=:meta_key');
        $statement->bindValue(':transaction_id', $transaction->getId(), PDO::PARAM_INT);
        $statement->bindValue(':meta_key', $key, PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any transaction found with this ID
        return $statement->rowCount() > 0;
    }
}