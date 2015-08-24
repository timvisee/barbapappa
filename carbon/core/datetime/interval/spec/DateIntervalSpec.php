<?php

/**
 * DateIntervalSpec.php
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Carbon CMS 2015. All rights reserved.
 */

namespace carbon\core\datetime\interval\spec;

use carbon\core\datetime\DateTime;
use carbon\core\datetime\interval\DateInterval;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Class DateIntervalSpec
 *
 * @package carbon\core\datetime\interval\spec
 */
class DateIntervalSpec {

    /**
     * Create a date interval specification based on the given parameters.
     *
     * @param int|null $years [optional] The number of years, must be zero or a positive number. Null to ignore this
     *     value.
     * @param int|null $months [optional] The number of months, must be zero or a positive number. Null to ignore this
     *     value.
     * @param int|null $weeks [optional] The number of weeks, must be zero or a positive number. Null to ignore this
     *     value. The weeks are converted into days.
     * @param int|null $days [optional] The number of days, must be zero or a positive number. Null to ignore this
     *     value.
     * @param int|null $hours [optional] The number of hours, must be zero or a positive number. Null to ignore this
     *     value.
     * @param int|null $minutes [optional] The number of minutes, must be zero or a positive number. Null to ignore
     *     this value.
     * @param int|null $seconds [optional] The number of seconds, must be zero or a positive number. Null to ignore
     *     this value.
     *
     * @return string|null The date interval spec as a string, or null on failure.
     */
    // TODO: Allow and return a default value, or should we throw an exception!
    public static function create($years = null, $months = null, $weeks = null, $days = null, $hours = null,
                                  $minutes = null, $seconds = null) {
        // Build the date interval specification string
        $dateIntervalSpec = DateInterval::PERIOD_PREFIX;

        // Check whether the weeks parameter is used, append the number of days
        if(!empty($weeks))
            $days += (int) ($weeks * DateTime::DAYS_PER_WEEK);

        // Reading all non-zero date parts
        $date = array_filter(array(
            DateInterval::PERIOD_YEARS => $years,
            DateInterval::PERIOD_MONTHS => $months,
            DateInterval::PERIOD_DAYS => $days
        ));

        // Reading all non-zero time parts
        $time = array_filter(array(
            DateInterval::PERIOD_HOURS => $hours,
            DateInterval::PERIOD_MINUTES => $minutes,
            DateInterval::PERIOD_SECONDS => $seconds
        ));

        // Make sure at least one part is available
        if(empty($date) && empty($time))
            $date = array(DateInterval::PERIOD_YEARS => 0);

        // Append each date part to the specification string
        foreach($date as $key => $value)
            $dateIntervalSpec .= $value . $key;

        // Append each time part to the specification string if available
        if(!empty($time)) {
            // Prefix the time designator
            $dateIntervalSpec .= DateInterval::PERIOD_TIME_PREFIX;

            // Append each time part to the specification string
            foreach($time as $key => $value)
                $dateIntervalSpec .= $value . $key;
        }

        // Return the spec if it's valid, return null otherwise
        return DateIntervalSpecUtils::isValid($dateIntervalSpec) ? $dateIntervalSpec : null;
    }

    /**
     * Create a date interval specification of zero.
     *
     * @return string The date interval specification.
     */
    public static function createZero() {
        return static::create();
    }

    /**
     * Create a date interval specification for one, or the given number of years.
     *
     * @param int $years [optional] The number of years. Null to use one year.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The date interval specification, or the default value on failure.
     */
    public static function createYear($years = 1, $default = null) {
        // Parse the year parameter, and make sure it's valid
        if($years === null)
            $years = 1;

        // Create and return a new date interval specification, or return the default value on failure
        return static::create(null, null, null, $years, null, null, null, $default);
    }

    /**
     * Alias of year();
     *
     * Create a date interval specification for one, or the given number of years.
     *
     * @param int $years [optional] The number of years. Null to use one year.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The date interval specification, or the default value on failure.
     */
    public static function createYears($years = 1, $default = null) {
        return static::createYear($years, $default);
    }

    /**
     * Create a date interval specification for one, or the given number of months.
     *
     * @param int $months [optional] The number of months. Null to use one month.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The date interval specification, or the default value on failure.
     */
    public static function createMonth($months = 1, $default = null) {
        // Parse the month parameter, and make sure it's valid
        if($months === null)
            $months = 1;

        // Create and return a new date interval specification, or return the default value on failure
        return static::create(null, null, null, $months, null, null, null, $default);
    }

    /**
     * Alias of month();
     *
     * Create a date interval specification for one, or the given number of months.
     *
     * @param int $months [optional] The number of months. Null to use one month.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The date interval specification, or the default value on failure.
     */
    public static function createMonths($months = 1, $default = null) {
        return static::createMonth($months, $default);
    }

    /**
     * Create a date interval specification for one, or the given number of weeks.
     *
     * @param int $weeks [optional] The number of weeks. Null to use one week.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The date interval specification, or the default value on failure.
     */
    public static function createWeek($weeks = 1, $default = null) {
        // Parse the week parameter, and make sure it's valid
        if($weeks === null)
            $weeks = 1;

        // Create and return a new date interval specification, or return the default value on failure
        return static::create(null, null, null, $weeks, null, null, null, $default);
    }

    /**
     * Alias of week();
     *
     * Create a date interval specification for one, or the given number of weeks.
     *
     * @param int $weeks [optional] The number of weeks. Null to use one week.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The date interval specification, or the default value on failure.
     */
    public static function createWeeks($weeks = 1, $default = null) {
        return static::createWeek($weeks, $default);
    }

    /**
     * Create a date interval specification for one, or the given number of days.
     *
     * @param int $days [optional] The number of days. Null to use one day.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The date interval specification, or the default value on failure.
     */
    public static function createDay($days = 1, $default = null) {
        // Parse the day parameter, and make sure it's valid
        if($days === null)
            $days = 1;

        // Create and return a new date interval specification, or return the default value on failure
        return static::create(null, null, null, $days, null, null, null, $default);
    }

    /**
     * Alias of day();
     *
     * Create a date interval specification for one, or the given number of days.
     *
     * @param int $days [optional] The number of days. Null to use one day.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The date interval specification, or the default value on failure.
     */
    public static function createDays($days = 1, $default = null) {
        return static::createDay($days, $default);
    }

    /**
     * Create a date interval specification for one, or the given number of hours.
     *
     * @param int $hours [optional] The number of hours. Null to use one hour.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The date interval specification, or the default value on failure.
     */
    public static function createHour($hours = 1, $default = null) {
        // Parse the hour parameter, and make sure it's valid
        if($hours === null)
            $hours = 1;

        // Create and return a new date interval specification, or return the default value on failure
        return static::create(null, null, null, $hours, null, null, null, $default);
    }

    /**
     * Alias of hour();
     *
     * Create a date interval specification for one, or the given number of hours.
     *
     * @param int $hours [optional] The number of hours. Null to use one hour.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The date interval specification, or the default value on failure.
     */
    public static function createHours($hours = 1, $default = null) {
        return static::createHour($hours, $default);
    }

    /**
     * Create a date interval specification for one, or the given number of minutes.
     *
     * @param int $minutes [optional] The number of minutes. Null to use one minute.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The date interval specification, or the default value on failure.
     */
    public static function createMinute($minutes = 1, $default = null) {
        // Parse the minute parameter, and make sure it's valid
        if($minutes === null)
            $minutes = 1;

        // Create and return a new date interval specification, or return the default value on failure
        return static::create(null, null, null, $minutes, null, null, null, $default);
    }

    /**
     * Alias of minute();
     *
     * Create a date interval specification for one, or the given number of minutes.
     *
     * @param int $minutes [optional] The number of minutes. Null to use one minute.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The date interval specification, or the default value on failure.
     */
    public static function createMinutes($minutes = 1, $default = null) {
        return static::createMinute($minutes, $default);
    }

    /**
     * Create a date interval specification for one, or the given number of seconds.
     *
     * @param int $seconds [optional] The number of seconds. Null to use one second.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The date interval specification, or the default value on failure.
     */
    public static function createSecond($seconds = 1, $default = null) {
        // Parse the second parameter, and make sure it's valid
        if($seconds === null)
            $seconds = 1;

        // Create and return a new date interval specification, or return the default value on failure
        return static::create(null, null, null, $seconds, null, null, null, $default);
    }

    /**
     * Alias of second();
     *
     * Create a date interval specification for one, or the given number of seconds.
     *
     * @param int $seconds [optional] The number of seconds. Null to use one second.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|mixed The date interval specification, or the default value on failure.
     */
    public static function createSeconds($seconds = 1, $default = null) {
        return static::createSecond($seconds, $default);
    }
}
