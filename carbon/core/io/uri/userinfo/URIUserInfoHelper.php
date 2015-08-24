<?php

/**
 * URIUserInfoHelper.php
 * The URI user info utilities class.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Tim Visee 2015. All rights reserved.
 */

namespace carbon\core\io\uri\userinfo;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * URI user info utilities class.
 *
 * @package carbon\core\io\uri\userinfo
 * @author Tim Visee
 */
class URIUserInfoHelper {

    /**
     * Character classes defined in RFC-3986
     */
    const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';
    const CHAR_GEN_DELIMS = ':\/\?#\[\]@';
    const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';
    const CHAR_RESERVED = ':\/\?#\[\]@!\$&\'\(\)\*\+,;=';
    /**
     * Not in the spec - those characters have special meaning in urlencoded query parameters
     */
    const CHAR_QUERY_DELIMS = '!\$\'\(\)\*\,';
    /**
     * Host part types represented as binary masks
     * The binary mask consists of 5 bits in the following order:
     * <RegName> | <DNS> | <IPvFuture> | <IPv6> | <IPv4>
     * Place 1 or 0 in the different positions for enable or disable the part.
     * Finally use a hexadecimal representation.
     */
    const HOST_IPV4 = 0x01; //00001
    const HOST_IPV6 = 0x02; //00010
    const HOST_IPVFUTURE = 0x04; //00100
    const HOST_IPVANY = 0x07; //00111
    const HOST_DNS = 0x08; //01000
    const HOST_DNS_OR_IPV4 = 0x09; //01001
    const HOST_DNS_OR_IPV6 = 0x0A; //01010
    const HOST_DNS_OR_IPV4_OR_IPV6 = 0x0B; //01011
    const HOST_DNS_OR_IPVANY = 0x0F; //01111
    const HOST_REGNAME = 0x10; //10000
    const HOST_DNS_OR_IPV4_OR_IPV6_OR_REGNAME = 0x1B; //11011
    const HOST_ALL = 0x1F; //11111
















    /**
     * Get some user info as a string.
     *
     * @param URIUserInfo|string $userInfo The user info as user info instance or as a string.
     * @param bool $validate [optional] True to validate the user info, false to skip validation.
     * @param mixed|null $default [optional] The default value returned on error.
     *
     * @return string|mixed|null The user info as a string. The default value will be returned if the URI is invalid
     * while validation is enabled, or on error.
     */
    public static function asString($userInfo, $validate = false, $default = null) {
        // Make sure the user info isn't null
        if($userInfo === null)
            return $default;

        // Convert user info instances into a string
        if($userInfo instanceof URIUserInfo)
            return $userInfo->getUserInfo($validate);

        // Make sure the user info is a string
        if(!is_string($userInfo))
            return $default;

        // Validate the user info
        if($validate)
            if(!self::isValid($userInfo))
                return $default;

        // Return the user info
        return $userInfo;
    }

    /**
     * Validate URI user info.
     *
     * @param URIUserInfo|string $userInfo The user info as user info instance or as a string.
     *
     * @return bool True if the user info is valid, false otherwise.
     */
    public static function isValid($userInfo) {
        // Get the user info as a string, and make sure it's valid
        if(($userInfo = self::asString($userInfo, false, null)) === null)
            return false;

        // Validate the user info using regex
        $regex = '/^(?:[' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . ':]+|%[A-Fa-f0-9]{2})*$/';
        return preg_match($regex, $userInfo) != 0;
    }
}