<?php

/**
 * DateIntervalSpecUtils.php
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Carbon CMS 2015. All rights reserved.
 */

namespace carbon\core\datetime\interval\spec;

use carbon\core\datetime\DateTime;
use carbon\core\datetime\interval\DateInterval;
use DateInterval as PHPDateInterval;
use Exception;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Class DateIntervalSpecUtils
 *
 * @package carbon\core\datetime\interval\spec
 */
class DateIntervalSpecUtils {

    // TODO: Does this utilities class contain all required methods?
    // TODO: Create an array utilities class?

    /**
     * Defines the regex to use to validate a date interval specification string according to ISO-8601.
     *
     * @const string The date interval validation regex.
     */
    const DATE_INTERVAL_SPEC_REGEX = '/^\s*P((((([0-9]+Y([0-9]+M)?([0-9]+[DW])?)|([0-9]+M([0-9]+[DW])?)|([0-9]+[DW]))(T(([0-9]+H([0-9]+M)?([0-9]+S)?)|([0-9]+M([0-9]+S)?)|([0-9]+S)))?|(T(([0-9]+H([0-9]+M)?([0-9]+S)?)|([0-9]+M([0-9]+S)?)|([0-9]+S)))))|(([0-9]{4}-(0[0-9]|1[0-2])-([0-2][0-9]|3[0-1]))T(([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9])))\s*$/';

    /**
     * Try to parse a date interval specification string.
     *
     * @param string|DateInterval|PHPDateInterval|null $dateIntervalSpec [optional] A date interval specification, a
     *     relative date and time string, a DateInterval or PHPDateInterval instance or null to create a zero
     *     configuration.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The parsed date interval specification or the default value on failure.
     */
    public static function parse($dateIntervalSpec = null, $default = null) {
        // Return a zero specification if the specification is set to null
        if(empty($dateIntervalSpec))
            return DateIntervalSpec::createZero();

        // Parse strings
        if(is_string($dateIntervalSpec)) {
            // Check whether the string is already a valid specification
            if(static::isValid($dateIntervalSpec))
                return $dateIntervalSpec;

            // Check whether the string has relative keywords
            if(DateTime::hasRelativeKeywords($dateIntervalSpec)) {
                try {
                    // Try to parse the string as relative date and time
                    $dateInterval = DateInterval::createFromDateString($dateIntervalSpec);

                    // Get and return the date interval specification
                    return $dateInterval->toSpecString();

                } catch(Exception $ex) { }
            }
        }

        // Parse DateInterval objects
        if($dateIntervalSpec instanceof DateInterval)
            return $dateIntervalSpec->toSpecString();

        // Parse PHPDateInterval objects
        if($dateIntervalSpec instanceof PHPDateInterval)
            if(($spec = DateIntervalSpec::create($dateIntervalSpec->y, $dateIntervalSpec->m, null, $dateIntervalSpec->d, $dateIntervalSpec->h, $dateIntervalSpec->i, $dateIntervalSpec->s)) !== null)
                return $spec;

        // Couldn't parse the string, return the default value
        return $default;
    }

    /**
     * Validate whether a date interval specification string is valid or not based on ISO-8601.
     *
     * @param string $dateIntervalSpec The date interval specification string to validate.
     *
     * @return bool True if the date interval specification is valid, false otherwise.
     */
    public static function isValid($dateIntervalSpec) {
        // Make sure the param is a string
        if(!is_string($dateIntervalSpec))
            return false;

        // Check whether the specification is valid using a regular expression, return the result
        return preg_match(static::DATE_INTERVAL_SPEC_REGEX, $dateIntervalSpec) > 0;
    }
}
