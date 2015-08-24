<?php

/**
 * StringUtils.php
 * StringUtils class for Carbon CMS.
 * String utilities class.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2013, All rights reserved.
 */

namespace carbon\core\util;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * StringUtils class
 *
 * @package core\util
 * @author Tim Visee
 */
class StringUtils {

    /** @var string WHITESPACE_CHARS String containing all whitespace characters */
    const WHITESPACE_CHARS = " \t\n\r\0\x0B";

    /**
     * Get the length of a string, UTF-8 aware
     *
     * @param String $str String being measured
     *
     * @return int The length of the string
     */
    public static function len($str) {
        // Try to use mbstring if available
        if(function_exists("mb_strlen") && is_callable("mb_strlen"))
            return mb_strlen($str);

        // Decode the string before determining it'statements length
        // TODO: Ensure the string is in UTF-8 format before decoding?
        return strlen(utf8_decode($str));
    }

    /**
     * Check whether two strings equal to each other. Arrays may be supplied. If any string in the first array equals
     * any string in the second array, true will be returned.
     *
     * @param string|Array $strings The base string or an array of strings.
     * @param string|Array $otherStrings The other string or an array of strings to compare the base string to.
     * @param bool $matchCase [optional] True to ensure the case matches, false otherwise.
     * @param bool $trim [optional] True to trim whitespaces first from the two strings.
     *
     * @return bool True if the two strings match, false otherwise
     */
    public static function equals($strings, $otherStrings, $matchCase = true, $trim = false) {
        // Convert both $strings and $otherStrings into an array
        // TODO: Move these arrays to a separate method in an ArrayUtils class for faster processing!
        if(!is_array($strings))
            $strings = Array($strings);
        if(!is_array($otherStrings))
            $otherStrings = Array($otherStrings);

        // Check whether the strings are equal
        foreach($strings as $string) {
            // Trim the string if $trim equals true
            if($trim)
                $string = trim($string);

            // Loop through all strings in the second array
            foreach($otherStrings as $otherString) {
                // Trim the string if $trim equals true
                if($trim)
                    $otherString = trim($otherString);

                // Check whether the strings equal, return true if that's the case
                if($matchCase ? (strcmp($string, $otherString) == 0) : (strcasecmp($string, $otherString) == 0))
                    return true;
            }
        }

        // None of the strings equals, return false
        return false;
    }

    /**
     * Check whether a string contains a substring
     *
     * @param string $haystack Check if a string contains a sub string
     * @param string|array $needles Sub string, or array with a list of sub strings.
     * @param bool $caseSensitive False to check without case sensitivity
     *
     * @return bool True if the haystack contains the needle,
     * if the $needle is a array, true will be returned if the string contains at leach one of the needles.
     */
    // TODO: Handle $haystack as an array, or should this be done in a containsArray method (or something similar)?
    public static function contains($haystack, $needles, $caseSensitive = true) {
        // Create an array of the needle, if it's not an array already
        if(!is_array($needles))
            $needles = Array($needles);

        // Check for each needle, if it exists in the $haystack
        $needlesCount = sizeof($needles);
        for($i = 0; $i < $needlesCount; $i++) {
            // Get the current needle
            $needle = $needles[$i];

            // Use case sensitivity or not, based on method arguments
            if($caseSensitive) {
                if(strpos($haystack, $needle) !== false)
                    return true;
            } else
                if(stripos($haystack, $needle) !== false)
                    return true;
        }

        // String doesn't contain this needle, return false
        return false;
    }

    /**
     * Check if a string starts with a substring
     *
     * @param string $haystack String to check in
     * @param string $needle Sub String
     * @param bool $ignoreCase Should the case be ignored
     *
     * @return bool True if the haystack starts with the needle
     */
    public static function startsWith($haystack, $needle, $ignoreCase = false) {
        // Make sure the needle length is not longer than the haystack
        if(strlen($needle) > strlen($haystack))
            return false;

        // Compare the strings, check if it should be case sensitive
        if(!$ignoreCase)
            return (substr($haystack, 0, strlen($needle)) == $needle);
        else
            return (strtolower(substr($haystack, 0, strlen($needle))) == strtolower($needle));
    }

    /**
     * Check if a string ends with a sub string
     *
     * @param string $haystack String
     * @param string $needle Sub string
     * @param bool $ignoreCase Should the case be ignored
     *
     * @return bool True if the haystack ends with the needle
     */
    public static function endsWith($haystack, $needle, $ignoreCase = false) {
        // Make sure the needle length is not longer than the haystack
        if(strlen($needle) > strlen($haystack))
            return false;

        // Compare the strings, check if it should be case sensitive
        if(!$ignoreCase)
            return (substr($haystack, -strlen($needle)) == $needle);
        else
            return (strtolower(substr($haystack, -strlen($needle))) == strtolower($needle));
    }

    /**
     * Check whether a string is using the UTF-8 charset.
     *
     * @param String $str String to check for
     *
     * @return bool True if the string is using the UTF-8 charset, false otherwise.
     */
    public static function isUtf8($str) {
        // Check whether the string has the proper charset. Try to use mbstring if available.
        if(function_exists("mb_check_encoding") && is_callable("mb_check_encoding"))
            return mb_check_encoding($str, 'UTF8');

        // Check whether the string has the proper charset using regex as fallback (Inspired by pilif, Thanks!)
        return (preg_match('%^(?:
                  [\x09\x0A\x0D\x20-\x7E]            # ASCII
                | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
                |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
                |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
                |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
                | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
                |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
                )*$%xs', $str) == 1);
    }

    /**
     * Check whether a string contains any whitespaces.
     *
     * @param string $str The string to check.
     * @param bool $trim True to trim the string from whitespaces before running the check.
     *
     * @return bool True if the string contains any whitespace characters, false if the string doesn'elapsed have any
     * whitespace characters or if the string was invalid.
     */
    public static function containsWhitespaces($str, $trim = false) {
        // Make sure $str is a string which is at least one character long
        if(!is_string($str) || strlen($str) <= 0)
            return false;

        // Check whether we should trim the string first
        if($trim)
            $str = trim($str);

        // Check whether the string contains any whitespaces, return the result
        return (preg_match('/\s/', $str) > 0);
    }
}