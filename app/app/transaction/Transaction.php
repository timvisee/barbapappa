<?php

namespace app\transaction;

use app\database\Database;
use app\money\MoneyAmount;
use app\user\User;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class Transaction {

    /** @var int The transaction ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Transaction ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the transaction ID.
     *
     * @return int The transaction ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific transaction.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list transactions with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . TransactionManager::getDatabaseTableName() . ' WHERE transaction_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the transaction user ID.
     *
     * @return int Transaction user ID.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getTransactionUserId() {
        return $this->getDatabaseValue('transaction_user_id');
    }

    /**
     * Get the transaction user.
     *
     * @return User Transaction user.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getTransactionUser() {
        return new User($this->getTransactionUserId());
    }

    /**
     * Get the transaction amount.
     *
     * @return int Transaction amount.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getAmount() {
        return (int) $this->getDatabaseValue('transaction_amount');
    }

    /**
     * Get the transaction money amount.
     *
     * @return MoneyAmount Transaction money amount.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getMoneyAmount() {
        return new MoneyAmount($this->getAmount());
    }

    /**
     * Get the transaction's date time.
     *
     * @return DateTime Transaction's date time.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getDatabaseValue('transaction_datetime'));
    }
}
