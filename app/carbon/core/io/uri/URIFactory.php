<?php

/**
 * URIFactory.php
 * The URI factory class, which is used to fabricate URI's.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Tim Visee 2015. All rights reserved.
 */

namespace carbon\core\io\uri;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class URIFactory {

    /** @var string|null Defines the URI scheme as a string, or null if unavailable. */
    private $scheme = null;
    /** @var string|null Defines the URI host as a string, or null if unavailable. */
    private $host;
    /** @var int|null Defines the URI port as an int, or null if unavailable. */
    private $port = null;
    /** @var string|null Defines the URI user as a string, or null if unavailable. */
    private $user = null;
    /** @var string|null Defines the URI password as a string, or null if unavailable. */
    private $pass = null;
    /** @var string|null Defines the URI path as a string, or null if unavailable. */
    private $path = null;
    /** @var string|null Defines the URI query as a string, or null if unavailable. */
    private $query = null;
    /** @var string|null Defines the URI fragment as a string, or null if unavailable. */
    private $fragment = null;

    // TODO: Userinfo such as user:pass should be put together?

    /**
     * Constructor.
     */
    public function __construct($uri) {
        // TODO: Complete this method!
    }

    /**
     * Get the URI scheme if set.
     *
     * @return string|null The scheme of the URI if available, null otherwise.
     */
    public function getScheme() {
        return $this->scheme;
    }

    /**
     * Set the URI scheme.
     *
     * @param string|URI|null $scheme The URI scheme as a string. Or an URI instance to copy the URI scheme.
     * Null to remove the scheme.
     *
     * @return bool True on success, false on failure.
     */
    public function setScheme($scheme) {
        // Get the scheme form an URI instance
        if($scheme instanceof URI)
            $scheme = $scheme->getScheme();

        // Make sure the scheme is a string or null
        if(!is_string($scheme) && $scheme !== null)
            return false;

        // Set the scheme, return the result
        $this->scheme = $scheme;
        return true;
    }

    /**
     * Check whether the URI has a scheme set.
     *
     * @return bool True if a scheme is set, false otherwise.
     */
    public function hasScheme() {
        return $this->scheme !== null;
    }

    /**
     * Get the URI host.
     *
     * @return string The host of the URI.
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Set the URI host.
     *
     * @param string|URI $host The URI host as a string. Or an URI instance to copy the URI host.
     *
     * @return bool True on success, false on failure.
     */
    public function setHost($host) {
        // Get the host from an URI instance
        if($host instanceof URI)
            $host = $host->getHost();

        // Make sure the host is a string
        if(!is_string($host))
            return false;

        // Set the host, return the result
        $this->host = $host;
        return true;
    }

    /**
     * Get the URI port if set.
     *
     * @return int|null
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Set the URI port.
     *
     * @param int|string|null $port The URI port number as an integer or string. Or an URI instance to copy the URI port.
     *
     * @return bool True on success, false on failure.
     */
    public function setPort($port) {
        // Get the port form an URI instance
        if($port instanceof URI)
            $port = $port->getPort();

        // Try to convert the port into an integer if it's a string
        if(is_string($port)) {
            // Cast the port number,
            $port = intval($port);

            // Make sure the port number is valid
            if(!URIHelper::isValidPort($port))
                return false;
        }

        // Make sure the port number is an integer or null
        if(!is_int($port) && $port !== null)
            return false;

        // Set the port number, return the result
        $this->port = $port;
        return true;
    }

    /**
     * Check whether the URI has a port set.
     *
     * @return bool True if a port is set, false otherwise.
     */
    public function hasPort() {
        return $this->port !== null;
    }

    /**
     * Get the URI user if set.
     *
     * @return string|null The user of the URI if available, null otherwise.
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set the URI user.
     *
     * @param string|URI|null $user The URI user as a string. Or an URI instance to copy the URI user.
     * Null to remove the user.
     *
     * @return bool True on success, false on failure.
     */
    public function setUser($user) {
        // Get the user from an URI instance
        if($user instanceof URI)
            $user = $user->getUserInfo();

        // Make sure the user is a string or null
        if(!is_string($user) && $user !== null)
            return false;

        // Set the user, return the result
        $this->user = $user;
        return true;
    }

    /**
     * Check whether the URI has a user set.
     *
     * @return bool True if a user is set, false otherwise.
     */
    public function hasUser() {
        return $this->user !== null;
    }

    /**
     * Get the URI password if set.
     *
     * @return string|null The password of the URI if available, null otherwise.
     */
    public function getPassword() {
        return $this->pass;
    }

    /**
     * Set the URI password.
     *
     * @param string|URI|null $pass The URI password as a string. Or an URI instance to copy the URI password.
     * Null to remove the password.
     *
     * @return bool True on success, false on failure.
     */
    public function setPassword($pass) {
        // Get the password from an URI instance
        if($pass instanceof URI)
            $pass = $pass->getPassword();

        // Make sure the password is a string or null
        if(!is_string($pass) && $pass !== null)
            return false;

        // Set the password, return the result
        $this->pass = $pass;
        return true;
    }

    /**
     * Check whether the URI has a pass set.
     *
     * @return bool True if a pass is set, false otherwise.
     */
    public function hasPassword() {
        return $this->pass !== null;
    }

    /**
     * Get the URI path if set.
     *
     * @return string|null The path of the URI if available, null otherwise.
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set the URI path.
     *
     * @param string|URI|null $path The URI path as a string. Or an URI instance to copy the URI path.
     * Null to remove the path.
     *
     * @return bool True on success, false on failure.
     */
    public function setPath($path) {
        // Get the path from an URI instance
        if($path instanceof URI)
            $path = $path->getPath();

        // Make sure the path is a string or null
        if(!is_string($path) && $path !== null)
            return false;

        // Set the path, return the result
        $this->path = $path;
        return true;
    }

    /**
     * Check whether the URI has a path set.
     *
     * @return bool True if a path is set, false otherwise.
     */
    public function hasPath() {
        return $this->path !== null;
    }

    /**
     * Get the URI query if set.
     *
     * @return string|null The query of the URI if available, null otherwise.
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * Set the URI query.
     *
     * @param string|URI|null $query The URI query as a string. Or an URI instance to copy the URI query.
     * Null to remove the path.
     *
     * @return bool True on success, false on failure.
     */
    // TODO: More query options!
    public function setQuery($query) {
        // Get the query from an URI instance
        if($query instanceof URI)
            $query = $query->getQuery(false);

        // Make sure the query is a string or null
        if(!is_string($query) && $query !== null)
            return false;

        // Set the query, return the result
        $this->query = $query;
        return true;
    }

    /**
     * Check whether the URI has a query set.
     *
     * @return bool True if a query is set, false otherwise.
     */
    public function hasQuery() {
        return $this->query !== null;
    }

    /**
     * Get the URI fragment if set.
     *
     * @return string|null The fragment of the URI if available, null otherwise.
     */
    public function getFragment() {
        return $this->fragment;
    }

    /**
     * Set the URI fragment.
     *
     * @param string|URI|null $fragment The URI fragment as a string. Or an URI instance to copy the URI fragment.
     * Null to remove the path.
     *
     * @return bool True on success, false on failure.
     */
    public function setFragment($fragment) {
        // Get the query from the URI instance
        if($fragment instanceof URI)
            $fragment = $fragment->getFragment();

        // Make sure the fragment is a string or null
        if(!is_string($fragment) && $fragment !== null)
            return false;

        // Set the fragment, return the result
        $this->fragment = $fragment;
        return true;
    }

    /**
     * Check whether the URI has a fragment set.
     *
     * @return bool True if a fragment is set, false otherwise.
     */
    public function hasFragment() {
        return $this->fragment !== null;
    }

    /**
     * Get the URI.
     *
     * @return string
     */
    // TODO: Update this method!
    public function getUri() {
        // Define the URI
        $uri = '';

        // Append the scheme
        if($this->hasScheme())
            $uri .= $this->getScheme() . ':';

        // Append the username and password
        if($this->hasUser()) {
            // Append the user
            $uri .= $this->getUser();

            // Append the password
            if($this->hasPassword())
                $uri .= ':' . $this->getPassword();

            $uri .= '@';
        }

        // Append the host
        $uri .= $this->getHost();

        // Append the port
        if($this->hasPort())
            $uri .= ':' . $this->getPort();

        // Append the path
        if($this->hasPath())
            $uri .= $this->getPath();

        // Append the query
        if($this->hasQuery())
            $uri .= '?' . $this->getQuery();

        // Append the fragment
        if($this->hasFragment())
            $uri .= '#' . $this->getFragment();

        // Return the URI
        return $uri;
    }
}
