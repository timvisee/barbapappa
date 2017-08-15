<?php

/**
 * DateIntervalUtils.php
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Carbon CMS 2015. All rights reserved.
 */

namespace carbon\core\datetime\interval;

use DateInterval as PHPDateInterval;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Class DateIntervalUtils
 *
 * @package carbon\core\datetime\interval
 */
class DateIntervalUtils {

    // TODO: Does this utilities class contain all required methods?
    // TODO: Create an array utilities class!

    /**
     * Before PHP 5.4.20/5.5.4 instead of false days will be set to -99999 when the interval instance was created by
     * DateTime:diff().
     *
     * @const int The old PHP false value of days.
     */
    const PHP_OLD_FALSE_DAYS = -99999;

    /**
     * Parse a date interval. A new instance may be created.
     *
     * This method allows better fluent syntax because it makes method chaining possible.
     *
     * @param DateInterval|PHPDateInterval|string|null $dateInterval [optional] A DateInterval or PHPDateInterval
     *     instance, a date interval specification, or null to use a zero specification.
     * @param mixed|null $default [optional] The default value to be returned on failure.
     *
     * @return static|mixed A DateInterval instance, or the default value on failure.
     */
    public static function parse($dateInterval, $default = null) {
        return ($result = DateInterval::parse($dateInterval)) === null ? $default : $result;
    }

    /**
     * Check whether this date time object was created using DateTime::diff() or PHPDateTime::diff().
     *
     * @param DateInterval|PHPDateInterval $dateInterval The DateInterval or PHPDateInterval instance.
     *
     * @return bool True if this date interval object was created by a diff() method, false if not. If the date
     *     interval isn't an instance of DateInterval false will also be returned.
     */
    public static function isCreatedFromDiff($dateInterval) {
        // Make sure the date interval isn't null
        if($dateInterval == null)
            return false;

        // Make sure the date interval is a DateInterval or PHPDateInterval instance
        if(!($dateInterval instanceof PHPDateInterval))
            return false;

        // Get the value to compare the days to, this differs depending on the installed PHP version
        $compareTo = (
            version_compare(phpversion(), '5.4.20', '<') ||
            (version_compare(phpversion(), '5.0', '>=') && version_compare(phpversion(), '5.5.4', '<'))
        ) ? static::PHP_OLD_FALSE_DAYS : false;

        // Make sure the number of days, return the result
        return $dateInterval->days != $compareTo;
    }
}
