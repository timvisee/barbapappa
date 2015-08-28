<?php

/**
 * DateInterval.php
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Carbon CMS 2015. All rights reserved.
 */

namespace carbon\core\datetime\interval;

use carbon\core\datetime\DateTime;
use carbon\core\datetime\interval\spec\DateIntervalSpec;
use carbon\core\datetime\interval\spec\DateIntervalSpecUtils;
use carbon\core\util\StringUtils;
use DateInterval as PHPDateInterval;
use DomainException;
use Exception;
use InvalidArgumentException;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Class DateInterval
 *
 * @package carbon\core\datetime
 *
 * @property int $intervalYears Total years of the current interval.
 * @property int $intervalMonths Total months of the current interval.
 * @property int $intervalWeeks Total weeks of the current interval calculated from the days.
 * @property-read int $intervalDaysExcludeWeeks Total days remaining in the final week of the current instance (days %
 *     7).
 * @property int $intervalDays Total days of the current interval.
 * @property int $intervalHours Total hours of the current interval.
 * @property int $intervalMinutes Total minutes of the current interval.
 * @property int $intervalSeconds Total seconds of the current interval.
 */
class DateInterval extends PHPDateInterval {

    // TODO: Throw better exceptions!
    // TODO: Create array utils!
    // TODO: toString() for humans!
    // TODO: Create a DateInterval set class!

    /**
     * The date interval specification prefix designator.
     *
     * @const string The period prefix.
     */
    const PERIOD_PREFIX = 'P';
    /**
     * The date interval specification year designator.
     *
     * @const string The year prefix.
     */
    const PERIOD_YEARS = 'Y';
    /**
     * The date interval specification month designator.
     *
     * @const string The month prefix.
     */
    const PERIOD_MONTHS = 'M';
    /**
     * The date interval specification day designator.
     *
     * @const string The day prefix.
     */
    const PERIOD_DAYS = 'D';
    /**
     * The date interval specification time designator.
     *
     * @const string The time prefix.
     */
    const PERIOD_TIME_PREFIX = 'T';
    /**
     * The date interval specification hour designator.
     *
     * @const string The hour prefix.
     */
    const PERIOD_HOURS = 'H';
    /**
     * The date interval specification minute designator.
     *
     * @const string The minute prefix.
     */
    const PERIOD_MINUTES = 'M';
    /**
     * The date interval specification second designator.
     *
     * @const string The second prefix.
     */
    const PERIOD_SECONDS = 'S';

    /**
     * Constructor.
     *
     * @param string|PHPDateInterval|PHPDateInterval|null $dateIntervalSpec [optional] A date interval specification, a
     *     relative date and time string, a DateInterval or PHPDateInterval instance, or null to use a zero
     *     specification.
     * @param bool $inverted [optional] Defines whether the date interval specification is inverted or not, this
     *     parameter is ignored if a DateInterval or PHPDateInterval instance is given for the $dateIntervalSpec
     *     parameter.
     *
     * @throws Exception Throws an exception on failure.
     */
    public function __construct($dateIntervalSpec, $inverted = false) {
        // Try to parse the date interval specification
        if(($spec = DateIntervalSpecUtils::parse($dateIntervalSpec, null)) === null)
            throw new Exception('Invalid date interval specification (\'' . $dateIntervalSpec . '\' was given)');

        // Copy the inverted state from DateInterval objects
        if($dateIntervalSpec instanceof parent)
            $inverted = $dateIntervalSpec->invert;

        // Construct the parent object, and set whether the date interval is inverted
        parent::__construct($spec);
        $this->setInverted($inverted);
    }

    /**
     * Parse a date interval. A new instance may be created.
     *
     * This method allows better fluent syntax because it makes method chaining possible.
     *
     * @param PHPDateInterval|PHPDateInterval|string|null $dateInterval [optional] A DateInterval or PHPDateInterval
     *     instance, a date interval specification, or null to use a zero specification.
     *
     * @return static A DateInterval instance, or null on failure.
     */
    public static function parse($dateInterval) {
        // Return the object if it's already a DateInterval instance
        if($dateInterval instanceof self)
            return $dateInterval;

        // Parse PHPDateInterval objects
        if($dateInterval instanceof parent)
            return new static($dateInterval, $dateInterval->invert);

        // Return a zero specification if the specification is set to null
        if($dateInterval === null)
            return static::createZero();

        // Try to parse the string as date interval specification, return null on failure
        if(($dateIntervalSpec = DateIntervalSpecUtils::parse($dateInterval, null)) !== null)
            // Create and return a new date interval instance based on the parsed specification
            return new static($dateIntervalSpec);

        // Couldn't parse the date interval instance, return null
        return null;
    }

    /**
     * Create a new DateInterval instance from specific values.
     *
     * @param int $years [optional] The number of years, or null to ignore this value.
     * @param int $months [optional] The number of months, or null to ignore this value.
     * @param int $weeks [optional] The number of weeks, or null to ignore this value.
     * @param int $days [optional] The number of days, or null to ignore this value.
     * @param int $hours [optional] The number of hours, or null to ignore this value.
     * @param int $minutes [optional] The number of minutes, or null to ignore this value.
     * @param int $seconds [optional] The number of seconds, or null to ignore this value.
     * @param bool $inverted [optional] Define whether the date interval is inverted or not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed A new DateInterval instance, or the default value on failure.
     */
    public static function create($years = null, $months = null, $weeks = null, $days = null, $hours = null,
                                  $minutes = null, $seconds = null, $inverted = false, $default = null) {
        // Create the specification, and make sure it's valid
        if(($spec = DateIntervalSpec::create($years, $months, $weeks, $days, $hours, $minutes, $seconds)) === null)
            return $default;

        // Construct the DateInterval object
        return new static($spec, $inverted);
    }

    /**
     * Create a DateInterval instance based on the relative parts of the string. The date interval specification may
     * not be negative or an exception will be thrown.
     *
     * @param string $dateInterval The string with the relative parts for the date interval.
     *
     * @return static The DateInterval instance, may be zero if the string doesn't have any relative parts.
     *
     * @throw DomainException Throws an exception if the date interval specification isn't a string.
     */
    public static function createFromDateString($dateInterval) {
        // Make sure the date interval parameter is a string, throw an exception if this is not the case
        if(!is_string($dateInterval)) {
            /** @noinspection PhpParamsInspection */
            $type = is_object($dateInterval) ? get_class($dateInterval) : gettype($dateInterval);
            throw new DomainException('The date interval specification must be a string (' . $type . ' given)');
        }

        // Create and return a DateInterval instance
        return self::parse(parent::createFromDateString($dateInterval));
    }

    /**
     * Create a date interval object of zero.
     *
     * @return static|null The DateInterval object.
     */
    public static function createZero() {
        return static::create();
    }

    /**
     * Create a date interval object for one, or the given number of years.
     *
     * @param int $years [optional] The number of years. Null to use one year.
     * @param bool $inverted [optional] True to invert the date interval, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed The DateInterval instance, or the default value on failure.
     */
    public static function createYear($years = 1, $inverted = false, $default = null) {
        // Parse the year parameter, and make sure it's valid
        if($years === null)
            $years = 1;

        // Create and return a new DateInterval instance, or return the default value on failure
        return static::create(null, null, null, $years, null, null, null, $inverted, $default);
    }

    /**
     * Alias of year();
     *
     * Create a date interval object for one, or the given number of years.
     *
     * @param int $years [optional] The number of years. Null to use one year.
     * @param bool $inverted [optional] True to invert the date interval, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed The DateInterval instance, or the default value on failure.
     */
    public static function createYears($years = 1, $inverted = false, $default = null) {
        return static::createYear($years, $inverted, $default);
    }

    /**
     * Create a date interval object for one, or the given number of months.
     *
     * @param int $months [optional] The number of months. Null to use one month.
     * @param bool $inverted [optional] True to invert the date interval, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed The DateInterval instance, or the default value on failure.
     */
    public static function createMonth($months = 1, $inverted = false, $default = null) {
        // Parse the month parameter, and make sure it's valid
        if($months === null)
            $months = 1;

        // Create and return a new DateInterval instance, or return the default value on failure
        return static::create(null, null, null, $months, null, null, null, $inverted, $default);
    }

    /**
     * Alias of month();
     *
     * Create a date interval object for one, or the given number of months.
     *
     * @param int $months [optional] The number of months. Null to use one month.
     * @param bool $inverted [optional] True to invert the date interval, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed The DateInterval instance, or the default value on failure.
     */
    public static function createMonths($months = 1, $inverted = false, $default = null) {
        return static::createMonth($months, $inverted, $default);
    }

    /**
     * Create a date interval object for one, or the given number of weeks.
     *
     * @param int $weeks [optional] The number of weeks. Null to use one week.
     * @param bool $inverted [optional] True to invert the date interval, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed The DateInterval instance, or the default value on failure.
     */
    public static function createWeek($weeks = 1, $inverted = false, $default = null) {
        // Parse the week parameter, and make sure it's valid
        if($weeks === null)
            $weeks = 1;

        // Create and return a new DateInterval instance, or return the default value on failure
        return static::create(null, null, null, $weeks, null, null, null, $inverted, $default);
    }

    /**
     * Alias of week();
     *
     * Create a date interval object for one, or the given number of weeks.
     *
     * @param int $weeks [optional] The number of weeks. Null to use one week.
     * @param bool $inverted [optional] True to invert the date interval, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed The DateInterval instance, or the default value on failure.
     */
    public static function createWeeks($weeks = 1, $inverted = false, $default = null) {
        return static::createWeek($weeks, $inverted, $default);
    }

    /**
     * Create a date interval object for one, or the given number of days.
     *
     * @param int $days [optional] The number of days. Null to use one day.
     * @param bool $inverted [optional] True to invert the date interval, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed The DateInterval instance, or the default value on failure.
     */
    public static function createDay($days = 1, $inverted = false, $default = null) {
        // Parse the day parameter, and make sure it's valid
        if($days === null)
            $days = 1;

        // Create and return a new DateInterval instance, or return the default value on failure
        return static::create(null, null, null, $days, null, null, null, $inverted, $default);
    }

    /**
     * Alias of day();
     *
     * Create a date interval object for one, or the given number of days.
     *
     * @param int $days [optional] The number of days. Null to use one day.
     * @param bool $inverted [optional] True to invert the date interval, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed The DateInterval instance, or the default value on failure.
     */
    public static function createDays($days = 1, $inverted = false, $default = null) {
        return static::createDay($days, $inverted, $default);
    }

    /**
     * Create a date interval object for one, or the given number of hours.
     *
     * @param int $hours [optional] The number of hours. Null to use one hour.
     * @param bool $inverted [optional] True to invert the date interval, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed The DateInterval instance, or the default value on failure.
     */
    public static function createHour($hours = 1, $inverted = false, $default = null) {
        // Parse the hour parameter, and make sure it's valid
        if($hours === null)
            $hours = 1;

        // Create and return a new DateInterval instance, or return the default value on failure
        return static::create(null, null, null, $hours, null, null, null, $inverted, $default);
    }

    /**
     * Alias of hour();
     *
     * Create a date interval object for one, or the given number of hours.
     *
     * @param int $hours [optional] The number of hours. Null to use one hour.
     * @param bool $inverted [optional] True to invert the date interval, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed The DateInterval instance, or the default value on failure.
     */
    public static function createHours($hours = 1, $inverted = false, $default = null) {
        return static::createHour($hours, $inverted, $default);
    }

    /**
     * Create a date interval object for one, or the given number of minutes.
     *
     * @param int $minutes [optional] The number of minutes. Null to use one minute.
     * @param bool $inverted [optional] True to invert the date interval, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed The DateInterval instance, or the default value on failure.
     */
    public static function createMinute($minutes = 1, $inverted = false, $default = null) {
        // Parse the minute parameter, and make sure it's valid
        if($minutes === null)
            $minutes = 1;

        // Create and return a new DateInterval instance, or return the default value on failure
        return static::create(null, null, null, $minutes, null, null, null, $inverted, $default);
    }

    /**
     * Alias of minute();
     *
     * Create a date interval object for one, or the given number of minutes.
     *
     * @param int $minutes [optional] The number of minutes. Null to use one minute.
     * @param bool $inverted [optional] True to invert the date interval, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed The DateInterval instance, or the default value on failure.
     */
    public static function createMinutes($minutes = 1, $inverted = false, $default = null) {
        return static::createMinute($minutes, $inverted, $default);
    }

    /**
     * Create a date interval object for one, or the given number of seconds.
     *
     * @param int $seconds [optional] The number of seconds. Null to use one second.
     * @param bool $inverted [optional] True to invert the date interval, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed The DateInterval instance, or the default value on failure.
     */
    public static function createSecond($seconds = 1, $inverted = false, $default = null) {
        // Parse the second parameter, and make sure it's valid
        if($seconds === null)
            $seconds = 1;

        // Create and return a new DateInterval instance, or return the default value on failure
        return static::create(null, null, null, $seconds, null, null, null, $inverted, $default);
    }

    /**
     * Alias of second();
     *
     * Create a date interval object for one, or the given number of seconds.
     *
     * @param int $seconds [optional] The number of seconds. Null to use one second.
     * @param bool $inverted [optional] True to invert the date interval, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return static|mixed The DateInterval instance, or the default value on failure.
     */
    public static function createSeconds($seconds = 1, $inverted = false, $default = null) {
        return static::createSecond($seconds, $inverted, $default);
    }

    /**
     * Create a new DateInterval instance. A DateInterval object is returned even if the date interval parameter uses a
     * different type of object.
     *
     * @param DateInterval|PHPDateInterval|string|null $dateInterval A DateInterval or PHPDateInterval instance, the
     *     date interval specification as a string, or the relative time as a string. Null to create a zero
     *     configuration.
     *
     * @return static|null A new DateTime instance, or null on failure.
     */
    public static function instance($dateInterval) {
        // Parse DateInterval instances, create a new instance
        if($dateInterval instanceof self)
            return new static($dateInterval->toSpecString(), $dateInterval->isInverted());

        // Try to parse and instantiate other specifications and return the result
        return static::parse($dateInterval);
    }

    /**
     * Create a copy of this instance.
     *
     * @return PHPDateInterval The DateInterval instance copy.
     */
    public function copy() {
        return static::instance($this);
    }

    /**
     * Clone this instance.
     *
     * @return PHPDateInterval A DateInterval instance clone.
     */
    public function __clone() {
        return $this->copy();
    }

    /**
     * Get a date interval property.
     *
     * @param string $name The name of the property to get.
     *
     * @return int The value of the getter.
     *
     * @throws InvalidArgumentException Throws an exception on failure.
     */
    public function __get($name) {
        // Get the number of years
        if(StringUtils::equals($name, 'intervalYears', true))
            return $this->getYears();

        // Get the number of months
        if(StringUtils::equals($name, 'intervalMonths', true))
            return $this->getMonths();

        // Get the number of full weeks
        if(StringUtils::equals($name, 'intervalWeeks', true))
            return $this->getWeeks();

        // Get the number of days excluding weeks
        if(StringUtils::equals($name, 'intervalDaysExcludeWeeks', true))
            return $this->getDaysExcludeWeeks();

        // Get the number of days
        if(StringUtils::equals($name, 'intervalDays', true))
            return $this->getDays();

        // Get the number of hours
        if(StringUtils::equals($name, 'intervalHours', true))
            return $this->getHours();

        // Get the number of minutes
        if(StringUtils::equals($name, 'intervalMinutes', true))
            return $this->getMinutes();

        // Get the number of seconds
        if(StringUtils::equals($name, 'intervalSeconds', true))
            return $this->getSeconds();

        // Unknown property getter, throw an exception
        throw new InvalidArgumentException('Unknown property getter \'' . $name . '\'');
    }

    /**
     * Set a property of the date interval object.
     *
     * @param string $name The name of the property to set.
     * @param int $value The value to set the property to.
     *
     * @return static The DateTime instance.
     *
     * @throws InvalidArgumentException Throw an exception on failure.
     */
    public function __set($name, $value) {
        // Set the number of years
        if(StringUtils::equals($name, 'intervalYears', true))
            return $this->setYears($value);

        // Set the number of months
        if(StringUtils::equals($name, 'intervalMonths', true))
            return $this->setMonths($value);

        // Set the number of weeks
        if(StringUtils::equals($name, 'intervalWeeks', true))
            return $this->setWeeks($value);

        // Set the number of days
        if(StringUtils::equals($name, 'intervalDays', true))
            return $this->setDays($value);

        // Set the number of hours
        if(StringUtils::equals($name, 'intervalHours', true))
            return $this->setHours($value);

        // Set the number of minutes
        if(StringUtils::equals($name, 'intervalMinutes', true))
            return $this->setMInutes($value);

        // Set the number of seconds
        if(StringUtils::equals($name, 'intervalSeconds', true))
            return $this->setSeconds($value);

        // Unknown property setter, throw an exception
        throw new InvalidArgumentException('Unknown property setter \'' . $name . '\'');
    }

    /**
     * Add the given number of years, months, weeks, days, hours, minutes and seconds to the date interval.
     * The resulting values may not be a negative number, or an exception will be thrown.
     *
     * @param int|null $years The number of years to add to the interval. Null to ignore this value.
     * @param int|null $months The number of months to add to the interval. Null to ignore this value.
     * @param int|null $weeks The number of weeks to add to the interval, this is converted into and added to the
     *     number of days. Null to ignore this value.
     * @param int|null $days The number of days to add to the interval. Null to ignore this value.
     * @param int|null $hours The number of hours to add to the interval. Null to ignore this value.
     * @param int|null $minutes The number of minutes to add to the interval. Null to ignore this value.
     * @param int|null $seconds The number of seconds to add to the interval. Null to ignore this value.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addDateTime($years = null, $months = null, $weeks = null, $days = null, $hours = null,
                                $minutes = null, $seconds = null) {
        // Add the date and time part
        $this->addDate($years, $months, $weeks, $days);
        $this->addTime($hours, $minutes, $seconds);

        // Return this instance
        return $this;
    }

    /**
     * Subtract the given number of years, months, weeks, days, hours, minutes and seconds to the date interval.
     * The resulting values may not be a negative number, or an exception will be thrown.
     *
     * @param int|null $years The number of years to subtract to the interval. Null to ignore this value.
     * @param int|null $months The number of months to subtract to the interval. Null to ignore this value.
     * @param int|null $weeks The number of weeks to subtract to the interval, this is converted into and added to the
     *     number of days. Null to ignore this value.
     * @param int|null $days The number of days to subtract to the interval. Null to ignore this value.
     * @param int|null $hours The number of hours to subtract to the interval. Null to ignore this value.
     * @param int|null $minutes The number of minutes to subtract to the interval. Null to ignore this value.
     * @param int|null $seconds The number of seconds to subtract to the interval. Null to ignore this value.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subDateTime($years = null, $months = null, $weeks = null, $days = null, $hours = null,
                                $minutes = null, $seconds = null) {
        // Subtract the date and time part
        $this->subDate($years, $months, $weeks, $days);
        $this->subTime($hours, $minutes, $seconds);

        // Return this instance
        return $this;
    }

    /**
     * Set the time part of the interval. This changes the specified hours, minutes and seconds.
     *
     * @param int|null $years The number of years of the interval. Null to ignore this value.
     * @param int|null $months The number of months of the interval. Null to ignore this value.
     * @param int|null $weeks The number of weeks of the interval, this is converted into and added to the number of
     *     days. Null to ignore this value.
     * @param int|null $days The number of days of the interval. Null to ignore this value.
     * @param int|null $hours The number of hours of the interval. Null to ignore this value.
     * @param int|null $minutes The number of minutes of the interval. Null to ignore this value.
     * @param int|null $seconds The number of seconds of the interval. Null to ignore this value.
     *
     * @return static This DateInterval instance for method chaining.
     */
    public function setDateTime($years = null, $months = null, $weeks = null, $days = null, $hours = null,
                                $minutes = null, $seconds = null) {
        // Set the date and time part
        $this->setDate($years, $months, $weeks, $days);
        $this->setTime($hours, $minutes, $seconds);

        // Return this instance
        return $this;
    }

    /**
     * Add the given number of years, months, weeks and days to the date interval.
     * The resulting values may not be a negative number, or an exception will be thrown.
     *
     * @param int|null $years The number of years to add to the interval. Null to ignore this value.
     * @param int|null $months The number of months to add to the interval. Null to ignore this value.
     * @param int|null $weeks The number of weeks to add to the interval, this is converted into and added to the
     *     number of days. Null to ignore this value.
     * @param int|null $days The number of days to add to the interval. Null to ignore this value.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addDate($years = null, $months = null, $weeks = null, $days = null) {
        // Add the number of years and months if set
        if(!empty($years))
            $this->addYears($years);
        if(!empty($months))
            $this->addMonths($months);

        // Add the number of weeks and days
        $this->addWeeksAndDays($weeks, $days);

        // Return this instance for method chaining
        return $this;
    }

    /**
     * Subtract the given number of years, months, weeks and days to the date interval.
     * The resulting values may not be a negative number, or an exception will be thrown.
     *
     * @param int|null $years The number of years to subtract of the interval. Null to ignore this value.
     * @param int|null $months The number of months to subtract of the interval. Null to ignore this value.
     * @param int|null $weeks The number of weeks to subtract of the interval, this is converted into and added to the
     *     number of days. Null to ignore this value.
     * @param int|null $days The number of days to subtract of the interval. Null to ignore this value.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subDate($years = null, $months = null, $weeks = null, $days = null) {
        // Subtract the number of years and months if set
        if(!empty($years))
            $this->subYears($years);
        if(!empty($months))
            $this->subMonths($months);

        // Subtract the number of weeks and days
        $this->subWeeksAndDays($weeks, $days);

        // Return this instance for method chaining
        return $this;
    }

    /**
     * Set the date part of the interval. This changes the specified years, months and days.
     *
     * @param int|null $years The number of years of the interval. Null to ignore this value.
     * @param int|null $months The number of months of the interval. Null to ignore this value.
     * @param int|null $weeks The number of weeks of the interval, this is converted into and added to the number of
     *     days. Null to ignore this value.
     * @param int|null $days The number of days of the interval. Null to ignore this value.
     *
     * @return static This DateInterval instance for method chaining.
     */
    public function setDate($years = null, $months = null, $weeks = null, $days = null) {
        // Set the years and months if set
        if(!empty($years))
            $this->setYears($years);
        if(!empty($months))
            $this->setMonths($months);

        // Set the days
        $this->setWeeksAndDays($weeks, $days);

        // Return this instance
        return $this;
    }

    /**
     * Get the number of years.
     *
     * @return int Number of years.
     */
    public function getYears() {
        return $this->y;
    }

    /**
     * Add the given number of years to the date interval.
     * The resulting number of years may not be a negative number, or an exception will be thrown.
     *
     * @param int $years [optional] The number of years to add.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addYears($years = 1) {
        // Make sure the years parameter is an integer
        if(!is_int($years))
            throw new DomainException('Invalid value for years, must be an integer (\'' . $years . '\' was given)');

        // Calculate the new number of years
        $years = $this->getYears() + intval($years);

        // Make sure the number isn't negative
        if($years < 0)
            throw new Exception('The resulting number of years may not be negative (' . $years . ' is negative)');

        // Set the number of years
        $this->setYears($years);

        // Return this instance for method chaining
        return $this;
    }

    /**
     * Add one, or the given number of years to the date interval.
     * The resulting number of years may not be a negative number, or an exception will be thrown.
     *
     * @param int $years [optional] The number of years to add.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addYear($years = 1) {
        return $this->addYears($years);
    }

    /**
     * Subtract the given number of years to the date interval.
     * The resulting number of years may not be a negative number, or an exception will be thrown.
     *
     * @param int $years [optional] The number of years to subtract.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subYears($years = 1) {
        // Make sure the years parameter is an integer
        if(!is_int($years))
            throw new \DomainException('Invalid value for years, must be an integer (\'' . $years . '\' was given)');

        // Subtract the number of years, return the result
        return $this->addYears($years * -1);
    }

    /**
     * Subtract one, or the given number of years to the date interval.
     * The resulting number of years may not be a negative number, or an exception will be thrown.
     *
     * @param int $years [optional] The number of years to subtract.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subYear($years = 1) {
        return $this->subYears($years);
    }

    /**
     * Set the number of years.
     *
     * @param int $years The number of years, must be zero or a positive number.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws \DomainException Throws an exception on failure.
     */
    public function setYears($years) {
        // Make sure the value is a positive number or zero
        if(!is_int($years) || $years < 0)
            throw new \DomainException('Invalid value for years, must be zero or a positive number (\'' . $years .
                '\' was given)');

        // Set the years, return this instance
        $this->y = $years;

        return $this;
    }

    /**
     * Get the number of months.
     *
     * @return int Number of months.
     */
    public function getMonths() {
        return $this->m;
    }

    /**
     * Add the given number of months to the date interval.
     * The resulting number of months may not be a negative number, or an exception will be thrown.
     *
     * @param int $months [optional] The number of months to add.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addMonths($months = 1) {
        // Make sure the months parameter is an integer
        if(!is_int($months))
            throw new DomainException('Invalid value for months, must be an integer (\'' . $months . '\' was given)');

        // Calculate the new number of months
        $months = $this->getMonths() + intval($months);

        // Make sure the number isn't negative
        if($months < 0)
            throw new Exception('The resulting number of months may not be negative (' . $months . ' is negative)');

        // Set the number of months
        $this->setMonths($months);

        // Return this instance for method chaining
        return $this;
    }

    /**
     * Add one, or the given number of months to the date interval.
     * The resulting number of months may not be a negative number, or an exception will be thrown.
     *
     * @param int $months [optional] The number of months to add.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addMonth($months = 1) {
        return $this->addMonths($months);
    }

    /**
     * Subtract the given number of months to the date interval.
     * The resulting number of months may not be a negative number, or an exception will be thrown.
     *
     * @param int $months [optional] The number of months to subtract.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subMonths($months = 1) {
        // Make sure the months parameter is an integer
        if(!is_int($months))
            throw new DomainException('Invalid value for months, must be an integer (\'' . $months . '\' was given)');

        // Subtract the number of months, return the result
        return $this->addMonths($months * -1);
    }

    /**
     * Subtract one, or the given number of months to the date interval.
     * The resulting number of months may not be a negative number, or an exception will be thrown.
     *
     * @param int $months [optional] The number of months to subtract.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subMonth($months = 1) {
        return $this->subMonths($months);
    }

    /**
     * Set the number of months.
     *
     * @param int $months The number of months, must be zero or a positive number.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws \DomainException Throws an exception on failure.
     */
    public function setMonths($months) {
        // Make sure the value is a positive number or zero
        if(!is_int($months) || $months < 0)
            throw new DomainException('Invalid value for months, must be zero or a positive number (\'' . $months .
                '\' was given)');

        // Set the months, return this instance
        $this->m = $months;

        return $this;
    }

    /**
     * Add the given number of weeks and days to the date interval.
     * The resulting number of days may not be a negative number, or an exception will be thrown.
     *
     * @param int|null $weeks [optional] The number of weeks to add. Null to ignore this value.
     * @param int|null $days [optional] The number of days to add. Null to ignore this value.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addWeeksAndDays($weeks = null, $days = null) {
        // Calculate the total number of days
        $totalDays = 0;

        // Append the number of weeks if set
        if(is_int($weeks))
            $totalDays += intval($weeks) * DateTime::DAYS_PER_WEEK;

        else if(!empty($weeks))
            throw new DomainException('Invalid value for weeks (\'' . $weeks . '\' was given)');

        // Append the number of days if set
        if(is_int($days))
            $totalDays += intval($days);

        else if(!empty($days))
            throw new DomainException('Invalid value for days (\'' . $days . '\' was given)');

        // Set the number of days
        if(!empty($totalDays)) {
            // Make sure the number of days isn't negative
            if(($this->getDays() + $totalDays) < 0)
                throw new DomainException('The resulting number of days may not be below zero (got ' . $totalDays .
                    ' days)');

            // Add the number of days
            $this->addDays($totalDays);
        }

        // Return this instance
        return $this;
    }

    /**
     * Subtract the given number of weeks and days to the date interval.
     * The resulting number of days may not be a negative number, or an exception will be thrown.
     *
     * @param int|null $weeks [optional] The number of weeks to subtract. Null to ignore this value.
     * @param int|null $days [optional] The number of days to subtract. Null to ignore this value.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subWeeksAndDays($weeks = null, $days = null) {
        // Calculate the total number of days
        $totalDays = 0;

        // Append the number of weeks if set
        if(is_int($weeks))
            $totalDays += intval($weeks) * DateTime::DAYS_PER_WEEK;

        else if(!empty($weeks))
            throw new DomainException('Invalid value for weeks (\'' . $weeks . '\' was given)');

        // Append the number of days if set
        if(is_int($days))
            $totalDays += intval($days);

        else if(!empty($days))
            throw new DomainException('Invalid value for days (\'' . $days . '\' was given)');

        // Set the number of days
        if(!empty($totalDays)) {
            // Make sure the number of days isn't negative
            if(($this->getDays() + $totalDays) < 0)
                throw new DomainException('The resulting number of days may not be below zero (got ' . $totalDays .
                    ' days)');

            // Subtract the number of days
            $this->subDays($totalDays);
        }

        // Return this instance
        return $this;
    }

    /**
     * Set the number of days based ont he given number of weeks and days.
     *
     * @param int|null $weeks The number of weeks of the interval, this is converted into and added to the number of
     *     days. Null to ignore this value.
     * @param int|null $days The number of days of the interval. Null to ignore this value.
     *
     * @return static This DateTime instance for method chaining.
     */
    public function setWeeksAndDays($weeks = null, $days = null) {
        // Calculate the total number of days
        $totalDays = 0;

        // Append the number of weeks if set
        if(is_int($weeks))
            $totalDays += intval($weeks) * DateTime::DAYS_PER_WEEK;

        else if(!empty($weeks))
            throw new DomainException('Invalid value for weeks (\'' . $weeks . '\' was given)');

        // Append the number of days if set
        if(is_int($days))
            $totalDays += intval($days);

        else if(!empty($days))
            throw new DomainException('Invalid value for days (\'' . $days . '\' was given)');

        // Set the number of days
        if(!empty($totalDays)) {
            // Make sure the number of days isn't negative
            if($totalDays < 0)
                throw new DomainException('The total number of days may not be below zero (' . $totalDays .
                    ' total days)');

            // Set the number of days
            $this->setDays($totalDays);
        }

        // Return this instance
        return $this;
    }

    /**
     * Get the number of of full weeks based on the number of days. This value may not be exact because it's being
     * round down.
     *
     * @return int The number of full weeks.
     */
    public function getWeeks() {
        return (int) floor($this->getDays() / DateTime::DAYS_PER_WEEK);
    }

    /**
     * Get the number of days excluding the weeks. The total days remaining in the final week of the current instance
     * (days % 7).
     *
     * @return int The number of days excluding the weeks.
     */
    public function getDaysExcludeWeeks() {
        return (int) ($this->getDays() % DateTime::DAYS_PER_WEEK);
    }

    /**
     * Add the given number of days to the date interval specified as weeks.
     * The resulting number of days may not be a negative number, or an exception will be thrown.
     *
     * @param int $weeks [optional] The number of weeks to add.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addWeeks($weeks = 1) {
        // Make sure the weeks parameter is an integer
        if(!is_int($weeks))
            throw new DomainException('Invalid value for weeks, must be an integer (\'' . $weeks . '\' was given)');

        // Calculate the new number of days
        $days = $this->getDays() + (intval($weeks) * DateTime::DAYS_PER_WEEK);

        // Make sure the number isn't negative
        if($days < 0)
            throw new Exception('The resulting number of days may not be negative (' . $days . ' is negative)');

        // Set the number of days
        $this->setDays($weeks);

        // Return this instance for method chaining
        return $this;
    }

    /**
     * Add one, or the given number of days to the date interval specified as weeks.
     * The resulting number of days may not be a negative number, or an exception will be thrown.
     *
     * @param int $weeks [optional] The number of weeks to add.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addWeek($weeks = 1) {
        return $this->addWeeks($weeks);
    }

    /**
     * Subtract the given number of days to the date interval specified as weeks.
     * The resulting number of days may not be a negative number, or an exception will be thrown.
     *
     * @param int $weeks [optional] The number of weeks to subtract.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subWeeks($weeks = 1) {
        // Make sure the weeks parameter is an integer
        if(!is_int($weeks))
            throw new DomainException('Invalid value for weeks, must be an integer (\'' . $weeks . '\' was given)');

        // Subtract the number of weeks, return the result
        return $this->addWeeks($weeks * -1);
    }

    /**
     * Subtract one, or the given number of days to the date interval specified as weeks.
     * The resulting number of days may not be a negative number, or an exception will be thrown.
     *
     * @param int $weeks [optional] The number of weeks to subtract.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subWeek($weeks = 1) {
        return $this->subWeeks($weeks);
    }

    /**
     * Set the number of days based on the given number of weeks.
     * Note: This will change the number of days.
     *
     * @param int $weeks The number of weeks, must be zero or a positive number.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws \DomainException Throws an exception on failure.
     */
    public function setWeeks($weeks) {
        // Make sure the value is a positive number or zero
        if(!is_int($weeks) || $weeks < 0)
            throw new DomainException('Invalid value for months, must be zero or a positive number (\'' . $weeks .
                '\' was given)');

        // Set the days, return this instance
        $this->d = $weeks * DateTime::DAYS_PER_WEEK;

        return $this;
    }

    /**
     * Get the number of days.
     *
     * @return int Number of days.
     */
    public function getDays() {
        return $this->d;
    }

    /**
     * Add the given number of days to the date interval.
     * The resulting number of days may not be a negative number, or an exception will be thrown.
     *
     * @param int $days [optional] The number of days to add.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addDays($days = 1) {
        // Make sure the days parameter is an integer
        if(!is_int($days))
            throw new DomainException('Invalid value for days, must be an integer (\'' . $days . '\' was given)');

        // Calculate the new number of days
        $days = $this->getDays() + intval($days);

        // Make sure the number isn't negative
        if($days < 0)
            throw new Exception('The resulting number of days may not be negative (' . $days . ' is negative)');

        // Set the number of days
        $this->setDays($days);

        // Return this instance for method chaining
        return $this;
    }

    /**
     * Add one, or the given number of days to the date interval.
     * The resulting number of days may not be a negative number, or an exception will be thrown.
     *
     * @param int $days [optional] The number of days to add.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addDay($days = 1) {
        return $this->addDays($days);
    }

    /**
     * Subtract the given number of days to the date interval.
     * The resulting number of days may not be a negative number, or an exception will be thrown.
     *
     * @param int $days [optional] The number of days to subtract.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subDays($days = 1) {
        // Make sure the days parameter is an integer
        if(!is_int($days))
            throw new DomainException('Invalid value for days, must be an integer (\'' . $days . '\' was given)');

        // Subtract the number of days, return the result
        return $this->addDays($days * -1);
    }

    /**
     * Subtract one, or the given number of days to the date interval.
     * The resulting number of days may not be a negative number, or an exception will be thrown.
     *
     * @param int $days [optional] The number of days to subtract.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subDay($days = 1) {
        return $this->subDays($days);
    }

    /**
     * Set the number of days.
     *
     * @param int $days The number of days, must be zero or a positive number.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException Throws an exception on failure.
     */
    public function setDays($days) {
        // Make sure the value is a positive number or zero
        if(!is_int($days) || $days < 0)
            throw new DomainException('Invalid value for days, must be zero or a positive number (\'' . $days .
                '\' was given)');

        // Set the days, return this instance
        $this->d = $days;

        return $this;
    }

    /**
     * Add the given number of hours, minutes and seconds to the date interval.
     * The resulting values may not be a negative number, or an exception will be thrown.
     *
     * @param int|null $hours The number of hours to add to the interval. Null to ignore this value.
     * @param int|null $minutes The number of minutes to add to the interval. Null to ignore this value.
     * @param int|null $seconds The number of seconds to add to the interval. Null to ignore this value.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addTime($hours = null, $minutes = null, $seconds = null) {
        // Add the number of hours, minutes and seconds if set
        if(!empty($hours))
            $this->addHours($hours);
        if(!empty($minutes))
            $this->addMinutes($minutes);
        if(!empty($seconds))
            $this->addSecond($seconds);

        // Return this instance for method chaining
        return $this;
    }

    /**
     * Subtract the given number of hours, minutes and seconds to the date interval.
     * The resulting values may not be a negative number, or an exception will be thrown.
     *
     * @param int|null $hours The number of hours to subtract to the interval. Null to ignore this value.
     * @param int|null $minutes The number of minutes to subtract to the interval. Null to ignore this value.
     * @param int|null $seconds The number of seconds to subtract to the interval. Null to ignore this value.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subTime($hours = null, $minutes = null, $seconds = null) {
        // Subtract the number of hours, minutes and seconds if set
        if(!empty($hours))
            $this->subHours($hours);
        if(!empty($minutes))
            $this->subMinutes($minutes);
        if(!empty($seconds))
            $this->subSecond($seconds);

        // Return this instance for method chaining
        return $this;
    }

    /**
     * Set the date and time part of the interval. This changes the specified years, months, days, hours, minutes and
     * seconds.
     *
     * @param int|null $hours The number of hours of the interval. Null to ignore this value.
     * @param int|null $minutes The number of minutes of the interval. Null to ignore this value.
     * @param int|null $seconds The number of seconds of the interval. Null to ignore this value.
     *
     * @return static This DateInterval instance for method chaining.
     */
    public function setTime($hours = null, $minutes = null, $seconds = null) {
        // Set the hours, minutes and seconds
        if(!empty($hours))
            $this->setHours($hours);
        if(!empty($minutes))
            $this->setMinutes($minutes);
        if(!empty($seconds))
            $this->setSeconds($seconds);

        // Return this instance
        return $this;
    }

    /**
     * Get the number of hours.
     *
     * @return int Number of hours.
     */
    public function getHours() {
        return $this->h;
    }

    /**
     * Add the given number of hours to the date interval.
     * The resulting number of hours may not be a negative number, or an exception will be thrown.
     *
     * @param int $hours [optional] The number of hours to add.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addHours($hours = 1) {
        // Make sure the hours parameter is an integer
        if(!is_int($hours))
            throw new DomainException('Invalid value for hours, must be an integer (\'' . $hours . '\' was given)');

        // Calculate the new number of hours
        $hours = $this->getHours() + intval($hours);

        // Make sure the number isn't negative
        if($hours < 0)
            throw new Exception('The resulting number of hours may not be negative (' . $hours . ' is negative)');

        // Set the number of hours
        $this->setHours($hours);

        // Return this instance for method chaining
        return $this;
    }

    /**
     * Add one, or the given number of hours to the date interval.
     * The resulting number of hours may not be a negative number, or an exception will be thrown.
     *
     * @param int $hours [optional] The number of hours to add.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addHour($hours = 1) {
        return $this->addHours($hours);
    }

    /**
     * Subtract the given number of hours to the date interval.
     * The resulting number of hours may not be a negative number, or an exception will be thrown.
     *
     * @param int $hours [optional] The number of hours to subtract.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subHours($hours = 1) {
        // Make sure the hours parameter is an integer
        if(!is_int($hours))
            throw new DomainException('Invalid value for hours, must be an integer (\'' . $hours . '\' was given)');

        // Subtract the number of hours, return the result
        return $this->addHours($hours * -1);
    }

    /**
     * Subtract one, or the given number of hours to the date interval.
     * The resulting number of hours may not be a negative number, or an exception will be thrown.
     *
     * @param int $hours [optional] The number of hours to subtract.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subHour($hours = 1) {
        return $this->subHours($hours);
    }

    /**
     * Set the number of hours.
     *
     * @param int $hours The number of hours, must be zero or a positive number.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws \DomainException Throws an exception on failure.
     */
    public function setHours($hours) {
        // Make sure the value is a positive number or zero
        if(!is_int($hours) || $hours < 0)
            throw new DomainException('Invalid value for hours, must be zero or a positive number (\'' . $hours .
                '\' was given)');

        // Set the hours, return this instance
        $this->h = $hours;

        return $this;
    }

    /**
     * Get the number of minutes.
     *
     * @return int Number of minutes.
     */
    public function getMinutes() {
        return $this->i;
    }

    /**
     * Add the given number of minutes to the date interval.
     * The resulting number of minutes may not be a negative number, or an exception will be thrown.
     *
     * @param int $minutes [optional] The number of minutes to add.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addMinutes($minutes = 1) {
        // Make sure the minutes parameter is an integer
        if(!is_int($minutes))
            throw new DomainException('Invalid value for minutes, must be an integer (\'' . $minutes . '\' was given)');

        // Calculate the new number of minutes
        $minutes = $this->getMinutes() + intval($minutes);

        // Make sure the number isn't negative
        if($minutes < 0)
            throw new Exception('The resulting number of minutes may not be negative (' . $minutes . ' is negative)');

        // Set the number of minutes
        $this->setMinutes($minutes);

        // Return this instance for method chaining
        return $this;
    }

    /**
     * Add one, or the given number of minutes to the date interval.
     * The resulting number of minutes may not be a negative number, or an exception will be thrown.
     *
     * @param int $minutes [optional] The number of minutes to add.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addMinute($minutes = 1) {
        return $this->addMinutes($minutes);
    }

    /**
     * Subtract the given number of minutes to the date interval.
     * The resulting number of minutes may not be a negative number, or an exception will be thrown.
     *
     * @param int $minutes [optional] The number of minutes to subtract.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subMinutes($minutes = 1) {
        // Make sure the minutes parameter is an integer
        if(!is_int($minutes))
            throw new DomainException('Invalid value for minutes, must be an integer (\'' . $minutes .
                '\' was given)');

        // Subtract the number of minutes, return the result
        return $this->addMinutes($minutes * -1);
    }

    /**
     * Subtract one, or the given number of minutes to the date interval.
     * The resulting number of minutes may not be a negative number, or an exception will be thrown.
     *
     * @param int $minutes [optional] The number of minutes to subtract.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subMinute($minutes = 1) {
        return $this->subMinutes($minutes);
    }

    /**
     * Set the number of minutes.
     *
     * @param int $minutes The number of minutes, must be zero or a positive number.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws \DomainException Throws an exception on failure.
     */
    public function setMinutes($minutes) {
        // Make sure the value is a positive number or zero
        if(!is_int($minutes) || $minutes < 0)
            throw new DomainException('Invalid value for minutes, must be zero or a positive number (\'' . $minutes .
                '\' was given)');

        // Set the minutes, return this instance
        $this->i = $minutes;

        return $this;
    }

    /**
     * Get the number of seconds.
     *
     * @return int Number of seconds.
     */
    public function getSeconds() {
        return $this->s;
    }

    /**
     * Add the given number of seconds to the date interval.
     * The resulting number of seconds may not be a negative number, or an exception will be thrown.
     *
     * @param int $seconds [optional] The number of seconds to add.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addSeconds($seconds = 1) {
        // Make sure the seconds parameter is an integer
        if(!is_int($seconds))
            throw new DomainException('Invalid value for seconds, must be an integer (\'' . $seconds . '\' was given)');

        // Calculate the new number of seconds
        $seconds = $this->getSeconds() + intval($seconds);

        // Make sure the number isn't negative
        if($seconds < 0)
            throw new Exception('The resulting number of seconds may not be negative (' . $seconds . ' is negative)');

        // Set the number of seconds
        $this->setSeconds($seconds);

        // Return this instance for method chaining
        return $this;
    }

    /**
     * Add one, or the given number of seconds to the date interval.
     * The resulting number of seconds may not be a negative number, or an exception will be thrown.
     *
     * @param int $seconds [optional] The number of seconds to add.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function addSecond($seconds = 1) {
        return $this->addSeconds($seconds);
    }

    /**
     * Subtract the given number of seconds to the date interval.
     * The resulting number of seconds may not be a negative number, or an exception will be thrown.
     *
     * @param int $seconds [optional] The number of seconds to subtract.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subSeconds($seconds = 1) {
        // Make sure the seconds parameter is an integer
        if(!is_int($seconds))
            throw new DomainException('Invalid value for seconds, must be an integer (\'' . $seconds .
                '\' was given)');

        // Subtract the number of seconds, return the result
        return $this->addSeconds($seconds * -1);
    }

    /**
     * Subtract one, or the given number of seconds to the date interval.
     * The resulting number of seconds may not be a negative number, or an exception will be thrown.
     *
     * @param int $seconds [optional] The number of seconds to subtract.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws DomainException|Exception Throws an exception on failure.
     */
    public function subSecond($seconds = 1) {
        return $this->subSeconds($seconds);
    }

    /**
     * Set the number of seconds.
     *
     * @param int $seconds The number of seconds, must be zero or a positive number.
     *
     * @return static The DateTime instance for method chaining.
     *
     * @throws \DomainException Throws an exception on failure.
     */
    public function setSeconds($seconds) {
        // Make sure the value is a positive number or zero
        if(!is_int($seconds) || $seconds < 0)
            throw new DomainException('Invalid value for seconds, must be zero or a positive number (\'' . $seconds .
                '\' was given)');

        // Set the seconds, return this instance
        $this->s = $seconds;

        return $this;
    }

    /**
     * Check whether the date interval is inverted.
     *
     * @return bool True if the date interval is inverted, false if not.
     */
    public function isInverted() {
        return $this->invert !== 0;
    }

    /**
     * Set whether the date interval is inverted.
     *
     * @param int|bool $inverted One or true if the date interval is inverted, zero or false if not.
     *
     * @return static This DateInterval instance.
     */
    public function setInverted($inverted) {
        // Parse the parameter value
        $inverted = empty($inverted) ? 0 : 1;

        // Set whether the interval is inverted, return this instance
        $this->invert = $inverted;

        return $this;
    }

    /**
     * Get the day span of this interval. This only works for objects created with DateTime::diff().
     *
     * @return int|null The day span of this interval, or null on failure.
     */
    public function getDaySpan() {
        return $this->isCreatedFromDiff() ? $this->days : null;
    }

    /**
     * Check whether this is a zero date interval.
     *
     * @return bool True if this date interval is zero, false if not.
     */
    public function isZero() {
        return ($this->getYears() == 0 && $this->getMonths() == 0 && $this->getDays() == 0 &&
            $this->getHours() == 0 && $this->getMinutes() == 0 && $this->getSeconds() == 0);
    }

    /**
     * Check whether this date time object was created using DateTime::diff() or PHPDateTime::diff().
     *
     * @return bool True if this date interval object was created by a diff() method, false if not. If the date
     *     interval isn't an instance of DateInterval false will also be returned.
     */
    public function isCreatedFromDiff() {
        return DateIntervalUtils::isCreatedFromDiff($this);
    }

    /**
     * Add the passed interval of the current instance
     *
     * @param PHPDateInterval|PHPDateInterval|string|null $dateInterval The DateInterval or PHPDateInterval instance,
     *     the date interval specification as a string or null to use a zero specification.
     *
     * @return static|null This DateInterval instance for method chaining, or null on failure.
     */
    public function add($dateInterval) {
        // Parse the date interval, return null on failure
        if(($dateInterval = static::parse($dateInterval)) === null)
            return null;

        // Calculate the factor to multiply each value with
        $factor = $dateInterval->isInverted() ? -1 : 1;

        /*if(DateIntervalUtils::isCreatedFromDiff($dateInterval))
            $this->setDays($this->getDays() + ($dateInterval->getDaySpan() * $factor));
        else*/

        // Add the date and time
        $this->addDateTime($dateInterval->getYears() * $factor, $dateInterval->getMonths() * $factor, null,
            $dateInterval->getDays() * $factor, $dateInterval->getHours() * $factor,
            $dateInterval->getMinutes() * $factor, $dateInterval->getSeconds() * $factor);

        // Return this instance
        return $this;
    }

    /**
     * Subtract the passed interval of the current instance.
     *
     * @param PHPDateInterval|PHPDateInterval|string|null $dateInterval The DateInterval or PHPDateInterval instance,
     *     the date interval specification as a string or null to use a zero specification.
     *
     * @return static|null This DateInterval instance for method chaining, or null on failure.
     */
    public function sub($dateInterval) {
        // Parse the date interval as a new instance, return null on failure
        if(($dateInterval = static::instance($dateInterval)) === null)
            return null;

        // Invert the date interval
        $dateInterval->setInverted(!$dateInterval->isInverted());

        // Subtract and return the result
        return $this->add($dateInterval);
    }

    /**
     * Formats the date interval into a string.
     *
     * @param string|null $format [optional] The format to use specified by PHPDateInterval::diff();, or null to return
     *     the date interval specification.
     *
     * @return string The formatted date interval.
     *
     * @link http://php.net/manual/en/dateinterval.format.php
     */
    public function format($format = null) {
        // Return the specification if the parameter is null
        if($format === null)
            return $this->toSpecString();

        // Get and return the date interval in proper format
        return parent::format($format);
    }

    /**
     * Create a ISO-8601 date interval specification string.
     *
     * @return string The date interval specification string.
     */
    public function toSpecString() {
        return DateIntervalSpec::create($this->getYears(), $this->getMonths(), null, $this->getDays(),
            $this->getHours(), $this->getMinutes(), $this->getSeconds());
    }

    /**
     * Get the date interval as a string.
     *
     * @return string The date interval string.
     */
    public function toString() {
        return $this->toSpecString();
    }

    /**
     * Get the date interval as a string.
     *
     * @return string The date interval string.
     */
    public function __toString() {
        return ($result = $this->toString()) === null ? '' : $result;
    }
}