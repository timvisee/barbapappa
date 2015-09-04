<?php

namespace app\balance;

use app\database\Database;
use app\money\MoneyAmount;
use app\user\User;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class Balance {

    /** @var int The balance ID. */
    private $id;

    /**
     * Constructor.
     *
     * @param int $id Balance ID.
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Get the balance ID.
     *
     * @return int The balance ID.
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get a value from the database from this specific balance.
     *
     * @param string $columnName The column name.
     *
     * @return mixed The value.
     *
     * @throws Exception Throws if an error occurred.
     */
    private function getDatabaseValue($columnName) {
        // Prepare a query for the database to list balances with this ID
        $statement = Database::getPDO()->prepare('SELECT ' . $columnName . ' FROM ' . BalanceManager::getDatabaseTableName() . ' WHERE balance_id=:id');
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return the result
        return $statement->fetch(PDO::FETCH_ASSOC)[$columnName];
    }

    /**
     * Get the balance user ID.
     *
     * @return int Balance user ID.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getUserId() {
        return $this->getDatabaseValue('balance_user_id');
    }

    /**
     * Get the balance user.
     *
     * @return User Balance user.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getUser() {
        return new User($this->getUserId());
    }

    /**
     * Get the balance amount.
     *
     * @return int Balance amount.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getAmount() {
        return (int) $this->getDatabaseValue('balance_amount');
    }

    /**
     * Get the balance money amount.
     *
     * @return MoneyAmount Balance money amount.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getMoneyAmount() {
        return new MoneyAmount($this->getAmount());
    }

    /**
     * Set the balance money amount.
     *
     * @param MoneyAmount|int $amount The money amount to set to.
     *
     * @throws Exception Throws if an error occurred.
     */
    // TODO: Force a transaction to be supplied, to prevent bugs?
    public function setAmount($amount) {
        // Parse the money amount
        if(is_numeric($amount))
            $amount = (int) $amount;
        if(is_int($amount))
            $amount = new MoneyAmount($amount);
        if(!($amount instanceof MoneyAmount))
            throw new Exception('Invalid money amount.');

        // Prepare a query to set the money amount
        $statement = Database::getPDO()->prepare('UPDATE ' . BalanceManager::getDatabaseTableName() .
            ' SET balance_amount=:amount' .
            ' WHERE balance_user_id=:user_id');
        $statement->bindValue(':user_id', $this->getUserId(), PDO::PARAM_INT);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':amount', $amount->getAmount(), PDO::PARAM_INT);

        // Set the modified date time
        $this->setBalanceModifiedDateTime();

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }

    /**
     * Get the balance modified date time.
     *
     * @return DateTime Balance modified date time.
     *
     * @throws Exception Throws an exception if an error occurred.
     */
    public function getBalanceModifiedDateTime() {
        // TODO: Use the proper timezone!
        return new DateTime($this->getDatabaseValue('balance_modified_datetime'));
    }

    /**
     * Set the balance modified date time.
     *
     * @param DateTime|null $dateTime The modified date time or null to use the current time.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function setBalanceModifiedDateTime($dateTime = null) {
        // Parse the date time and make sure it's valid
        if(($dateTime = DateTime::parse($dateTime)) === null)
            throw new Exception('Invalid date time.');

        // Prepare a query to set the modified date time
        $statement = Database::getPDO()->prepare('UPDATE ' . BalanceManager::getDatabaseTableName() .
            ' SET balance_modified_datetime=:modified_datetime' .
            ' WHERE balance_user_id=:user_id');
        $statement->bindValue(':user_id', $this->getUserId(), PDO::PARAM_INT);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':modified_datetime', $dateTime->toString(), PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');
    }
}
