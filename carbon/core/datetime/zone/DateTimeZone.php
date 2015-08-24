<?php

/**
 * DateTimeZone.php
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Carbon CMS 2015. All rights reserved.
 */

namespace carbon\core\datetime\zone;

use carbon\core\datetime\DateTime;
use carbon\core\datetime\DateTimeUtils;
use carbon\core\util\StringUtils;
use DateTime as PHPDateTime;
use DateTimeZone as PHPDateTimeZone;
use Exception;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Class DateTimeZone.
 *
 * @package carbon\core\datetime\zone
 */
class DateTimeZone extends PHPDateTimeZone {

    // TODO: Method to get the current/default timezone, like 'now' from DateTime. Use DateTimeZoneUtils::getDefaultTimezone().

    /**
     * Constructor.
     *
     * @param string|DateTimeZone|PHPDateTimeZone|null $timezone [optional] The timezone as a string, or as
     *     DateTimeZone or PHPDateTimeZone instance. Null to use the default timezone.
     *
     * @throws Exception Throws an exception on failure.
     */
    public function __construct($timezone) {
        // Parse null
        if($timezone === null)
            $timezone = DateTimeZoneUtils::getDefaultTimezone();

        // Parse DateTime instances
        else if($timezone instanceof DateTime)
            $timezone = $timezone->getTimezone();

        // Parse PHPDateTime instances
        else if($timezone instanceof PHPDateTime)
            $timezone = $timezone->getTimezone();

        // Parse DateTimeZone and PHPDateTimeZone instances
        if($timezone instanceof parent) {
            parent::__construct($timezone->getName());
            return $this;
        }

        // Check if this is a valid timezone ID
        if(DateTimeZoneUtils::isValidTimezoneId($timezone)) {
            parent::__construct($timezone);
            return $this;
        }

        // Invalid timezone, throw an exception
        throw new Exception('Invalid timezone (\'' . $timezone . '\' was given)');
    }

    /**
     * Parse a timezone. A new instance will be created if required.
     *
     * @param DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $timezone The timezone to parse as
     *     DateTimeZone or PHPDateTimeZone instance or as a string. A DateTime or PHPDateTime instance to use it's
     *     timezone. Null to parse as the default timezone.
     *
     * @return DateTimeZone|null The parsed timezone as DateTimeZone instance, or null on failure.
     */
    // TODO: Improve the performance of this method, possibly rename it to asDateTimeZone!
    public static function parse($timezone = null) {
        // Parse null
        if($timezone === null)
            return DateTimeZoneUtils::getDefaultTimezone();

        // Parse DateTimeZone instances
        if($timezone instanceof self)
            return $timezone;

        // Parse PHPDateTimeZone instances
        else if($timezone instanceof parent)
            return new self($timezone);

        // Get the timezone form a DateTime object
        else if($timezone instanceof DateTime)
            return $timezone->getTimezone();

        // Get the timezone from a PHPDateTimeZone object, and make sure it's valid
        else if($timezone instanceof PHPDateTime)
            return new self($timezone->getTimezone());

        // If the timezone is a string, make sure the timezone ID is valid, return the default value if not
        if(is_string($timezone)) {
            if(DateTimeZoneUtils::isValidTimezoneId($timezone))
                return new self($timezone);
            else
                return null;
        }

        // Couldn't parse the timezone, return null
        return null;
    }

    /**
     * Returns the timezone offset in seconds from GMT.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the DateTime::now() time.
     *
     * @return int|null The timezone offset in seconds, or null on failure.
     */
    public function getOffset($dateTime) {
        // Parse the date time, return null on failure
        if(($dateTime = DateTime::parse($dateTime)) === null)
            return null;

        // Get and return the timezone offset in seconds for the specified time, or return null on failure
        return ($offset = parent::getOffset($dateTime)) !== false ? $offset : null;
    }

    /**
     * Returns all transitions for the timezone in the specified time frame.
     *
     * @param int|DateTime|PHPDateTime|string|null $timestampBegin [optional] The end timestamp, a DateTime or
     *     PHPDateTime instance, the date and time as a string or null.
     *
     * @param null $timestampEnd
     *
     * @return Array|null Returns a numerically indexed array containing associative arrays with all transitions, or
     *     null on failure.
     *
     * @throws Exception Throws an exception on failure.
     *
     * @link http://php.net/manual/en/datetimezone.gettransitions.php
     */
    public function getTransitions($timestampBegin = null, $timestampEnd = null) {
        // Try to parse the timestamps for the begin and end parameter
        if($timestampBegin !== null && !is_int($timestampBegin)) {
            // Parse the timestamp, throw an exception on failure
            if(($timestampBegin = DateTime::parse($timestampBegin)) === null)
                throw new Exception('Invalid begin timestamp');

            // Gather the timestamp
            $timestampBegin = $timestampBegin->getTimestamp();
        }
        if($timestampEnd !== null && !is_int($timestampEnd)) {
            // Parse the timestamp, throw an exception on failure
            if(($timestampEnd = DateTime::parse($timestampEnd)) === null)
                throw new Exception('Invalid end timestamp');

            // Gather the timestamp
            $timestampEnd = $timestampEnd->getTimestamp();
        }

        // Get and return the transitions, return null on failure
        return ($transitions = parent::getTransitions($timestampBegin, $timestampEnd)) !== false ? $transitions : null;
    }

    /**
     * Check whether this timezone equals another timezone.
     *
     * @param DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime $timezone The other timezone as DateTimeZone or
     *     PHPDateTimeZone instance, or the timezone ID as a string. A DateTime or PHPDateTime instance may be supplied
     *     to use it's timezone.
     *
     * @return bool True if the timezones equal, false if not. False will also be returned if an error occurred.
     */
    public function equals($timezone) {
        // Parse the timezone
        if(($timezone = static::parse($timezone)) === null)
            return false;

        // Compare the timezone IDs of both instances, return the result
        return StringUtils::equals($this->getName(), $timezone->getName(), false);
    }

    /**
     * Check whether a timezone is local relative to this timezone. This compares the offset of both timezones at a
     * specific point in time.
     *
     * @param DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $timezone [optional] A DateTimeZone or
     *     PHPDateTimeZone instance, the timezone ID as a string, a DateTime or PHPDateTime instance to use it's
     *     timezone or null to use the defualt timezone.
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] The specific point in date and time to compare the
     *     offsets at. A DateTime or PHPDateTime instance, the date and time as a string or null to use the now()
     *     value.
     *
     * @return bool True if the timezone is local, false if not. False will also be returned on failure.
     */
    public function isLocal($timezone = null, $dateTime = null) {
        // Parse the timezone, return false on failure
        if(($timezone = static::parse($timezone)) === null)
            return false;

        // Parse the date and time, return false on failure
        if(($dateTime = DateTimeUtils::parse($dateTime, $timezone, null)) === null)
            return false;

        // Compare the offsets of both timezones at the specific point in time, return the result
        return $this->getOffset($dateTime) == $timezone->getOffset($dateTime);
    }

    /**
     * Get the timezone as a string. This will return it's name.
     *
     * @return string Timezone as a string.
     */
    public function toString() {
        return $this->getName();
    }

    /**
     * Get the timezone as a string. This will return it's name.
     *
     * @return string Timezone as a string.
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * Returns associative array containing dst, offset and the timezone name.
     *
     * @return Array|null An array of abbreviations, or null on failure.
     *
     * @link http://php.net/manual/en/datetimezone.listabbreviations.php
     */
    public static function listAbbreviations() {
        return ($abbreviations = parent::listAbbreviations()) !== false ? $abbreviations : null;
    }

    /**
     * Returns a numerically indexed array with all timezone identifiers.
     *
     * @param int|null $what [optional] One of DateTimeZone class constants.
     * @param string $country [optional] A two-letter ISO 3166-1 compatible country code. This option is only used if
     *     $what is set to DateTimeZone::PER_COUNTRY.
     *
     * @return Array|null An array of identifiers, or null on failure.
     *
     * @link http://php.net/manual/en/datetimezone.listidentifiers.php
     */
    public static function listIdentifiers($what = self::ALL, $country = null) {
        // Parse the what parameter
        if($what === null)
            $what = self::ALL;

        // Get and return the identifiers, return null on failure
        return ($identifiers = parent::listIdentifiers($what, $country)) !== false ? $identifiers : null;
    }

    /**
     * Get the version of the installed timezone database.
     *
     * @return string The timezone database version.
     */
    public static function getTimezoneVersion() {
        return timezone_version_get();
    }
}
