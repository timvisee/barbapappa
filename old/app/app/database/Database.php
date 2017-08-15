<?php

namespace app\database;

use app\config\Config;
use Exception;
use \PDO;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class Database {

    private static $db;

    /**
     * Connect to the database, with the credentials provided in the configuration file.
     */
    public static function connect() {
        // Retrieve the database data from the configuration
        $host = Config::getValue('database', 'host', 'localhost');
        $port = Config::getValue('database', 'port', 3306);
        $dbname = Config::getValue('database', 'database', '');
        $user = Config::getValue('database', 'user', 'root');
        $password = Config::getValue('database', 'password', '');

        // Connect to the database using PDO, store the instance globally
        try {
            static::$db = new PDO('mysql:host=' . $host . ';port=' . $port . ';dbname=' . $dbname, $user, $password);
        } catch(Exception $e) {
            throw new Exception('An error occurred while connecting to the database!');
        }
    }

    /**
     * Check if we're connected to the database.
     *
     * @return bool True if a database connection is available, false otherwise.
     */
    public static function isConnected() {
        return static::$db instanceof PDO;
    }

    /**
     * Get the PDO instance of the current database connection. An active connection to the database must be available.
     *
     * @return PDO|null The PDO instance or null on failure.
     */
    public static function getPDO() {
        return static::$db;
    }
}