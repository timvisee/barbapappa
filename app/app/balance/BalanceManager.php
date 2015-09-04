<?php

namespace app\balance;

use app\config\Config;
use app\database\Database;
use app\money\MoneyAmount;
use app\user\User;
use carbon\core\datetime\DateTime;
use Exception;
use PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class BalanceManager {

    /** The database table name. */
    const DB_TABLE_NAME = 'balance';

    /**
     * Get the database table name of the balances.
     *
     * @return string The database table name.
     */
    public static function getDatabaseTableName() {
        return Config::getValue('database', 'table_prefix', '') . static::DB_TABLE_NAME;
    }

    /**
     * Get a list of all balances.
     * Note: This method is very resource intensive and expensive to execute.
     *
     * @return array All balances.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getBalances() {
        // Build a query to select the balances
        $query = 'SELECT balance_id FROM ' . static::getDatabaseTableName();

        // Execute the query
        $statement = Database::getPDO()->query($query);

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // The list of balances
        $balances = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $balances[] = new Balance($data['balance_id']);

        // Return the list of balances
        return $balances;
    }

    /**
     * Get the number of balances.
     *
     * @return int Number of balances.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getBalanceCount() {
        // Create a row count query on the database instance
        $statement = Database::getPDO()->query('SELECT balance_id FROM ' . static::getDatabaseTableName());

        // Make sure the query succeed
        if($statement === false)
            throw new Exception('Failed to query the database.');

        // Return the number of rows
        return $statement->rowCount();
    }

    /**
     * Check if there's any balance with the specified ID.
     *
     * @param int $id The ID of the balance to check for.
     *
     * @return bool True if any balance exists with this ID.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function isBalanceWithId($id) {
        // Make sure the ID isn't null
        if($id === null)
            throw new Exception('Invalid balance ID.');

        // Prepare a query for the database to list balances with this ID
        $statement = Database::getPDO()->prepare('SELECT balance_id FROM ' . static::getDatabaseTableName() . ' WHERE balance_id=:id');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Return true if there's any balance found with this ID
        return $statement->rowCount() > 0;
    }

    /**
     * Get a list of all balances for a specific user.
     * Note: This method can be very resource intensive and expensive to execute.
     *
     * @param User $user The user.
     *
     * @return array All balances.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getBalancesFromUser($user) {
        // Make sure the user is valid
        if(!($user instanceof User))
            throw new Exception('Invalid user.');

        // Prepare a query to list all balances for this user
        $statement = Database::getPDO()->prepare('SELECT balance_id FROM ' . static::getDatabaseTableName() . ' WHERE balance_user_id=:user_id');
        $statement->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // The list of balances
        $balances = Array();

        // Return the number of rows
        foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $data)
            $balances[] = new Balance($data['balance_id']);

        // Return the list of balances
        return $balances;
    }

    /**
     * Create a new balance.
     *
     * @param User $user Balance user.
     * @param MoneyAmount|int|null $amount The base balance amount, or null to use zero.
     *
     * @return Balance The created balance as object.
     *
     * @throws Exception throws if an error occurred.
     */
    public static function createBalance($user, $amount = null) {
        // Make sure the user is valid
        if(!($user instanceof User))
            throw new Exception('Invalid user.');

        // Parse null amount
        if($amount === null)
            $amount = 0;

        // Validate the money amount
        if(!MoneyAmount::isValidAmount($amount))
            throw new Exception('Invalid money amount.');

        // Parse the amount
        $amount = MoneyAmount::parseMoneyAmountValue($amount);

        // Get the modified date and time
        $dateTime = DateTime::now();

        // Prepare a query for the picture being added
        $statement = Database::getPDO()->prepare('INSERT INTO ' . static::getDatabaseTableName() .
            ' (balance_user_id, balance_amount, balance_modified_datetime) ' .
            'VALUES (:user_id, :amount, :modified_datetime)');
        $statement->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);
        $statement->bindValue(':amount', $amount, PDO::PARAM_INT);
        // TODO: Use the UTC/GMT timezone!
        $statement->bindValue(':modified_datetime', $dateTime->toString(), PDO::PARAM_STR);

        // Execute the prepared query
        if(!$statement->execute())
            throw new Exception('Failed to query the database.');

        // Get and return the balance instance
        return new Balance(Database::getPDO()->lastInsertId());
    }
}