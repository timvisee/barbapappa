<?php

/**
 * URIHelper.php
 * The URI helper class, which is used to process URI's.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Tim Visee 2015. All rights reserved.
 */

namespace carbon\core\io\uri;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * URI utils class.
 *
 * @package carbon\core\io
 * @author Tim Visee
 */
class URIHelper {

    // TODO: Use scheme objects, instead of strings?
    // TODO: Create an object to get the current page request URI data from?

    /**
     * Parse an URI.
     * Invalid characters are replaced by an underscore sign.
     *
     * The following segments of an URI will be parsed:
     * - scheme (the used protocol, e.g. http)
     * - host
     * - port
     * - user
     * - pass
     * - path
     * - query (after the question mark ?)
     * - fragment (after the hashmark #)
     *
     * @param string|URI $uri The URI to parse as a string or as URI instance.
     * @param bool $validate [optional] True to validate the URI, false to skip validation. The default value will be
     * returned if the URI isn't valid.
     * @param mixed $default [optional] The default value returned on failure.
     *
     * @return URI|mixed The parsed URI as URI instance, or the default value on failure.
     */
    // TODO: Parse caching?
    public static function parseURI($uri, $validate = false, $default = null) {
        // Return the URI if it's an URI instance already
        if($uri instanceof URI)
            return $uri;

        // Parse the URI, and make sure it's valid
        $parsed = parse_url($uri);
        if($parsed === false)
            return $default;

        // Parse the user info
        $userInfo = null;
        if(isset($parsed['user']) || isset($parsed['pass']))
            $userInfo = new URIUserInfo(@$parsed['user'], @$parsed['pass']);

        // Parse the URI
        $uri = new URI(
            @$parsed['scheme'],
            @$parsed['host'],
            @$parsed['port'],
            $userInfo,
            @$parsed['path'],
            @$parsed['query'],
            @$parsed['fragment']
        );

        // Make sure the URI is valid
        if($validate)
            if(!$uri->isValid())
                return $default;

        // Return the parsed URI
        return $uri;
    }

    /**
     * Get an URI as a string.
     *
     * @param URI|string $uri The URI to get as an URI instance or as a string.
     * @param bool $validate [optional] True to validate the URI.
     * @param mixed $default [optional] The default value returned if the URI couldn't be parsed.
     *
     * @return string|mixed The URI as a string. The default value will be returned if the URI couldn't be parsed.
     * If $validate is set to true, the URI must be valid or the default value will be returned.
     */
    public static function parseURIString($uri, $validate = false, $default = null) {
        // Parse the URI, and make sure it parsed successfully
        if(($uri = self::parseURI($uri, $validate, null)) === null)
            return $default;

        // Get the URI string, make sure it's valid
        $uriString = $uri->getURIString();
        if($uriString === null)
            return $default;

        // Return the URI string
        return $uriString;
    }

    /**
     * Get the scheme of an URI if set.
     *
     * @param URI|string $uri The URI as URI instance or as string to get the scheme from.
     * @param mixed $default [optional] The default value returned on failure.
     *
     * @return string|mixed The scheme of the URI if set, the default value otherwise. The default value will also be
     * returned on failure.
     */
    public static function getScheme($uri, $default = null) {
        // Parse the URI, and make sure it's parsed successfully
        if(($uri = self::parseURI($uri, false, null)) === null)
            return $default;

        // Return the scheme, or the default value if
        if($uri->hasScheme())
            return $uri->getScheme();
        return $default;
    }

    /**
     * Get the host of an URI if set.
     *
     * @param URI|string $uri The URI as URI instance or as string to get the host from.
     * @param mixed $default [optional] The default value returned on failure.
     *
     * @return string|mixed The host of the URI if set, the default value otherwise. The default value will also be
     * returned on failure.
     */
    public static function getHost($uri, $default = null) {
        // Parse the URI, and make sure it's parsed successfully
        if(($uri = self::parseURI($uri, false, null)) === null)
            return $default;

        // Return the host, or the default value if
        if($uri->hasHost())
            return $uri->getHost();
        return $default;
    }

    /**
     * Get the port of an URI if set.
     *
     * @param URI|string $uri The URI as URI instance or as string to get the port from.
     * @param mixed $default [optional] The default value returned on failure.
     *
     * @return int|mixed The port of the URI if set, the default value otherwise. The default value will also be
     * returned on failure.
     */
    public static function getPort($uri, $default = null) {
        // Parse the URI, and make sure it's parsed successfully
        if(($uri = self::parseURI($uri, false, null)) === null)
            return $default;

        // Return the port, or the default value if
        if($uri->hasPort())
            return $uri->getPort();
        return $default;
    }

    /**
     * Get the user info of an URI if set.
     *
     * @param URI|string $uri The URI as URI instance or as string to get the user info from.
     * @param mixed $default [optional] The default value returned on failure.
     *
     * @return URIUserInfo|mixed The user info of the URI if set, the default value otherwise. The default value will also be
     * returned on failure.
     */
    public static function getUserInfo($uri, $default = null) {
        // Parse the URI, and make sure it's parsed successfully
        if(($uri = self::parseURI($uri, false, null)) === null)
            return $default;

        // Return the user info, or the default value if
        if($uri->hasUserInfo())
            return $uri->getUserInfo();
        return $default;
    }

    /**
     * Get the path of an URI if set.
     *
     * @param URI|string $uri The URI as URI instance or as string to get the path from.
     * @param mixed $default [optional] The default value returned on failure.
     *
     * @return string|mixed The path of the URI if set, the default value otherwise. The default value will also be
     * returned on failure.
     */
    public static function getPath($uri, $default = null) {
        // Parse the URI, and make sure it's parsed successfully
        if(($uri = self::parseURI($uri, false, null)) === null)
            return $default;

        // Return the path, or the default value if
        if($uri->hasPath())
            return $uri->getPath();
        return $default;
    }

    /**
     * Get the query of an URI if set.
     *
     * @param URI|string $uri The URI as URI instance or as string to get the query from.
     * @param mixed $default [optional] The default value returned on failure.
     *
     * @return string|mixed The query of the URI if set, the default value otherwise. The default value will also be
     * returned on failure.
     */
    public static function getQuery($uri, $default = null) {
        // Parse the URI, and make sure it's parsed successfully
        if(($uri = self::parseURI($uri, false, null)) === null)
            return $default;

        // Return the query, or the default value if
        if($uri->hasQuery())
            return $uri->getQuery();
        return $default;
    }

    /**
     * Get the fragment of an URI if set.
     *
     * @param URI|string $uri The URI as URI instance or as string to get the fragment from.
     * @param mixed $default [optional] The default value returned on failure.
     *
     * @return string|mixed The fragment of the URI if set, the default value otherwise. The default value will also be
     * returned on failure.
     */
    public static function getFragment($uri, $default = null) {
        // Parse the URI, and make sure it's parsed successfully
        if(($uri = self::parseURI($uri, false, null)) === null)
            return $default;

        // Return the fragment, or the default value if
        if($uri->hasFragment())
            return $uri->getFragment();
        return $default;
    }

    /**
     * Get an URI as an URI string.
     *
     * @param URI|string $uri The URI as URI instance or as string to get as URI string.
     * @param mixed $default [optional] The default value returned on failure.
     *
     * @return string|mixed The URI as a string or the default value on failure.
     */
    public static function getURIString($uri, $default = null) {
        // Parse the URI, and make sure it's parsed successfully
        if(($uri = self::parseURI($uri, false, null)) === null)
            return $default;

        // Reconstruct the authority
        $authority = null;
        if($uri->hasScheme()) {
            $authority = '';

            // Append the user info if set
            if($uri->hasUserInfo())
                $authority .= $uri->getUserInfo() . '@';

            // Append the host
            $authority .= $uri->getHost();

            // Append the port if set
            if($uri->hasPort())
                $authority .= ':' . $uri->hasPort();
        }

        // Reconstruct the URI
        $uriString = '';

        // Append the scheme if set
        if($uri->hasScheme())
            $uriString .= $uri->getScheme() . ':';

        // Append the authority if available
        if($authority !== null)
            $uriString .= '//' . $authority;

        // Append the path
        $uriString .= $uri->getPath();

        // Append the query
        if($uri->hasQuery())
            $uriString .= '?' . $uri->getQuery();

        // Append the fragment
        if($uri->hasFragment())
            $uriString .= '#' . $uri->getFragment();

        // Return the reconstructed URI
        return $uriString;
    }

    /**
     * Validate an URI
     *
     * @param URI|string $uri The URI to validate as an URI instance or string.
     *
     * @return bool True if the URI is valid, false otherwise.
     */
    public static function isValid($uri) {
        // Get the URI as a string and make sure it's valid
        if(($uri = self::parseURIString($uri, false, null)) === null)
            return false;

        // TODO: Use a different (better performing) validation method
        // Check whether the URI is valid, return the result
        return filter_var($uri, FILTER_VALIDATE_URL) !== false;
    }
}