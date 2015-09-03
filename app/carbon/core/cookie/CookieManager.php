<?php

namespace carbon\core\cookie;

use carbon\core\datetime\DateTime;
use carbon\core\datetime\period\DatePeriod;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class CookieManager {

    /** @var string Cookie key prefix. */
    private static $prefix = '';
    /** @var string Set the default cookie domain. */
    private static $domain = '';
    /** @var string Set the default cookie path. */
    private static $path = '/';

    /**
     * Get the cookie key prefix.
     *
     * @return string Cookie key prefix.
     */
    public static function getCookiePrefix() {
        return static::$prefix;
    }

    /**
     * Set the cookie key prefix.
     *
     * @param string $prefix Cookie key prefix, or null for nothing.
     */
    public static function setCookiePrefix($prefix) {
        static::$prefix = ($prefix != null) ? $prefix : '';
    }

    /**
     * Get the cookie key domain.
     *
     * @return string Cookie key domain.
     */
    public static function getCookieDomain() {
        return static::$domain;
    }

    /**
     * Set the cookie key domain.
     *
     * @param string $domain Cookie key domain, or null for nothing.
     */
    public static function setCookieDomain($domain) {
        static::$domain = ($domain != null) ? $domain : '';
    }

    /**
     * Get the cookie key path.
     *
     * @return string Cookie key path.
     */
    public static function getCookiePath() {
        return static::$path;
    }

    /**
     * Set the cookie key path.
     *
     * @param string $path Cookie key path, or null for nothing.
     */
    public static function setCookiePath($path) {
        static::$path = ($path != null) ? $path : '';
    }

    /**
     * Get a cookie value.
     *
     * @param string $key Cookie key.
     *
     * @return mixed Cookie value, or null if this cookie isn't set.
     */
    public static function getCookie($key) {
        // Make sure the cookie exists
        if(!static::hasCookie($key))
            return null;

        // Return the cookie value
        return $_COOKIE[static::getFullCookieKey($key)];
    }

    /**
     * Set a cookie.
     *
     * @param string $key Cookie key.
     * @param mixed $value Cookie value.
     * @param string|DatePeriod $duration Duration or period.
     * @param string|null $path [optional] Cookie path.
     * @param string|null $domain [optional] Cookie domain.
     */
    public static function setCookie($key, $value, $duration, $path = null, $domain = null) {
        // Determine the expiration date time
        $expirationDateTime = DateTime::parse($duration);

        // Parse the cookie path and domain
        if($path === null)
            $path = static::$path;
        if($domain === null)
            $domain = static::$domain;

        // Set the cookie
        setcookie(static::getFullCookieKey($key), $value, $expirationDateTime->getTimestamp(), $path, $domain);
    }

    /**
     * Check whether a cookie is set.
     *
     * @param string $key The cookie key.
     *
     * @return bool True if the cookie is set, false otherwise.
     */
    public static function hasCookie($key) {
        return isset($_COOKIE[static::getFullCookieKey($key)]);
    }

    /**
     * Delete a cookie.
     *
     * @param string $key Cookie key.
     * @param string|null $path [optional] Cookie path.
     * @param string|null $domain [optional] Cookie domain.
     */
    public static function deleteCookie($key, $path = null, $domain = null) {
        // Parse the cookie path and domain
        if($path === null)
            $path = static::$path;
        if($domain === null)
            $domain = static::$domain;

        // Remove the cookie
        setcookie(static::getFullCookieKey($key), null, -1, $path, $domain);
    }

    /**
     * Get the full cookie key, with prefix.
     *
     * @param string $key The base key.
     *
     * @return string Full cookie key.
     */
    private static function getFullCookieKey($key) {
        return static::$prefix . trim($key);
    }
}
