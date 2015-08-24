<?php

/**
 * URI.php
 * The URI class, which is used to manage URI's.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Tim Visee 2015. All rights reserved.
 */

namespace carbon\core\io\uri;

use carbon\core\cache\simplecache\SimpleCache;
use carbon\core\io\uri\userinfo\URIUserInfo;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * URI class.
 *
 * @package carbon\core\io
 * @author Tim Visee
 */
class URI {

    // TODO: Finish this class, and all closely related classes such as URIUserInfo!

    /** @var string|null The scheme of the URI or null. */
    protected $scheme;
    /** @var string|null The host of the URI or null. */
    protected $host;
    /** @var int|null The port of the URI or null. */
    protected $port;
    /** @var URIUserInfo|null The user info of the URI or null. */
    protected $userInfo;
    /** @var string|null The path of the URI or null. */
    protected $path;
    /** @var string|null The query of the URI or null. */
    protected $query;
    /** @var string|null The fragment of the URI or null. */
    protected $fragment;

    // TODO: Should we remove the cache!?
    /** @var SimpleCache Instance used for basic caching. */
    protected $cache;

    /**
     * Constructor.
     *
     * @param string|null $scheme The URI scheme or null.
     * @param string|null $host The URI host or null.
     * @param int|null $port The URI port or null.
     * @param URIUserInfo|null $userInfo The URI user info or null.
     * @param string|null $path The URI path or null.
     * @param string|null $query The URI query or null.
     * @param string|null $fragment The URI fragment or null.
     */
    public function __construct($scheme, $host, $port, $userInfo, $path, $query, $fragment) {
        // Initialize the simple cache
        $this->cache = new SimpleCache();

        // Set the URI segments
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->userInfo = $userInfo;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }

    /**
     * Parse an URI.
     *
     * @param string|URI $uri The URI to parse as a string or as URI instance.
     * @param bool $validate [optional] True to validate the URI, false to skip validation. If the URI isn't valid null
     * will be returned.
     * @return URI|null The parsed URI as URI instance, or null on failure.
     */
    public static function parse($uri, $validate = false) {
        return URIHelper::parseURI($uri, $validate, null);
    }

    /**
     * Flush the cache.
     *
     * @return bool True on success, false on failure.
     */
    public function flushCache() {
        return $this->cache->flush() >= 0;
    }

    /**
     * Get the scheme of the URI if set.
     *
     * @return string|null The scheme of the URI or null.
     */
    public function getScheme() {
        return $this->scheme;
    }

    /**
     * Set the scheme of the URI.
     *
     * @param string|null $scheme The scheme of the URI or null.
     */
    public function setScheme($scheme) {
        $this->scheme = $scheme;
    }

    /**
     * Check whether the URI has any scheme set.
     *
     * @return bool True if the URI has any scheme set, false if not.
     */
    public function hasScheme() {
        return ($this->scheme !== null);
    }

    /**
     * Get the host of the URI if set.
     *
     * @return string|null The host of the URI or null.
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Set the host of the URI.
     *
     * @param string|null $host The host of the URI or null.
     */
    public function setHost($host) {
        $this->host = $host;
    }

    /**
     * Check whether the URI has any host set.
     *
     * @return bool True if the URI has any host set, false if not.
     */
    public function hasHost() {
        return ($this->host !== null);
    }

    /**
     * Get the port of the URI if set.
     *
     * @return int|null The port of the URI or null.
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Set the port of the URI.
     *
     * @param int|null $port The port of the URI or null.
     */
    public function setPort($port) {
        $this->port = $port;
    }

    /**
     * Check whether the URI has any port set.
     *
     * @return bool True if the URI has any port set, false if not.
     */
    public function hasPort() {
        return ($this->port !== null);
    }

    /**
     * Get the user info of the URI if set.
     *
     * @return URIUserInfo|null The user info of the URI or null.
     */
    public function getUserInfo() {
        return $this->userInfo;
    }

    /**
     * Set the user info of the URI.
     *
     * @param URIUserInfo|null $userInfo The user info of the URI or null.
     */
    public function setUserInfo($userInfo) {
        $this->userInfo = $userInfo;
    }

    /**
     * Check whether the URI has any user info set.
     *
     * @return bool True if the URI has any user info set, false if not.
     */
    public function hasUserInfo() {
        // Make sure an user info instance is available
        if($this->userInfo === null)
            return false;

        // Make sure any user info is set, return the result
        return $this->userInfo->hasUserInfo();
    }


    /**
     * Get the path of the URI if set.
     *
     * @return string|null The path of the URI or null.
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set the path of the URI.
     *
     * @param string|null $path The path of the URI or null.
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * Check whether the URI has any scheme set.
     *
     * @return bool True if the URI has any path set, false if not.
     */
    public function hasPath() {
        return ($this->path !== null);
    }

    /**
     * Get the query of the URI if set.
     *
     * @return string|null The query of the URI or null.
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * Set the query of the URI.
     *
     * @param string|null $query The query of the URI or null.
     */
    public function setQuery($query) {
        $this->query = $query;
    }

    /**
     * Check whether the URI has any query set.
     *
     * @return bool True if the URI has any query set, false if not.
     */
    public function hasQuery() {
        return ($this->query !== null);
    }

    /**
     * Get the fragment of the URI if set.
     *
     * @return string|null The fragment of the URI or null.
     */
    public function getFragment() {
        return $this->fragment;
    }

    /**
     * Set the fragment of the URI.
     *
     * @param string|null $fragment The fragment of the URI or null.
     */
    public function setFragment($fragment) {
        $this->fragment = $fragment;
    }

    /**
     * Check whether the URI has any fragment set.
     *
     * @return bool True if the URI has any fragment set, false if not.
     */
    public function hasFragment() {
        return ($this->fragment !== null);
    }

    /**
     * Make sure the URI is valid.
     *
     * @return bool True if the URI is valid, false otherwise.
     */
    public function isValid() {
        return URIHelper::isValid($this);
    }

    /**
     * Get the URI without any formatting as a string.
     *
     * @return string|null The URI as a string. Null will be returned on failure.
     */
    public function getURIString() {
        return URIHelper::getURIString($this, null);
    }

    /**
     * Get the URI as a string.
     *
     * @return string The URI as a string. An empty string will be returned on failure.
     */
    public function toString() {
        // Get the URI as a string, make sure it's valid
        $uri = $this->getURIString();
        if($uri === null)
            return '';

        // Return the URI string
        return $uri;
    }

    /**
     * Get the URI as a string.
     *
     * @return string The URI as a string. An empty string will be returned on failure.
     */
    public function __toString() {
        return $this->toString();
    }
}
