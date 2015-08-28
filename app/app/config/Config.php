<?php

namespace app\config;

use carbon\core\io\filesystem\file\File;
use Exception;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class Config {

    /** Define the location of the configuration file. */
    const CONFIG_FILE = '/config/config.php';

    /** The configuration array when loaded. */
    private static $configArray = null;

    /**
     * Get the file path of the configuration file.
     *
     * @return File The configuration file.
     */
    public static function getConfigFile() {
        return new File(CARBON_SITE_ROOT, static::CONFIG_FILE);
    }

    /**
     * Load the configuration from the file.
     *
     * @throws \Exception Throws on failure.
     */
    public static function load() {
        // Determine the location of the configuration file
        $configFile = static::getConfigFile();

        // Make sure the configuration file exists
        if(!file_exists($configFile))
            throw new \Exception('Failed to load configuration file, file doesn\'t exist!');

        // Try to load the configuration file in the con    figuration array
        /** @noinspection PhpIncludeInspection */
        static::$configArray = include($configFile->getPath());
    }

    /**
     * Flush the loaded configuration
     */
    public static function flushConfig() {
        static::$configArray = Array();
    }

    /**
     * Check if a configuration file is loaded
     *
     * @return bool True if loaded
     */
    public static function isConfigLoaded() {
        // Make sure the configuration data is an array
        if(!is_array(static::$configArray))
            return false;

        // Make sure the array contains any items
        return (sizeof(static::$configArray) > 0);
    }

    /**
     * Get akk keys from a configuration array section
     *
     * @param string $section The section to get the keys from
     * @param array|null $configArray The configuration array to get the keys from,
     * null to use the current loaded configuration array
     *
     * @return array|null Array with keys from the section, or null if the section or configuration array was invalid.
     */
    public static function getKeys($section, $configArray = null) {
        // Use the default configuration array if the param equals to null
        if($configArray == null)
            $configArray = static::$configArray;

        // The configuration array may not be null and must be an array
        if($configArray == null || !is_array($configArray))
            return false;

        // Make sure this section exists
        if(!static::hasSection($section, $configArray))
            return null;

        // Return the config keys
        return $configArray[$section];
    }

    /**
     * Get a value from a loaded configuration file
     *
     * @param string $section The section in the configuration file
     * @param string $key The key in the configuration file
     * @param mixed $default The default value returned if the key was not found
     * @param array|null $configArray The configuration array to get the value from,
     * null to use the current loaded configuration array
     *
     * @return bool The value from the configuration file, or the default value if the key was not found
     */
    public static function get($section, $key, $default = null, $configArray = null) {
        return static::getValue($section, $key, $default, $configArray);
    }

    /**
     * Get a value from a loaded configuration file
     *
     * @param string $section The section in the configuration file
     * @param string $key The key in the configuration file
     * @param mixed $default The default value returned if the key was not found
     * @param array $configArray The configuration array to get the value from,
     * null to use the current loaded configuration array
     *
     * @return bool The value from the configuration file, or the default value if the key was not found
     */
    public static function getValue($section, $key, $default = null, $configArray = null) {
        // Use the default configuration array if the param equals to null
        if($configArray === null)
            $configArray = static::$configArray;

        // The configuration array may not be null and must be an array
        if($configArray === null || !is_array($configArray))
            return false;

        // Make sure the section and the key are booth available
        if(!static::hasSection($section, $configArray) || !static::hasKey($section, $key, $configArray))
            return $default;

        // Return the value from the config
        return $configArray[$section][$key];
    }

    /**
     * Get a boolean from a loaded configuration file
     *
     * @param string $section The section in the configuration file
     * @param string $key The key in the configuration file
     * @param bool $default The default value returned if the key was not found
     * @param array $configArray The configuration array to get the value from,
     * null to use the current loaded configuration array
     *
     * @return bool The boolean from the configuration file, or the default boolean value
     */
    public static function getBool($section, $key, $default = false, $configArray = null) {
        return (bool) static::getValue($section, $key, $default, $configArray);
    }

    /**
     * Check if a configuration array section exists
     *
     * @param string $section The section to search for
     * @param array $configArray The configuration array to search in,
     * null to use the current loaded configuration array
     *
     * @return bool True if the section was found
     */
    public static function hasSection($section, $configArray = null) {
        // Use the default configuration array if the param equals to null
        if($configArray == null)
            $configArray = static::$configArray;

        // The configuration array may not be null and must be an array
        if($configArray == null || !is_array($configArray))
            return false;

        // Check if the config array contains this section
        return array_key_exists($section, $configArray);
    }

    /**
     * Check if the configuration file has a specified key
     *
     * @param string $section The section to search in
     * @param string $key The key to search for
     * @param array $configArray The configuration array to check in,
     * null to use the current loaded configuration file array
     *
     * @return bool True if the configuration file has this key
     */
    public static function hasKey($section, $key, $configArray = null) {
        // Use the default configuration array if the param equals to null
        if($configArray == null)
            $configArray = static::$configArray;

        // The configuration array may not be null and must be an array
        if($configArray == null || !is_array($configArray))
            return false;

        // Check if the config array contains this section
        if(!static::hasSection($section, $configArray))
            return false;

        // Check if this config array contains this key
        return array_key_exists($key, $configArray[$section]);
    }
}
