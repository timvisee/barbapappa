<?php

/**
 * IpUtils.php
 * IpUtils class for Carbon CMS.
 * Ip utilities class.
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2013, All rights reserved.
 */

namespace carbon\core\util;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * IpUtils class
 * @package core\util
 * @author Tim Visee
 */
class IpUtils {

    /**
     * Get the IP address of the client. If the IP address could not be retrieved, 0.0.0.0 will be returned.
     * @return string Retrieved IP address
     */
    public static function getClientIp() {
        // Try to retrieve the IP address of the client from some $_SERVER variables
        foreach(array("REMOTE_ADDR", "HTTP_X_FORWARDED_FOR", "HTTP_CLIENT_IP") as $key)
            if(isset($_SERVER[$key]))
                return $_SERVER[$key];

        // Unknown IP address, a proxy might have been used, returns 0.0.0.0 as default IP address
        return '0.0.0.0';
    }

    /*public static function getClientIp($proxy = false) {
        if ($proxy === true) {
            foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED') as $key) {
                if (array_key_exists($key, $_SERVER) === true) {
                    foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                            return $ip;
                        }
                    }
                }
            }
        }

        if(isset($_SERVER["REMOTE_ADDR"]))
            return $_SERVER["REMOTE_ADDR"];

        else if(isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        else if(isset($_SERVER["HTTP_CLIENT_IP"]))
            return $_SERVER["HTTP_CLIENT_IP"];

        // Unknown IP address, a proxy might have been used, returns 0.0.0.0 as default IP address
        return '0.0.0.0';
    }*/

    /**
     * Checks whether the IP address of the client is unknown or not
     * @return bool True if the IP address of the client is unknown, because a proxy might be used
     */
    public static function isClientIpUnknown() {
        return (IpUtils::getClientIp() == '0.0.0.0');
    }
}