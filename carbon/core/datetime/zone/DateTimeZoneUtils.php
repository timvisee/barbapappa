<?php

/**
 * DateTimeZoneUtils.php
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Carbon CMS 2015. All rights reserved.
 */

namespace carbon\core\datetime\zone;

use carbon\core\datetime\DateTime;
use DateTime as PHPDateTime;
use DateTimeZone as PHPDateTimeZone;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Class DateTimeZoneUtils
 *
 * @package carbon\core\datetime\zone
 */
class DateTimeZoneUtils {

    // TODO: TimeZone or Timezone? Use only one of the two, not both!
    // TODO: Does this utilities class contain all required methods?
    // TODO: Create an array utilities class!

    /**
     * Parse a timezone. A new instance will be created if required.
     *
     * @param DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $timezone The timezone to parse as
     *     DateTimeZone or PHPDateTimeZone instance or as a string. A DateTime or PHPDateTime instance to use it's
     *     timezone. Null to parse as the default timezone.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return DateTimeZone|mixed The parsed timezone as DateTimeZone instance, or the default value on failure.
     */
    public static function parse($timezone = null, $default = null) {
        return ($result = DateTimeZone::parse($timezone)) !== null ? $result : $default;
    }

    // TODO: equals(...); method (also to the base class)!

    /**
     * Get the default timezone for all date and time functions.
     *
     * @return DateTimeZone The default timezone.
     */
    public static function getDefaultTimezone() {
        return static::parse(date_default_timezone_get());
    }

    /**
     * Set the default timezone for all date and time functions.
     *
     * @param DateTimeZone|PHPDateTimeZone|string|null $timezone The timezone as DateTimeZone instance, the timezone ID
     *     as a string or null to use the default timezone configured in PHPs configuration file.
     *
     * @return bool True if succeed, false on failure.
     */
    public static function setDefaultTimezone($timezone) {
        // Check whether the default timezone should be restored
        if($timezone === null)
            // TODO: Read the real-default timezone from other sources if possible
            $timezone = ini_get('date.timezone');

        // Parse the timezone and make sure it's valid
        if(($timezone = static::parse($timezone, null)) === null)
            return false;

        // Set the default timezone
        date_default_timezone_set($timezone->getName());
    }

    /**
     * Check whether a timezone ID is valid. A timezone ID is specified by a string, see PHPs list of supported
     * timezones.
     *
     * @param string $timezone The timezone ID to validate.
     *
     * @return bool True if the timezone ID is valid, false otherwise.
     */
    public static function isValidTimezoneId($timezone) {
        // Make sure the timezone parameter is a string
        if(!is_string($timezone))
            return false;

        // Create a list of valid timezones
        $valid = array();

        // Get the list of supported PHP timezones
        $timezoneList = timezone_abbreviations_list();

        // Create a list of valid timezones
        foreach($timezoneList as $zone)
            foreach($zone as $item)
                $valid[$item['timezone_id']] = true;

        // Remove the invalid keys
        unset($valid['']);

        // Check weather the timezone ID exists as a key in the array, return the result
        return !!$valid[$timezone];
    }

    /**
     * Check whether the timezones a and b are equal to each other.
     *
     * @param DateTimeZone|PHPDateTimeZone|string|null $a [optional] The timezone as DateTimeZone or PHPDateTimeZone
     *     instance, the timezone ID as a string or null to use the default timezone.
     * @param DateTimeZone|PHPDateTimeZone|string|null $b [optional] The timezone as DateTimeZone or PHPDateTimeZone
     *     instance, the timezone ID as a string or null to use the default timezone.
     * @param null|mixed $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if the timezones are equal, false if not. The default value will be returned on failure.
     */
    public static function equals($a = null, $b = null, $default = null) {
        // Parse the timezone of a, return the default value on failure
        if(($a = static::parse($a, null)) === null)
            return $default;

        // Check whether the timezones are equal, return the result
        return $a->equals($b);
    }

    /**
     * Check whether the timezone a and b are local. This compares the offset of both timezones at a specific point in
     * time.
     *
     * @param DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $a [optional] A DateTimeZone or
     *     PHPDateTimeZone instance, the timezone ID as a string, a DateTime or PHPDateTime instance to use it's
     *     timezone or null to use the default timezone.
     * @param DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $b [optional] A DateTimeZone or
     *     PHPDateTimeZone instance, the timezone ID as a string, a DateTime or PHPDateTime instance to use it's
     *     timezone or null to use the default timezone.
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] The specific point in date and time to compare the
     *     offsets at. A DateTime or PHPDateTime instance, the date and time as a string or null to use the now()
     *     value.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool True if the timezone is local, false if not. The default value will be returned on failure.
     */
    public function isLocal($a = null, $b = null, $dateTime = null, $default = null) {
        // Parse the timezone of a, return false on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Check whether the timezone of a and b are local, return the result or return the default value on failure
        return ($result = $a->isLocal($b, $dateTime)) === null ? $default : $result;
    }





















    /**
     * Creates a DateTimeZone from a string or a PHPDateTimeZone.
     *
     * @param PHPDateTimeZone|string|null $timeZone
     *
     * @return PHPDateTimeZone
     *
     * @throws \InvalidArgumentException
     */
    // TODO: Should we keep this method?
    public static function safeCreateDateTimeZone($timeZone) {
        // Return the default time zone if nothing was supplied
        if($timeZone === null)
            // Don't return null to avoid the PHP bug #52063 (< v5.3.6)
            return static::getDefaultTimezone();

        // Immediately return the object if it's already a time zone instance
        if($timeZone instanceof PHPDateTimeZone)
            return $timeZone;

        // Try to parse the timezone, throw an exception on failure
        if(($timeZone = static::parse($timeZone, null)) === null)
            // Failed to create a time zone object, thrown an exception
            throw new \InvalidArgumentException('Unknown or bad timezone (' . $timeZone . ')');
        return $timeZone;
    }
}
