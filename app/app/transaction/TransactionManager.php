<?php

namespace app\transaction;

use app\config\Config;
use app\database\Database;
use app\money\MoneyAmount;
use app\user\User;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class TransactionManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'transactions';

    /**
     * Get the database table name of the transactions.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get a list of all transactions.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array All transactions.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getTransactions() {
        // Build a query to select the transactions
        $query = 'SELECT transaction_id FROM ' . static::getDatabaseTableName();

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // The list of transactions
        $transactions = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $transactions[] = new Transaction($data['transaction_id']);

        // Return the list of transactions
        return $transactions;
    }

    /**
     * Get the number of transactions.
     *
     * @return int Number of transactions.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getTransactionCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT transaction_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any transaction with the specified ID.
     *
     * @param int $id The ID of the transaction to check for.
     *
     * @return bool True if any transaction exists with this ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isTransactionWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid transaction ID.');

        // Prepare a query for the database to list transactions with this ID
        $statement = Database::getPDO()->prepare('SELECT transaction_id FROM ' . static::getDatabaseTableName() . ' WHERE transaction_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any transaction found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Create a new transaction.
     *
     * @param User $user Transaction user.
     * @param MoneyAmount|int $amount Transaction money amount.
     *
     * @return Transaction The created transaction as object.
     *
     * @throws Exception throws if an error occurred.
     */
    public static function createTransaction($user, $amount) {
        // Make sure the user is valid
        if(!($user instanceof User))
            throw new Exception('Invalid user.');

        // Validate the money amount
        if(!MoneyAmount::isValidAmount($amount))
            throw new Exception('Invalid money amount.');

        // Parse the amount
        $amount = MoneyAmount::parseMoneyAmountValue($amount);

        // Get the creation date and time
        $dateTime = DateTime::now();

        // Prepare a query for the picture being added
        $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
            ' (transaction_user_id, transaction_amount, transaction_datetime) ' .
            'VALUES (:user_id, :amount, :datetime)');
        $statement->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);
        $statement->bindValue(':amount', $amount, PDO::PARAM_INT);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':datetime', $dateTime->toString(), PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Get and return the transaction instance
        return new Transaction(Database::getPDO()->lastInsertId());
    }
}