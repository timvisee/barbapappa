<?php

// TODO: This is a temporary class, used for development and should be replaced by a class written from scratch!

namespace carbon\core\cache\simplecache;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

// TODO: Better class naming!

/**
 * A high speed, simple caching class which allows variables to be cached temporarily.
 * This class provides an easy to use interface to manage cached values.
 *
 * @package carbon\core\cache\simplecache
 */
class SimpleCache {

    /** @var array Array holding all cached values. */
    private $cache = Array();

    /**
     * Constructor
     */
    public function __construct() { }

    /**
     * Get a cached value.
     *
     * @param mixed $key Valid cache key.
     * @param mixed $default [optional] Default value returned on failure or if the cached value didn't exist.
     *
     * @return mixed Cached value. The default value will be returned on failure or if the cached value didnt' exist.
     */
    public function get($key, $default = null) {
        // Make sure this cache entry exists
        if(!$this->has($key))
            return $default;

        // Return the cache value
        return @$this->cache[$key];
    }

    /**
     * Cache a value. This overwrites a cached value with the same key.
     *
     * @param mixed $key Valid cache key.
     * @param mixed $value Cache value
     *
     * @return bool True on success, false on failure due to an invalid cache key.
     */
    public function set($key, $value) {
        // Make sure the cache key is valid
        if(!$this->isValidKey($key))
            return false;

        // Set the cache value and return true
        $this->cache[$key] = $value;
        return true;
    }

    /**
     * Check whether a value is cached based on it's cache key.
     *
     * @param mixed $key Valid cache key.
     *
     * @return bool True if the cached value exists, false otherwise.
     */
    public function has($key) {
        // Make sure the cache key is valid
        if(!$this->isValidKey($key))
            return false;

        // Check whether the cache key exists, return the result
        return isset($this->cache[$key]);
    }

    /**
     * Delete or unset a cached value.
     *
     * @param mixed $key Valid cache key.
     *
     * @return bool True if any cached value was deleted, false otherwise.
     */
    public function delete($key) {
        // Make sure this cache entry exists
        if(!$this->has($key))
            return false;

        // Delete the cache value, return the result
        unset($this->cache[$key]);
        return true;
    }

    /**
     * Get the number of cached values.
     *
     * @return int Number of cached values.
     */
    public function getSize() {
        return sizeof($this->cache);
    }

    /**
     * Flush the cache
     *
     * @return int Number of flushed cache values, a negative number on failure.
     */
    public function flush() {
        // Get the number of cached values
        $count = $this->getSize();

        // Flush the cache and return the number of flushed values
        $this->cache = Array();
        return $count;
    }

    /**
     * Check whether a cache key is valid or not.
     *
     * @param mixed $key Cache key to check.
     *
     * @return bool True if the cache key is valid, false otherwise.
     */
    public function isValidKey($key) {
        return !empty($key);
    }
}
