<?php

/**
 * DateTimeUtils.php
 *
 * A utilities class for the DateTime class.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Carbon CMS 2015. All rights reserved.
 */

namespace carbon\core\datetime;

use carbon\core\datetime\interval\DateInterval;
use carbon\core\datetime\zone\DateTimeZone;
use Closure;
use DateTime as PHPDateTime;
use DateTimeZone as PHPDateTimeZone;
use Exception;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * A utilities class for the DateTime class.
 *
 * @package carbon\core\datetime
 */
class DateTimeUtils {

    // TODO: Does this utilities class contain all required methods?
    // TODO: Add more getters and setters.

    /**
     * Parse a date and time with an optional time zone. A new instance will be created if required.
     *
     * If the $dateTime parameter is a DateTime zone instance, the instance will be returned and the $timezone
     * parameter is ignored. If the $dateTime parameter is anything other than a DateTime zone the date, time and the
     * time zone is parsed through the constructor.
     *
     * This method allows better fluent syntax because it makes method chaining possible.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the time as a string, or
     *     null to use the now() time.
     * @param DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $timezone [optional] The time zone the
     *     specified time is in, or null to use the default time zone if the $time param isn't a DateTime instance. A
     *     DateTime or PHPDateTime instance to use it's timezone.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return DateTime|mixed The DateTime instance, or the default value on failure.
     */
    public static function parse($dateTime = null, $timezone = null, $default = null) {
        return ($result = DateTime::parse($dateTime, $timezone)) !== null ? $result : $default;
    }

    /**
     * Get the year of a date and time.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the time as a string, or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The year, or the default value on failure.
     */
    public static function getYear($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Get and return the year
        return $dateTime->getYear();
    }

    /**
     * Get the month of a date and time.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the time as a string, or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The month, or the default value on failure.
     */
    public static function getMonth($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Get and return the month
        return $dateTime->getMonth();
    }

    /**
     * Get the day of a date and time.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the time as a string, or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The day, or the default value on failure.
     */
    public static function getDay($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Get and return the day
        return $dateTime->getDay();
    }

    /**
     * Get the hour of a date and time.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the time as a string, or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The hour, or the default value on failure.
     */
    public static function getHour($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Get and return the hour
        return $dateTime->getHour();
    }

    /**
     * Get the minute of a date and time.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the time as a string, or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The minute, or the default value on failure.
     */
    public static function getMinute($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Get and return the minute
        return $dateTime->getMinute();
    }

    /**
     * Get the second of a date and time.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the time as a string, or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The second, or the default value on failure.
     */
    public static function getSecond($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Get and return the second
        return $dateTime->getSecond();
    }

    /**
     * Check whether the date and time specified by a equals b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] The DateTime or PHPDateTime instance, the date and time as
     *     a string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b [optional] The DateTime or PHPDateTime instance, the date and time as
     *     a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if the date and time of a and b equals, false if not. The default value will be returned
     *     on failure.
     */
    public static function equals($a = null, $b = null, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Compare a and b and return the result, return the default value on failure
        return ($result = $a->equals($b)) === null ? $default : $result;
    }

    /**
     * Check whether the date and time specified by a is greater than b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] The DateTime or PHPDateTime instance, the date and time as
     *     a string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b [optional] The DateTime or PHPDateTime instance, the date and time as
     *     a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if the date and time of a is greater than b, false if not. The default value will be
     *     returned on failure.
     */
    public static function isGreaterThan($a = null, $b = null, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Compare a and b and return the result, return the default value on failure
        return ($result = $a->isGreaterThan($b)) === null ? $default : $result;
    }

    /**
     * Check whether the date and time specified by a is greater or equal to b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] The DateTime or PHPDateTime instance, the date and time as
     *     a string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b [optional] The DateTime or PHPDateTime instance, the date and time as
     *     a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if the date and time of a is greater or equal to b, false if not. The default value will
     *     be returned on failure.
     */
    public static function isGreaterOrEqualTo($a = null, $b = null, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Compare a and b and return the result, return the default value on failure
        return ($result = $a->isGreaterOrEqualTo($b)) === null ? $default : $result;
    }

    /**
     * Check whether the date and time specified by a is less than b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] The DateTime or PHPDateTime instance, the date and time as
     *     a string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b [optional] The DateTime or PHPDateTime instance, the date and time as
     *     a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if the date and time of a is less than b, false if not. The default value will be
     *     returned on failure.
     */
    public static function isLessThan($a = null, $b = null, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Compare a and b and return the result, return the default value on failure
        return ($result = $a->isLessThan($b)) === null ? $default : $result;
    }

    /**
     * Check whether the date and time specified by a is less or equal to b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] The DateTime or PHPDateTime instance, the date and time as
     *     a string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b [optional] The DateTime or PHPDateTime instance, the date and time as
     *     a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if the date and time of a is less or equal b, false if not. The default value will be
     *     returned on failure.
     */
    public static function isLessOrEqualTo($a = null, $b = null, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Compare a and b and return the result, return the default value on failure
        return ($result = $a->isLessOrEqualTo($b)) === null ? $default : $result;
    }

    /**
     * Check whether the specified date and time is between a and b.
     * The $a and $b parameter may not be null at the same time or false will be returned.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime The date and time that needs to be in between a and be as
     *     DateTime or PHPDateTime instance, the date and time as a string or null to use the now() value.
     * @param DateTime|PHPDateTime|string|null $a The date and time as DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b The date and time as DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param bool $equals [optional] True to also return true if the date equals one of the specified date and times,
     *     false otherwise.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if the date is between the specified date and time, or if it equals one of the date and
     *     times while $equals is set to true, false if not. The default value will be returned on failure.
     */
    public static function isBetween($dateTime = null, $a = null, $b = null, $equals = true, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Check whether the date and time is in between and return the result, return the default value on failure
        return ($result = $dateTime->isBetween($a, $b, $equals)) === null ? $default : $result;
    }

    /**
     * Get the greatest date and time of a and b. If both are equal, a will be returned.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return DateTime|mixed The greatest DateTime instance, or the default value on failure.
     */
    public static function max($a = null, $b = null, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Compare a and b and return the greatest, return the default value on failure
        return ($result = $a->max($b)) === null ? $default : $result;
    }

    /**
     * Get the lowest date and time of a and b. If both are equal, a will be returned.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return DateTime|mixed The lowest DateTime instance, or the default value on failure.
     */
    public static function min($a = null, $b = null, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Compare a and b and return the smallest, return the default value on failure
        return ($result = $a->min($b)) === null ? $default : $result;
    }

    /**
     * Check whether the specified date is a weekday (monday to friday).
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if this is a weekday, false if not. The default value will be returned on failure.
     */
    public static function isWeekday($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Check whether the specified date is a weekday, return the result
        return $dateTime->isWeekday();
    }

    /**
     * Check whether the specified date is a weekend day (saturday or sunday).
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if this is a weekend day, false if not. The default value on failure.
     */
    public static function isWeekend($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Check whether the specified date is in the weekend, return the result
        return $dateTime->isWeekend();
    }

    /**
     * Check whether the specified date is today.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if this is today, false if not. The default value will be returned on failure.
     */
    public static function isToday($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Check whether the specified date is today, return the result
        return $dateTime->isToday();
    }

    /**
     * Check whether the specified date is tomorrow.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool True if this is tomorrow, false if not. The default value will be returned on failure.
     */
    public static function isTomorrow($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Check whether the specified date is tomorrow, return the result
        return $dateTime->isTomorrow();
    }

    /**
     * Check whether the specified date is yesterday.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool true if this is yesterday, false if not. The default value will be returned on failure.
     */
    public static function isYesterday($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Check whether the specified date is yesterday, return the result
        return $dateTime->isYesterday();
    }

    /**
     * Check whether the specified date and time is in the future. If the date and time equals the now() date and time
     * false is returned.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool True if this is in the future, false if not. The default value will be returned on failure.
     */
    public static function isFuture($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Check whether the specified date is in the future, return the result
        return $dateTime->isFuture();
    }

    /**
     * Check whether the specified date and time is in the past. If the date and time equals the now() date and time
     * false is returned.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool True if this is in the past, false if not. The default value will be returned on failure.
     */
    public static function isPast($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Check whether the specified date is in the past, return the result
        return $dateTime->isPast();
    }

    /**
     * Check whether the specified date is a leap year.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool True if this is a leap year, false if not. The default value will be returned on failure.
     */
    public static function isLeapYear($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Check whether the specified date is a leap year, return the result
        return $dateTime->isLeapYear();
    }

    /**
     * Check whether the specified date is in daylight saving time.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool True if this is in daylight saving time, false if not. The default value will be returned on
     *     failure.
     */
    public static function isDST($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Check whether the specified date is a leap year, return the result
        return $dateTime->isDST();
    }

    /**
     * Check whether the specified date is in UTC time.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool True if this time is in UTC time, false if not. The default value will be returned on failure.
     */
    public static function isUTC($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Check whether the specified date is a leap year, return the result
        return $dateTime->isUTC();
    }

    /**
     * Check whether the year of a and b is the same.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool True if year is the same as the specified date, false otherwise. False will also be returned on
     *     failure.
     */
    public function isSameYear($a = null, $b = null, $default = null) {
        // Parse the date and time of a and b, return the default value on failure
        if(($a = static::parse($a)) === null || ($b = static::parse($b)) === null)
            return $default;

        // Check whether the time of a and b is the same and return the result
        return $a->isSameYear($b);
    }

    /**
     * Check whether the month of a and b is the same.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool True if month is the same as the specified date, false otherwise. False will also be returned on
     *     failure.
     */
    public function isSameMonth($a = null, $b = null, $default = null) {
        // Parse the date and time of a and b, return the default value on failure
        if(($a = static::parse($a)) === null || ($b = static::parse($b)) === null)
            return $default;

        // Check whether the time of a and b is the same and return the result
        return $a->isSameMonth($b);
    }

    /**
     * Check whether the ISO-8601 week of a and b is the same.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool True if the ISO-8601 week is the same as the specified date, false otherwise. False will also be
     *     returned on failure.
     */
    public function isSameWeek($a = null, $b = null, $default = null) {
        // Parse the date and time of a and b, return the default value on failure
        if(($a = static::parse($a)) === null || ($b = static::parse($b)) === null)
            return $default;

        // Check whether the time of a and b is the same and return the result
        return $a->isSameWeek($b);
    }

    /**
     * Check whether the day of a and b is the same.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if the date of a and b are the same, false if not. The default value will be returned on
     *     failure.
     */
    public static function isSameDate($a = null, $b = null, $default = null) {
        // Parse the date and time of a and b, return the default value on failure
        if(($a = static::parse($a)) === null || ($b = static::parse($b)) === null)
            return $default;

        // Check whether the time of a and b is the same and return the result
        return $a->isSameDate($b);
    }

    /**
     * Check whether the hour of a and b is the same.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param bool $checkDate [optional] True to make sure the dates are equal, false to just compare the time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool True if hour is the same as the specified date and time, false otherwise. False will also be
     *     returned on failure.
     */
    public function isSameHour($a = null, $b = null, $checkDate = true, $default = null) {
        // Parse the date and time of a and b, return the default value on failure
        if(($a = static::parse($a)) === null || ($b = static::parse($b)) === null)
            return $default;

        // Check whether the time of a and b is the same and return the result
        return $a->isSameHour($b, $checkDate);
    }

    /**
     * Check whether the minute of a and b is the same.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param bool $checkDate [optional] True to make sure the dates are equal, false to just compare the time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool True if minute is the same as the specified date and time, false otherwise. False will also be
     *     returned on failure.
     */
    public function isSameMinute($a = null, $b = null, $checkDate = true, $default = null) {
        // Parse the date and time of a and b, return the default value on failure
        if(($a = static::parse($a)) === null || ($b = static::parse($b)) === null)
            return $default;

        // Check whether the time of a and b is the same and return the result
        return $a->isSameMinute($b, $checkDate);
    }

    /**
     * Check whether the time of a and b is the same.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() time.
     * @param bool $checkDate [optional] True to make sure the dates are equal, false to just compare the time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool True if time is the same as the specified date and time, false otherwise. False will also be
     *     returned on failure.
     */
    public function isSameTime($a = null, $b = null, $checkDate = true, $default = null) {
        // Parse the date and time of a and b, return the default value on failure
        if(($a = static::parse($a)) === null || ($b = static::parse($b)) === null)
            return $default;

        // Check whether the time of a and b is the same and return the result
        return $a->isSameTime($b, $checkDate);
    }

    /**
     * Get the difference of a and b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in years.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return DateInterval|mixed The difference of a and b, or the default value on failure.
     */
    public static function diff($a = null, $b = null, $absolute = true, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Get the difference of a and b and return the result, return the default value on failure
        return ($result = $a->diff($b, $absolute)) === null ? $default : $result;
    }

    /**
     * Get the difference of a and b by the given interval using a filter closure.
     * The callback will be called for each period in the given time frame. If the callback returns true the period is
     * included as difference, false should be returned otherwise.
     *
     * @param DateInterval $dateInterval An interval to traverse by.
     * @param Closure $callback The callback function to call for each period as filter.
     * @param DateTime|PHPDateTime|string|null $a [optional] The DateTime or PHPDateTime instance, the date and time as
     *     a string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $b [optional] The DateTime or PHPDateTime instance, the date and time as
     *     a string or null to use the now() date and time.
     * @param boolean $absolute [optional] True to get the absolute difference, false otherwise.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The difference of the date interval in the given time frame. The default value will be
     *     returned on failure.
     */
    public function diffFiltered($dateInterval, Closure $callback, $a = null, $b = null, $absolute = true,
                                 $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Call the difference filtered method on a and return the result, return the default value on failure
        return ($result = $a->diffFiltered($dateInterval, $callback, $b, $absolute)) === null ? $default : $result;
    }

    /**
     * Get the difference in years of a and b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in years.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The difference in years of a and b, or the default value on failure.
     */
    public static function diffInYears($a = null, $b = null, $absolute = true, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Get the difference in years of a and b and return the result, return the default value on failure
        return ($result = $a->diffInYears($b, $absolute)) === null ? $default : $result;
    }

    /**
     * Get the difference in months of a and b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in months.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The difference in years of a and b, or the default value on failure.
     */
    public static function diffInMonths($a = null, $b = null, $absolute = true, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Get the difference in months of a and b and return the result, return the default value on failure
        return ($result = $a->diffInMonths($b, $absolute)) === null ? $default : $result;
    }

    /**
     * Get the difference in weeks of a and b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in weeks.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The difference in years of a and b, or the default value on failure.
     */
    public static function diffInWeeks($a = null, $b = null, $absolute = true, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Get the difference in weeks of a and b and return the result, return the default value on failure
        return ($result = $a->diffInWeeks($b, $absolute)) === null ? $default : $result;
    }

    /**
     * Get the difference in weekdays of a and b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param boolean $absolute Get the absolute of the difference.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int The difference in weekdays, or the default value returned on failure.
     */
    public function diffInWeekdays($a = null, $b = null, $absolute = true, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Get the difference in weekdays of a and b and return the result, return the default value on failure
        return ($result = $a->diffInWeekdays($b, $absolute)) === null ? $default : $result;
    }

    /**
     * Get the difference in weekend days of a and b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param boolean $absolute Get the absolute of the difference.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int The difference in weekend days, or the default value returned on failure.
     */
    public function diffInWeekendDays($a = null, $b = null, $absolute = true, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Get the difference in weekend days of a and b and return the result, return the default value on failure
        return ($result = $a->diffInWeekendDays($b, $absolute)) === null ? $default : $result;
    }

    /**
     * Get the difference in days of a and b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in days.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The difference in years of a and b, or the default value on failure.
     */
    public static function diffInDays($a = null, $b = null, $absolute = true, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Get the difference in days of a and b and return the result, return the default value on failure
        return ($result = $a->diffInDays($b, $absolute)) === null ? $default : $result;
    }

    /**
     * Get the difference in days of a and b using a filter closure.
     *
     * @param Closure $callback The callback function to call for each day as filter.
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param boolean $absolute Get the absolute of the difference.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|null The difference in days. The default value will be returned on failure.
     */
    public function diffInDaysFiltered(Closure $callback, $a = null, $b = null, $absolute = true, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Get the difference in days of a and b using a filter closure and return the result, return the default value on failure
        return ($result = $a->diffInDaysFiltered($callback, $b, $absolute)) === null ? $default : $result;
    }

    /**
     * Get the difference in hours of a and b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in hours.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The difference in years of a and b, or the default value on failure.
     */
    public static function diffInHours($a = null, $b = null, $absolute = true, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Get the difference in hours of a and b and return the result, return the default value on failure
        return ($result = $a->diffInHours($b, $absolute)) === null ? $default : $result;
    }

    /**
     * Get the difference in hours of a and b using a filter closure.
     *
     * @param Closure $callback The callback function to call for each hour as filter.
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param boolean $absolute Get the absolute of the difference.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|null The difference in hours. The default value will be returned on failure.
     */
    public function diffInHoursFiltered(Closure $callback, $a = null, $b = null, $absolute = true, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Get the difference in hours of a and b using a filter closure and return the result, return the default value on failure
        return ($result = $a->diffInHoursFiltered($callback, $b, $absolute)) === null ? $default : $result;
    }

    /**
     * Get the difference in minutes of a and b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in minutes.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The difference in years of a and b, or the default value on failure.
     */
    public static function diffInMinutes($a = null, $b = null, $absolute = true, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Get the difference in minutes of a and b and return the result, return the default value on failure
        return ($result = $a->diffInMinutes($b, $absolute)) === null ? $default : $result;
    }

    /**
     * Get the difference in seconds of a and b.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $b [optional] A DateTime or PHPDateTime instance, the date and time as a
     *     string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in seconds.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The difference in years of a and b, or the default value on failure.
     */
    public static function diffInSeconds($a = null, $b = null, $absolute = true, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Get the difference in seconds of a and b and return the result, return the default value on failure
        return ($result = $a->diffInSeconds($b, $absolute)) === null ? $default : $result;
    }

    /**
     * Get the number of seconds since midnight of the specified date and time.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as string or null to use the now() date and time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The number of seconds or the default value on failure.
     */
    public static function secondsSinceMidnight($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Get and return the number of seconds since midnight
        return $dateTime->secondsSinceMidnight();
    }

    /**
     * Get the number of seconds of the specified date and time until the end of the day, which is 23:23:59.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as string or null to use the now() date and time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The number of seconds until the end of the day, or the default value on failure.
     */
    public static function secondsUntilEndOfDay($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Get and return the number of seconds since midnight
        return $dateTime->secondsUntilEndOfDay();
    }

    /**
     * Get the age of the specified date and time compared to the current date and time specified by the now() method.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] The DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|mixed The age of the date and time, or the default value on failure.
     */
    public function getAge($dateTime = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Get the age, return the default value on failure
        if(($age = $dateTime->getAge()) === null)
            return $default;

        // Return the age
        return $age;
    }

    /**
     * Check whether it's the birthday of the specified date and time. This compares the month and day of both dates.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] The DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $birthday [optional] The DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return boolean True if it's the birthday, false if not. The default value will be returned on failure.
     */
    public static function isBirthday($dateTime = null, $birthday = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Check whether it's the birthday of the specified date and time and return the result, return the default value on failure
        return ($result = $dateTime->isBirthday($birthday)) === null ? $default : $result;
    }

    /**
     * Get the average in date and time of a and b. A new DateTime instance is returned with the average date and time.
     *
     * @param DateTime|PHPDateTime|string|null $a [optional] The DateTime or PHPDateTime instance, the date and time as
     *     a string or null to use the now() date and time.
     * @param DateTime|PHPDateTime|string|null $b [optional] The DateTime or PHPDateTime instance, the date and time as
     *     a string or null to use the now() date and time.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return DateTime|mixed The average as DateTime instance, or the default value on failure.
     */
    public static function average($a = null, $b = null, $default = null) {
        // Parse the date and time of a, return the default value on failure
        if(($a = static::parse($a)) === null)
            return $default;

        // Get the average date and time of a and b and return the result, return the default value on failure
        return ($average = $a->copy()->average($b)) !== null ? $average : $default;
    }

    /**
     * Get the sun information at the specified time for the given location as an array with information about
     * sunset/sunrise and twilight begin/end. The returned array will contain the following keys:
     * - sunrise
     * - sunset
     * - transit
     * - civil_twilight_begin
     * - civil_twilight_end
     * - nautical_twilight_begin
     * - nautical_twilight_end
     * - astronomical_twilight_begin
     * - astronomical_twilight_end
     * All array values are specified as DateTime objects.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string, or null to use the DateTime::now() date and time.
     * @param float|null $latitude [optional] The latitude, or null to use the default latitude specified by
     *     'date.default_latitude' in PHPs INI.
     * @param float|null $longitude [optional] The longitude, or null to use the default longitude specified by
     *     'date.default_longitude' in PHPs INI.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return Array|mixed An array with information about sunset/sunrise and twilight begin/end, or the default value
     *     on failure.
     */
    public static function getSunInfo($dateTime = null, $latitude = null, $longitude = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Get and return the sun info, return null on failure
        return ($sunInfo = $dateTime->getSunInfo($latitude, $longitude)) !== null ? $sunInfo : $default;
    }

    /**
     * Get the sunrise time at the specified time for a given location.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the DateTime::now() date and time.
     * @param float|null $latitude [optional] The latitude, or null to use the default latitude specified by
     *     'date.default_latitude' in PHPs INI.
     * @param float|null $longitude [optional] The longitude, or null to use the default longitude specified by
     *     'date.default_longitude' in PHPs INI.
     * @param float|null $zenith [optional] The sunrise zenith, or null to use the default value specified by
     *     'date.sunrise_zenith' in PHPs INI.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return DateTime|mixed The sunrise time as DateTime object, or the default value on failure.
     *
     * @throws Exception Throws an exception on failure.
     */
    public function getSunrise($dateTime = null, $latitude = null, $longitude = null, $zenith = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Get and return the sunrise time, return the default value on failure
        return ($sunrise = $dateTime->getSunrise($latitude, $longitude, $zenith)) !== null ? $sunrise : $default;
    }

    /**
     * Get the sunset time at the specified time for a given location.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the DateTime::now() date and time.
     * @param float|null $latitude [optional] The latitude, or null to use the default latitude specified by
     *     'date.default_latitude' in PHPs INI.
     * @param float|null $longitude [optional] The longitude, or null to use the default longitude specified by
     *     'date.default_longitude' in PHPs INI.
     * @param float|null $zenith [optional] The sunset zenith, or null to use the default value specified by
     *     'date.sunset_zenith' in PHPs INI.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return DateTime|mixed The sunset time as DateTime object, or the default value on failure.
     *
     * @throws Exception Throws an exception on failure.
     */
    public function getSunset($dateTime = null, $latitude = null, $longitude = null, $zenith = null, $default = null) {
        // Parse the date and time, return the default value on failure
        if(($dateTime = static::parse($dateTime)) === null)
            return $default;

        // Get and return the sunset time, return the default value on failure
        return ($sunset = $dateTime->getSunset($latitude, $longitude, $zenith)) !== null ? $sunset : $default;
    }
}