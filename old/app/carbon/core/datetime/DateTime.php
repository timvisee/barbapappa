<?php

/**
 * DateTime.php
 *
 * A class to representation date and time as an object.
 * This class allows you to get the current date and time of the server, to format the date and time in many different
 * ways using different timezones and also to travel through time.
 * Note: Even though this class uses futuristic technology to make date and time calculations, it doesn't allow humans
 * to travel through time.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Carbon CMS 2015. All rights reserved.
 */

namespace carbon\core\datetime;

use carbon\core\datetime\interval\DateInterval;
use carbon\core\datetime\period\DatePeriod;
use carbon\core\datetime\zone\DateTimeZone;
use carbon\core\datetime\zone\DateTimeZoneUtils;
use carbon\core\exception\datetime\interval\InvalidDateIntervalException;
use carbon\core\exception\datetime\InvalidDateException;
use carbon\core\exception\datetime\InvalidDateTimeException;
use carbon\core\exception\datetime\InvalidTimeException;
use carbon\core\exception\datetime\InvalidTimestampException;
use carbon\core\exception\datetime\zone\InvalidDateTimeZoneException;
use carbon\core\util\StringUtils;
use Closure;
use DateInterval as PHPDateInterval;
use DateTime as PHPDateTime;
use DateTimeZone as PHPDateTimeZone;
use DomainException;
use InvalidArgumentException;

// Prevent direct requests to this file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * A class to represent, format and transform date and time.
 *
 * Used the Carbon DateTime library as basis for this class.
 *
 * @package carbon\core\datetime
 *
 * @TODO: Update these comments below!
 * Note: It's not recommended to use these class properties.
 *
 * @property int $year The year.
 * @property int $yearIso The ISO year.
 * @property int $month The month.
 * @property int $day The day.
 * @property int $hour The hour.
 * @property int $minute The minute.
 * @property int $second The second.
 * @property int $timestamp seconds since the Unix Epoch
 * @property DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $timezone A DateTimeZone or PHPDateTimeZone
 *     instance, the timezone ID as a string, a DateTime or PHPDateTime instance to use it's timezone or null to use
 *     the default timezone.
 * @property-read int $micro Read the number of micro seconds.
 * @property-read int $dayOfWeek Get the day of the week as a number, 0 (for Sunday) through 6 (for Saturday).
 * @property-read int $dayOfYear Get the day of the year, 0 through 365.
 * @property-read int $weekOfMonth Get the week of the month, 1 through 5.
 * @property-read int $weekOfYear Get the ISO-8601 week number of year, weeks starting on Monday.
 * @property-read int $daysInMonth Get the number of days in the given month.
 * @property-read int $age Get the number of years passed since now().
 * @property-read int $quarter Get the the quarter of this instance, from 1 to 4.
 * @property-read int $offset Get the timezone offset in seconds from UTC.
 * @property-read int $offsetHours Get the timezone offset in hours from UTC.
 * @property-read bool $dst Check whether daylight saving time is enabled, true if DST, false otherwise.
 * @property-read bool $local Check whether the timezone is local, true if local, false otherwise.
 * @property-read bool $utc Check whether the timezone is UTC, true if UTC, false otherwise.
 * @property-read string $timezoneName Get the timezone name or ID.
 */
class DateTime extends PHPDateTime {

    // TODO: Support for all types of calendars!
    // TODO: Add the possibility to extend this class, with some functions, closures and so on.
    // TODO: Add proper exceptions for everything!
    // TODO: Better exceptions, possibly more general exceptions, not an infinite number of extended ones!
    // TODO: Put all translation and to string stuff here...!
    // TODO: Should we add the diffForHumans(); method from Carbon DateTime, and their related methods?
    // TODO: To string for humans, also update the toString method!

    /**
     * The day constant for sunday, this defines an integer for this week day.
     *
     * @const int Day index.
     */
    const SUNDAY = 0;
    /**
     * The day constant for monday, this defines an integer for this week day.
     *
     * @const int Day index.
     */
    const MONDAY = 1;
    /**
     * The day constant for tuesday, this defines an integer for this week day.
     *
     * @const int Day index.
     */
    const TUESDAY = 2;
    /**
     * The day constant for wednesday, this defines an integer for this week day.
     *
     * @const int Day index.
     */
    const WEDNESDAY = 3;
    /**
     * The day constant for thursday, this defines an integer for this week day.
     *
     * @const int Day index.
     */
    const THURSDAY = 4;
    /**
     * The day constant for friday, this defines an integer for this week day.
     *
     * @const int Day index.
     */
    const FRIDAY = 5;
    /**
     * The day constant for saturday, this defines an integer for this week day.
     *
     * @const int Day index.
     */
    const SATURDAY = 6;

    /**
     * Define the names of each weekday as an array, indexed by the day constants.
     *
     * @var Array An array containing all weekday names.
     */
    protected static $DAY_NAMES = array(
        self::SUNDAY => 'Sunday',
        self::MONDAY => 'Monday',
        self::TUESDAY => 'Tuesday',
        self::WEDNESDAY => 'Wednesday',
        self::THURSDAY => 'Thursday',
        self::FRIDAY => 'Friday',
        self::SATURDAY => 'Saturday'
    );

    /**
     * Terms used to detect if a time passed is a relative date for testing purposes.
     *
     * @var Array An array of relative keywords.
     */
    protected static $RELATIVE_KEYWORDS = Array(
        '+',
        '-',
        'this',
        'next',
        'last',
        'today',
        'tomorrow',
        'yesterday',
        'first',
        'last',
        'ago',
        'midnight',
        'noon',
        'of'
    );

    /**
     * Defines the format to use for most getter names.
     *
     * @var Array An array of getter formats.
     */
    protected static $GETTER_FORMATS = Array(
        'year' => 'Y',
        'yearIso' => 'o',
        'month' => 'n',
        'day' => 'j',
        'hour' => 'G',
        'minute' => 'i',
        'second' => 's',
        'micro' => 'u',
        'dayOfWeek' => 'w',
        'dayOfYear' => 'z',
        'weekOfYear' => 'W',
        'daysInMonth' => 't',
        'timestamp' => 'U'
    );

    /**
     * Defines the years per century, for time calculations.
     *
     * @const int Years per century.
     */
    const YEARS_PER_CENTURY = 100;
    /**
     * Defines the years per decade, for time calculations.
     *
     * @const int Years per decade.
     */
    const YEARS_PER_DECADE = 10;
    /**
     * Defines the months per year, for time calculations.
     *
     * @const int Months per year.
     */
    const MONTHS_PER_YEAR = 12;
    /**
     * Defines the weeks per year, for time calculations.
     *
     * @const int Weeks per year.
     */
    const WEEKS_PER_YEAR = 52;
    /**
     * Defines the days per week, for time calculations.
     *
     * @const int Days per week.
     */
    const DAYS_PER_WEEK = 7;
    /**
     * Defines the hours per day, for time calculations.
     *
     * @const int Hours per day.
     */
    const HOURS_PER_DAY = 24;
    /**
     * Defines the minutes per hour, for time calculations.
     *
     * @const int Minutes per hour.
     */
    const MINUTES_PER_HOUR = 60;
    /**
     * Defines the seconds per minute, for time calculations.
     *
     * @const int Seconds per minute.
     */
    const SECONDS_PER_MINUTE = 60;

    /**
     * Defines the default date format used when date and time is represented as a string.
     *
     * @const string The default date and time format.
     */
    const DEFAULT_FORMAT = 'Y-m-d H:i:s';

    /**
     * Defines the default date format used when a date is represented as a string.
     *
     * @const string The default date format.
     */
    const DEFAULT_FORMAT_DATE = 'Y-m-d';

    /**
     * Defines the default time format used when time is represented as a string.
     *
     * @const string The default time format.
     */
    const DEFAULT_FORMAT_TIME = 'H:i:s';

    /**
     * Defines the default date and time format that includes everything in the string.
     *
     * @const string The default complete date and time format.
     */
    const DEFAULT_FORMAT_COMPLETE = 'Y-m-d H:i:s.u e O';

    /**
     * An optional mock DateTime instance to return when the now() method is called.
     *
     * @var DateTime The mock DateTime instance, or null to use the default.
     */
    protected static $mockNow;

    /**
     * The preferred format to return the date and time in by default when using format and toString methods.
     *
     * @var string The preferred format.
     */
    protected static $preferredFormat = self::DEFAULT_FORMAT;

    /**
     * Constructor.
     *
     * @param string|DateTime|PHPDateTime|int|null $dateTime [optional] The date and time as a string, the date and
     *     time as DateTime or PHPDateTime instance, the UNIX timestamp as an integer, or null to use the current time.
     * @param DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime $timezone [optional] The timezone the specified
     *     time is in, or null to use the default timezone. A DateTime or PHPDateTime instance to use it's timezone.
     *
     * @throws InvalidDateTimeZoneException Throws InvalidDateTimeZoneException if the timezone is invalid.
     */
    public function __construct($dateTime = null, $timezone = null) {
        // Handle DateTime instances
        if($dateTime instanceof self) {
            // Construct the parent object with the proper properties
            parent::__construct($dateTime->toCompleteString(), $dateTime->getTimezone());
            return $this;
        }

        // Handle PHPDateTime instances
        if($dateTime instanceof parent) {
            // Construct the parent object with the proper properties
            parent::__construct($dateTime->format(self::DEFAULT_FORMAT_COMPLETE), $dateTime->getTimezone());
            return $this;
        }

        // Check whether this is a timestamp
        if(is_int($dateTime)) {
            // Parse the timezone if it isn't null and make sure it's valid
            if($timezone !== null)
                if(($timezone = DateTimeZoneUtils::parse($timezone, null)) === null)
                    throw new InvalidDateTimeZoneException('The given timezone is invalid');

            // Construct the parent object with the timestamp
            parent::__construct('@' . $dateTime, $timezone);

            // Set the proper timezone, and return this instance
            return $this->setTimezone($timezone);
        }

        // Check whether we should use the now time
        if(empty($dateTime) || StringUtils::equals($dateTime, 'now', false, true)) {
            // Return a new instance of the mock time if set, or the regular now time
            if(static::hasMockNow()) {
                // Get the mock now time
                $dateTime = static::getMockNow()->copy();

                // Shift the timezone
                if($timezone !== null)
                    $dateTime->setTimezone($timezone);

            } else
                $dateTime = 'now';
        }

        // Parse the timezone if it isn't null and make sure it's valid
        if($timezone !== null)
            if(($timezone = DateTimeZoneUtils::parse($timezone, null)) === null)
                throw new InvalidDateTimeZoneException('The given timezone is invalid');

        // Check whether the time contains relative keywords
        if(static::hasRelativeKeywords($dateTime)) {
            // Get a new DateTime instance, and modify the date and time according to the time parameter
            $dateTime = static::now($timezone)->modify($dateTime);

            // Shift the timezone if it's set
            // TODO: Should we do this, or is this done already with the 'now()' method?
            // TODO: Is this code below already done with the above statement?
            if($timezone !== null && !$timezone->equals(static::getMockNow()))
                $dateTime->setTimezone($timezone);
            else
                $timezone = $dateTime->getTimezone();

            // Update the time parameter with the modified time
            $dateTime = $dateTime->toCompleteString();
        }

        // Construct the parent object
        parent::__construct($dateTime, $timezone);
        return $this;
    }

    /**
     * Create a new DateTime instance from a specific date and time.
     *
     * If the $year, $month or $day parameters are set to null their now() value will be used.
     *
     * If $hour is null it will be set to its now() value and the default values for $minute and $second will be their
     * now() values. If $hour is not null, then the default values for $minute and $second will be 0.
     *
     * @param int $year [optional] The specified year, or null to use the current year.
     * @param int $month [optional] The specified month, or null to use the current month.
     * @param int $day [optional] The specified day, or null to use the current day.
     * @param int $hour [optional] The specified hour, or null to use the current hour.
     * @param int $minute [optional] The specified minute, or null to use the current minute if <var>$hour</var> is also null or use zero.
     * @param int $second [optional] The specified second, or null to use the current second if <var>$hour</var> is also null or use zero.
     * @param DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $timezone [optional] The preferred timezone
     *     to use, or null to use the default timezone. A DateTime or PHPDateTime instance to use it's timezone.
     *
     * @return DateTime|null The DateTime instance, or null on failure.
     */
    public static function create($year = null, $month = null, $day = null, $hour = null, $minute = null,
                                  $second = null, $timezone = null) {
        // Specify the date and time parts
        $year = $year === null ? date('Y') : $year;
        $month = $month === null ? date('n') : $month;
        $day = $day === null ? date('j') : $day;
        $minute = $minute === null ? ($hour === null ? date('i') : 0) : $minute;
        $second = $second === null ? ($hour === null ? date('s') : 0) : $second;
        $hour = $hour === null ? date('G') : $hour;

        // Create a DateTime instance from the time parts
        return static::createFromFormat('Y-n-j G:i:s', sprintf('%s-%s-%s %s:%02s:%02s', $year, $month, $day, $hour, $minute, $second), $timezone);
    }

    /**
     * Create a DateTime instance with a specific date. The time portion is set to now.
     *
     * If the $year, $month or $day parameters are set to null their now() value will be used.
     *
     * @param int $year [optional] The specified year, or null to use the current year.
     * @param int $month [optional] The specified month, or null to use the current month.
     * @param int $day [optional] The specified day, or null to use the current day.
     * @param DateTimeZone|PHPDateTimeZone|string $timezone The preferred timezone to use, or null to use the default
     *     timezone.
     *
     * @return static The DateTime instance.
     */
    public static function createFromDate($year = null, $month = null, $day = null, $timezone = null) {
        return static::create($year, $month, $day, null, null, null, $timezone);
    }

    /**
     * Create a DateTime instance with a specific time. The date portion is set to today.
     *
     * If $hour is null it will be set to its now() value and the default values for $minute and $second will be their
     * now() values. If $hour is not null, then the default values for $minute and $second will be 0.
     *
     * @param int $hour [optional] The specified hour, or null to use the current hour.
     * @param int $minute [optional] The specified minute, or null to use the current minute if <var>$hour</var> is also null or use zero.
     * @param int $second [optional] The specified second, or null to use the current second if <var>$hour</var> is also null or use zero.
     * @param DateTimeZone|PHPDateTimeZone|string $timezone The preferred timezone to use, or null to use the default
     *     timezone.
     *
     * @return static The DateTime instance.
     */
    public static function createFromTime($hour = null, $minute = null, $second = null, $timezone = null) {
        return static::create(null, null, null, $hour, $minute, $second, $timezone);
    }

    /**
     * Create a DateTime instance from a string with a specified format.
     *
     * @param string $format The format used to parse the date time.
     * @param string $dateTime The date time to parse as a string.
     * @param DateTimeZone|PHPDateTimeZone|string $timezone The preferred timezone to use, or null to use the default
     *     timezone.
     *
     * @return static The DateTime instance or null on failure.
     *
     * @throws InvalidDateTimeZoneException Throws InvalidDateTimeZoneException if the timezone is invalid.
     */
    public static function createFromFormat($format, $dateTime, $timezone = null) {
        // Try to create a DateTime instance based on the input
        if($timezone !== null) {
            // Parse the timezone, throw an exception on failure
            if(($timezone = DateTimeZoneUtils::parse($timezone, null)) === null)
                throw new InvalidDateTimeZoneException('The given timezone is invalid');

            // Create the date and time from the specified format
            $dateTime = parent::createFromFormat($format, $dateTime, $timezone);
        } else
            $dateTime = parent::createFromFormat($format, $dateTime);

        // Make sure the object is valid
        if(empty($dateTime))
            return null;

        // Parse and return the date time
        return static::parse($dateTime, $timezone);
    }

    /**
     * Create a DateTime instance based on a Unix timestamp.
     *
     * @param int $timestamp The timestamp to get the DateTime instance for.
     * @param DateTimeZone|PHPDateTimeZone|string $timezone The preferred timezone to use, or null to use the default
     *     timezone.
     *
     * @return static|null The DateTime instance, or null on failure.
     */
    public static function createFromTimestamp($timestamp, $timezone = null) {
        return ($dateTime = static::now($timezone)->setTimestamp($timestamp)) !== false ? $dateTime : null;
    }

    /**
     * Create a DateTime instance with the default timezone based on a Unix timestamp.
     *
     * @param int $timestamp The Unix timestamp.
     *
     * @return static The DateTime instance.
     */
    public static function createFromTimestampUTC($timestamp) {
        return new static('@' . $timestamp);
    }

    /**
     * Create a DateTime instance for the current date and time.
     *
     * @param DateTimeZone|PHPDateTimeZone|string $timezone [optional] The preferred timezone to use, or null to use
     *     the default timezone.
     * @param bool $real [optional] True to return the real now() value which ignores the mock date and time, false to
     *     return the normal value.
     *
     * @return static The DateTime instance.
     *
     * @throws InvalidDateTimeZoneException Throws InvalidDateTimeZoneException if the timezone is invalid.
     */
    // TODO: Should we rename this to something like createNow(...)?
    // TODO: Better performance?
    public static function now($timezone = null, $real = false) {
        // Define the time
        $time = null;

        // If the real time should be used, gather the real time form PHPs DateTime classes
        if($real) {
            // Parse the timezone, throw an exception on failure
            if($timezone !== null && ($timezone = DateTimeZone::parse($timezone)) === null)
                throw new InvalidDateTimeZoneException('The given timezone is invalid');

            // Gather PHPs now time
            $time = new PHPDateTime('now', $timezone);
        }

        // Return the real now time
        return new static($time, $timezone);
    }

    /**
     * Create a DateTime instance for the start of the current day.
     *
     * @param DateTimeZone|PHPDateTimeZone|string $timezone [optional] The preferred timezone to use, or null to use
     *     the default timezone.
     * @param bool $real [optional] True to use the real now() value which ignores the mock date and time, false to
     *     return the normal value.
     *
     * @return static The DateTime instance.
     */
    public static function createToday($timezone = null, $real = false) {
        return static::now($timezone, $real)->startOfDay();
    }

    /**
     * Create a DateTime instance for the start of tomorrow.
     *
     * @param DateTimeZone|PHPDateTimeZone|string $timezone [optional] The preferred timezone to use, or null to use
     *     the default timezone.
     * @param bool $real [optional] True to use the real now() value which ignores the mock date and time, false to
     *     return the normal value.
     *
     * @return static The DateTime instance.
     */
    public static function createTomorrow($timezone = null, $real = false) {
        return static::createToday($timezone, $real)->addDay();
    }

    /**
     * Create a DateTime instance for the start of yesterday.
     *
     * @param DateTimeZone|PHPDateTimeZone|string $timezone [optional] The preferred timezone to use, or null to use
     *     the default timezone.
     * @param bool $real [optional] True to use the real now() value which ignores the mock date and time, false to
     *     return the normal value.
     *
     * @return static The DateTime instance.
     */
    public static function createYesterday($timezone = null, $real = false) {
        return static::createToday($timezone, $real)->subDay();
    }

    /**
     * Create a DateTime instance for the greatest supported date and time.
     *
     * @return static The DateTime instance.
     */
    public static function createGreatestDate() {
        return static::createFromTimestamp(PHP_INT_MAX);
    }

    /**
     * Create a DateTime instance for the lowest supported date and time.
     *
     * @return static The DateTime instance.
     */
    public static function createLowestDate() {
        return static::createFromTimestamp(~PHP_INT_MAX);
    }

    /**
     * Create a copy of a DateTime instance. A new instance will be created if a PHPDateTime instance was given, or if
     * the time was specified as a string.
     *
     * @param DateTime|PHPDateTime|string|null $other The DateTime instance. Or a PHPDateTime instance, the time as a
     *     string or null.
     *
     * @return DateInterval|null The new DateTime instance, or null on failure.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the date time instance is invalid.
     */
    public static function instance($other) {
        // Reconstruct the object if it's a DateTime instance
        if($other instanceof self)
            return new static($other->format(self::DEFAULT_FORMAT_COMPLETE), $other->getTimeZone());

        // Parse and return the other instance
        if(($instance = static::parse($other)) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');
        return $instance;
    }

    /**
     * Create a copy of this DateTime instance.
     *
     * @return static A new DateTime zone instance.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the date time instance is invalid.
     */
    public function copy() {
        return static::instance($this);
    }

    /**
     * Create a copy of this DateTime instance. Called by PHP when the DateTime object is cloned.
     *
     * @return static A new DateTime zone instance
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the date time instance is invalid.
     */
    public function __clone() {
        return $this->copy();
    }

    /**
     * Parse date and time with a specific timezone. A new instance may be created if required.
     *
     * If the $time parameter is a DateTime zone instance, the instance will be returned and the $timezone parameter is
     * ignored. If the $time parameter is anything other than a DateTime zone the date, time and the timezone is parsed
     * through the constructor.
     *
     * This method allows better fluent syntax because it makes method chaining possible.
     *
     * @param DateTime|PHPDateTime|string|int|null $dateTime [optional] A DateTime instance, the time as a string, the
     *     UNIX timestamp as an integer, or null to use the current time.
     * @param DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $timezone [optional] The timezone the
     *     specified time is in, or null to use the default timezone if the $time param isn't a DateTime instance. A
     *     DateTime or PHPDateTime instance to use it's timezone.
     *
     * @return DateTime|null The parsed DateTime instance, or null on failure.
     *
     * @throws Exception Throws an exception on failure.
     */
    // TODO: Throw exceptions, or make this optional using some parameters?
    public static function parse($dateTime = null, $timezone = null) {
        // Return the object if it's already a DateTime instance
        if($dateTime instanceof self)
            return $dateTime;

        // Handle PHPDateTime instances
        if($dateTime instanceof parent)
            return new self($dateTime);

        // Check whether this is a timestamp
        if(is_int($dateTime))
            return static::createFromTimestamp($dateTime, $timezone);

        // Check whether we should use the now time
        if(empty($dateTime) || StringUtils::equals($dateTime, 'now', false, true))
            return new self($dateTime, $timezone);

        // Check whether the time contains relative keywords
        if(static::hasRelativeKeywords($dateTime))
            // Get a new DateTime instance, and modify the date and time according to the time parameter, return the result
            return static::now($timezone)->modify($dateTime);

        // Couldn't parse the date time object, return null
        return null;
    }

    /**
     * Get a property of the DateTime object.
     *
     * @param string $name The getter name.
     *
     * @return string|int|DateTimeZone|PHPDateTimeZone
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if the property name is unknown.
     */
    public function __get($name) {
        // Parse the getter based on the getter formats list
        if(array_key_exists($name, static::$GETTER_FORMATS))
            return (int) $this->format(static::$GETTER_FORMATS[$name]);

        // Get the week number of the month
        if(StringUtils::equals($name, 'weekOfMonth', false))
            return $this->getWeekOfMonth();

        // Get the age
        if(StringUtils::equals($name, 'age', false))
            return $this->getAge();

        // Ge the current quarter number
        if(StringUtils::equals($name, 'quarter', false))
            return $this->getQuarter();

        // Get the offset
        if(StringUtils::equals($name, 'offset', false))
            return $this->getOffset();

        // Get the offset hours
        if(StringUtils::equals($name, 'offsetHours', false))
            return $this->getOffsetHours();

        // Check whether daylight saving time is active
        if(StringUtils::equals($name, 'dst', false))
            return $this->isDST();

        // Check if the timezone is local
        if(StringUtils::equals($name, 'local', false))
            return $this->isLocal();;

        // Check whether the time is in UTC
        if(StringUtils::equals($name, 'utc', false))
            return $this->isUTC();

        // Get the timezone
        if(StringUtils::equals($name, 'timezone', false))
            return $this->getTimezone();

        // Get the timezone name
        if(StringUtils::equals($name, 'timezoneName', false))
            return $this->getTimezone()->getName();

        // The field name is unknown, throw an exception
        throw new InvalidArgumentException('Unknown property name \'' . $name . '\'');
    }

    /**
     * Check if an attribute exists on the object.
     *
     * @param string $name The name of the attribute.
     *
     * @return bool True if the attribute exists, false otherwise.
     */
    public function __isset($name) {
        try {
            // Try to get an attribute
            $this->__get($name);

        } catch(InvalidArgumentException $e) {
            // The attribute doesn't exist, return the result
            return false;
        }

        // The attribute does exist, return the result
        return true;
    }

    /**
     * Set a property of the DateTime object.
     *
     * @param string $name The name of the attribute to set.
     * @param string|int|DateTimeZone|PHPDateTimeZone $value The value to set it to.
     *
     * @throws DomainException|InvalidArgumentException Throws DomainException if the value type is invalid. Throws InvalidArgumentException if the propery name is unknown.
     */
    public function __set($name, $value) {
        // Set the year
        if(StringUtils::equals($name, 'year', true)) {
            if($this->setDate($value, null, null) === null)
                throw new DomainException('Invalid type for \'' . $name . '\'');
            return;
        }

        // Set the month
        if(StringUtils::equals($name, 'month', true)) {
            if($this->setDate(null, $value, null) === null)
                throw new DomainException('Invalid type for \'' . $name . '\'');
            return;
        }

        // Set the day
        if(StringUtils::equals($name, 'day', true)) {
            if($this->setDate(null, null, $value) === null)
                throw new DomainException('Invalid type for \'' . $name . '\'');
            return;
        }

        // Set the hour
        if(StringUtils::equals($name, 'hour', true)) {
            if($this->setTime($value, null, null) === null)
                throw new DomainException('Invalid type for \'' . $name . '\'');
            return;
        }

        // Set the minute
        if(StringUtils::equals($name, 'minute', true)) {
            if($this->setTime(null, $value, null) === null)
                throw new DomainException('Invalid type for \'' . $name . '\'');
            return;
        }

        // Set the second
        if(StringUtils::equals($name, 'second', true)) {
            if($this->setTime(null, null, $value) === null)
                throw new DomainException('Invalid type for \'' . $name . '\'');
            return;
        }

        // Set the timestamp
        if(StringUtils::equals($name, 'timestamp', true)) {
            if($this->setTimestamp($value) === null)
                throw new DomainException('Invalid type for \'' . $name . '\'');
            return;
        }

        // Set the timezone
        if(StringUtils::equals($name, Array('timezone', 'tz'), true)) {
            if($this->setTimezone($value) === null)
                throw new DomainException('Invalid type for ' . $name);
            return;
        }

        // Failed to set the attribute, throw an exception
        throw new InvalidArgumentException('Unknown property name \'' . $name . '\'');
    }

    /**
     * Get the year.
     *
     * @return int The year.
     */
    public function getYear() {
        return (int) $this->format(static::$GETTER_FORMATS['year']);
    }

    /**
     * Change the year.
     *
     * @param int $year The year.
     *
     * @return static A DateTime instance on success for method chaining.
     *
     * @throws InvalidDateException Throws InvalidDateException if the year is invalid.
     */
    // TODO: Possibility to use a 'null' argument to set the year to the current ::now() year (This might be possible already because the dynamic setters are used, document this in the PHPDocs)
    public function setYear($year) {
        $this->year = $year;
        return $this;
    }

    /**
     * Get the quarter.
     *
     * @return int The quarter.
     */
    public function getQuarter() {
        return (int) ceil($this->getMonth() / 3);
    }

    /**
     * Get the month.
     *
     * @return int The month.
     */
    public function getMonth() {
        return (int) $this->format(static::$GETTER_FORMATS['month']);
    }

    /**
     * Change the month.
     *
     * @param int $month The month.
     *
     * @return static A DateTime instance on success for method chaining.
     *
     * @throws InvalidDateException Throws InvalidDateException if the month is invalid.
     */
    public function setMonth($month) {
        $this->month = $month;
        return $this;
    }

    /**
     * Get the day.
     *
     * @return int The day.
     */
    public function getDay() {
        return (int) $this->format(static::$GETTER_FORMATS['day']);
    }

    /**
     * Change the day.
     *
     * @param int $day The day.
     *
     * @return static A DateTime instance on success for method chaining.
     *
     * @throws InvalidDateException Throws InvalidDateException if the day is invalid.
     */
    public function setDay($day) {
        $this->day = $day;
        return $this;
    }

    /**
     * Get the hour.
     *
     * @return int The hour.
     */
    public function getHour() {
        return (int) $this->format(static::$GETTER_FORMATS['hour']);
    }

    /**
     * Change the hour.
     *
     * @param int $hour The hour.
     *
     * @return static A DateTime instance on success for method chaining.
     *
     * @throws InvalidTimeException Throws InvalidTimeException if the hour is invalid.
     */
    public function setHour($hour) {
        $this->hour = $hour;
        return $this;
    }

    /**
     * Get the minute.
     *
     * @return int The minute.
     */
    public function getMinute() {
        return (int) $this->format(static::$GETTER_FORMATS['minute']);
    }

    /**
     * Change the minute.
     *
     * @param int $minute The minute.
     *
     * @return static A DateTime instance on success for method chaining.
     *
     * @throws InvalidTimeException Throws InvalidTimeException if the minute is invalid.
     */
    public function setMinute($minute) {
        $this->minute = $minute;
        return $this;
    }

    /**
     * Get the second.
     *
     * @return int The second.
     */
    public function getSecond() {
        return (int) $this->format(static::$GETTER_FORMATS['second']);
    }

    /**
     * Change the second.
     *
     * @param int $second The second.
     *
     * @return static A DateTime instance on success for method chaining.
     *
     * @throws InvalidTimeException Throws InvalidTimeException if the second is invalid.
     */
    public function setSecond($second) {
        $this->second = $second;
        return $this;
    }

    /**
     * Set the timestamp.
     *
     * @param int|null $timestamp The UNIX timestamp, or null to use the current timestamp.
     *
     * @return static A DateTime instance on success for method chaining.
     *
     * @throws InvalidTimestampException Throws InvalidTimestampException if the timestamp is invalid.
     */
    public function setTimestamp($timestamp) {
        // Check whether the timestamp should be set to the current
        if($timestamp === null) {
            // Check whether we need to use a mock date and time
            if(static::hasMockNow())
                $timestamp = static::getMockNow()->getTimestamp();
            else
                $timestamp = time();

            // Set the actual timestamp, return the result
            if(parent::setTimestamp($timestamp) === false)
                throw new InvalidTimestampException('Failed to set the timestamp to \'' . $timestamp . '\'');
            return $this;
        }

        // Set time timestamp, throw an exception on failure
        if(!is_long($timestamp) || parent::setTimestamp($timestamp) === false)
            throw new InvalidTimestampException('The given timestamp \'' . $timestamp . '\' is invalid');

        // Return this instance
        return $this;
    }

    /**
     * Get the timezone.
     *
     * @return DateTimeZone|null The timezone, or null on failure.
     */
    public function getTimezone() {
        // TODO: Make this method faster, possibly, without parsing probably?
        return DateTimeZone::parse(parent::getTimezone());
    }

    /**
     * Set the timezone.
     *
     * @param DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $timezone A DateTimeZone or PHPDateTimeZone
     *     instance or the timezone ID as a string. A DateTime or PHPDateTime instance to use it's timezone. Null to use the default timezone.
     *
     * @return static A DateTime instance on success for method chaining.
     *
     * @throws InvalidDateTimeZoneException Throws InvalidDateTimeZoneException if the timezone is invalid.
     */
    public function setTimezone($timezone = null) {
        // Parse the timezone, return null on failure
        if(($timezone = DateTimeZone::parse($timezone)) === null)
            throw new InvalidDateTimeZoneException('The given timezone is invalid');

        // Set the timezone, and return the current instance for method chaining
        parent::setTimezone($timezone);
        return $this;
    }

    /**
     * Get the timezone offset from UTC in seconds at the specified point in time.
     *
     * @return int|null The timezone offset in seconds, or null on failure.
     */
    public function getOffset() {
        return ($offset = parent::getOffset()) !== false ? $offset : null;
    }

    /**
     * Get the timezone offset from UTC in hours at the specified point in time.
     *
     * @return float|null The timezone offset in hours, or null on failure.
     */
    public function getOffsetHours() {
        return ($offsetSeconds = parent::getOffset()) !== false ?
            ($offsetSeconds / self::SECONDS_PER_MINUTE / self::MINUTES_PER_HOUR) : null;
    }

    /**
     * Get a mock DateTime instance which is returned when the now() method is called.
     *
     * @return DateTime The mock DateTime instance, or null when no mock instance is set.
     */
    public static function getMockNow() {
        return static::$mockNow;
    }

    /**
     * Check whether a mock DateTime instance is set.
     *
     * @return bool True if any mock DateTime instance is set, false otherwise.
     */
    public static function hasMockNow() {
        return static::getMockNow() !== null;
    }

    /**
     * Set a mock DateTime instance which is returned when the now() method is called.
     * A new DateTime instance will be created if $mockNow needs to be parsed into a DateTime instance.
     * This affects all methods using the now() method as default when no time data is supplied.
     *
     * The timezone doesn't have any effect on this method.
     *
     * To reset the mock instance, call this method using the default parameter of null.
     *
     * @param DateTime|PHPDateTime|string|null $mockNow [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to reset the mock date and time.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the <var>$mockNow</var> date time instance is invalid.
     */
    public static function setMockNow($mockNow = null) {
        // Check whether the mock instance should be reset
        if($mockNow === null) {
            static::$mockNow = null;
            return;
        }

        // Parse the mock date and time, return false on failure
        if(($mockNow = static::parse($mockNow)) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Set the mock date and time
        static::$mockNow = $mockNow;
    }

    /**
     * Get the micro.
     *
     * @return int The micro.
     */
    public function getMicro() {
        return (int) $this->format(static::$GETTER_FORMATS['micro']);
    }

    /**
     * Get the day of the week.
     *
     * @return int The day of the week.
     */
    public function getDayOfWeek() {
        return (int) $this->format(static::$GETTER_FORMATS['dayOfWeek']);
    }

    /**
     * Get the day of the year.
     *
     * @return int The day of the year.
     */
    public function getDayOfYear() {
        return (int) $this->format(static::$GETTER_FORMATS['dayOfYear']);
    }

    /**
     * Get the week of the year.
     *
     * @return int The week of the year.
     */
    public function getWeekOfYear() {
        return (int) $this->format(static::$GETTER_FORMATS['weekOfYear']);
    }

    /**
     * Get the week of the month.
     *
     * @return int The week of the month.
     */
    public function getWeekOfMonth() {
        return (int) ceil($this->getDay() / static::DAYS_PER_WEEK);
    }

    /**
     * Get the number of days in this month.
     *
     * @return int The number of days.
     */
    public function getDaysInMonth() {
        return (int) $this->format(static::$GETTER_FORMATS['daysInMonth']);
    }

    /**
     * Set the date of the object.
     *
     * @param int|null $year [optional] The year, or null to leave the year unchanged.
     * @param int|null $month [optional] The month, or null to leave the month unchanged.
     * @param int|null $day [optional] The day, or null to leave the day unchanged.
     *
     * @return DateTime The DateTime instance for method chaining.
     *
     * @throws InvalidDateException Throws InvalidDateException if the date is invalid.
     */
    public function setDate($year = null, $month = null, $day = null) {
        // Make sure the parameters are valid or null
        if(($year !== null && !is_int($year)) ||
            ($month !== null && !is_int($month)) ||
            ($day !== null && !is_int($day)))
            throw new InvalidDateException('The given date is invalid (\'' . $year . '\' years, \'' . $month . '\' months and \'' . $day . '\' days)');

        // Handle the null parameters
        if($year === null)
            $year = $this->getYear();
        if($month === null)
            $month = $this->getMonth();
        if($day === null)
            $day = $this->getDay();

        // Set the date using the parent method, throw an exception on failure
        if(parent::setDate($year, $month, $day) === false)
            throw new InvalidDateException('The given date is invalid (\'' . $year . '\' years, \'' . $month . '\' months and \'' . $day . '\' days)');

        // Return this
        return $this;
    }

    /**
     * Set a date according to the ISO 8601 standard, using weeks and day offsets rather than specific dates.
     *
     * @param int|null $year [optional] The year, or null to leave the year unchanged.
     * @param int|null $week [optional] The week, or null to leave the week unchanged.
     * @param int|null $day [optional] The day offset, or null to leave the day offset unchanged.
     *
     * @return DateTime The DateTime instance for method chaining.
     *
     * @throws InvalidDateException Throws InvalidDateException if the date is invalid.
     */
    public function setISODate($year = null, $week = null, $day = null) {
        // Make sure the parameters are valid or null
        if(($year !== null && !is_int($year)) ||
            ($week !== null && !is_int($week)) ||
            ($day !== null && !is_int($day)))
            throw new InvalidDateException('The given date is invalid (\'' . $year . '\' years, \'' . $week . '\' weeks and \'' . $day . '\' days)');

        // Handle the null parameters
        if($year === null)
            $year = $this->getYear();
        if($week === null)
            $week = $this->getWeekOfYear();
        if($day === null)
            $day = $this->getDay();

        // Set the ISO date using the parent method, throw an exception on failure
        if(parent::setISODate($year, $week, $day) === false)
            throw new InvalidDateException('The given date is invalid (\'' . $year . '\' years, \'' . $week . '\' weeks and \'' . $day . '\' days)');

        // Return this
        return $this;
    }

    /**
     * Set the time of the object.
     *
     * @param int|null $hour [optional] The hour, or null to leave the hour unchanged.
     * @param int|null $minute [optional] The minute, or null to leave the minute unchanged.
     * @param int|null $second [optional] The second, or null to leave the second unchanged.
     *
     * @return DateTime The DateTime instance for method chaining.
     *
     * @throws InvalidTimeException Throws InvalidTimeException if the time is invalid.
     */
    public function setTime($hour = null, $minute = null, $second = null) {
        // Make sure the parameters are valid or null
        if(($hour !== null && !is_int($hour)) ||
            ($minute !== null && !is_int($minute)) ||
            ($second !== null && !is_int($second)))
            throw new InvalidTimeException('The given time is invalid (\'' . $hour . '\' hours, \'' . $minute . '\' minutes and \'' . $second . '\' seconds)');

        // Handle the null parameters
        if($hour === null)
            $hour = $this->getHour();
        if($minute === null)
            $minute = $this->getMinute();
        if($second === null)
            $second = $this->getSecond();

        // Set the time using the parent function, throw an exception on failure
        if(parent::setTime($hour, $minute, $second) === false)
            throw new InvalidTimeException('The given time is invalid (\'' . $hour . '\' hours, \'' . $minute . '\' minutes and \'' . $second . '\' seconds)');

        // Return this
        return $this;
    }

    /**
     * Set the date and time of the object.
     *
     * @param int|null $year [optional] The year, or null to leave the year unchanged.
     * @param int|null $month [optional] The month, or null to leave the month unchanged.
     * @param int|null $day [optional] The day, or null to leave the day unchanged.
     * @param int|null $hour [optional] The hour, or null to leave the hour unchanged.
     * @param int|null $minute [optional] The minute, or null to leave the minute unchanged.
     * @param int|null $second [optional] The second, or null to leave the second unchanged.
     *
     * @return static The DateTime instance.
     *
     * @throws InvalidDateException|InvalidTimeException Throws InvalidDateException or InvalidTimeException if the date or time is invalid.
     */
    public function setDateTime($year = null, $month = null, $day = null, $hour = null, $minute = null, $second = null) {
        $this->setDate($year, $month, $day);
        $this->setTime($hour, $minute, $second);
    }

    /**
     * Check whether there is a relative keyword in the date and time string, this is to create dates relative to now for test instances, for example 'tomorrow' or 'next tuesday'.
     *
     * @param string $dateTime The date and time string to check.
     *
     * @return bool True if there is a relative keyword in the date and time string, false otherwise.
     */
    public static function hasRelativeKeywords($dateTime) {
        // Check whether the time string contains any relative keywords, skip the common time format
        if(preg_match('/[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}/', $dateTime) !== 1)
            if(StringUtils::contains($dateTime, static::$RELATIVE_KEYWORDS, false))
                return true;

        // The time string doesn't contain any relative keywords, return the result
        return false;
    }

    /**
     * Check whether the date equals to the date of the specified date and time parameter.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] The other DateTime or PHPDateTime instance, the
     *     date and time as a string or null to use the now() time.
     *
     * @return bool True if the date equals the date of the specified date and time, false if not.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the given date time instance is invalid.
     */
    public function equals($dateTime = null) {
        // Parse the date and time, return false on failure
        if(($dateTime = static::parse($dateTime)) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Compare the date and time of both objects, return the result
        return StringUtils::equals($this->toCompleteString(), $dateTime->toCompleteString());
    }

    /**
     * Check whether the date is greater than the date of the specified date and time parameter.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] The other DateTime or PHPDateTime instance, the
     *     date and time as a string or null to use the now() time.
     *
     * @return bool True if the date is greater than the specified date and time, false if not.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the given date time instance is invalid.
     */
    public function isGreaterThan($dateTime = null) {
        // Parse the date and time, return false on failure
        if(($dateTime = static::parse($dateTime)) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Compare the date and time, return the result
        return $this > $dateTime;
    }

    /**
     * Check whether the date is greater or equal to the date of the specified date and time parameter.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] The other DateTime or PHPDateTime instance, the
     *     date and time as a string or null to use the now() time.
     *
     * @return bool True if the date is greater or equal to the specified date and time, false if not.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the given date time instance is invalid.
     */
    public function isGreaterOrEqualTo($dateTime = null) {
        // Parse the date and time, return false on failure
        if(($dateTime = static::parse($dateTime)) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Compare the date and time, return the result
        return $this >= $dateTime;
    }

    /**
     * Check whether the date is less than the date of the specified date and time parameter.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] The other DateTime or PHPDateTime instance, the
     *     date and time as a string or null to use the now() time.
     *
     * @return bool True if the date is less than the specified date and time, false if not.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the given date time instance is invalid.
     */
    public function isLessThan($dateTime = null) {
        // Parse the date and time, return false on failure
        if(($dateTime = static::parse($dateTime)) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Compare the date and time, return the result
        return $this < $dateTime;
    }

    /**
     * Check whether the date is less or equal to the date of the specified date and time parameter.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] The other DateTime or PHPDateTime instance, the
     *     date and time as a string or null to use the now() time.
     *
     * @return bool True if the date is less or equal to the specified date and time, false if not.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the given date time instance is invalid.
     */
    public function isLessOrEqualTo($dateTime = null) {
        // Parse the date and time, return false on failure
        if(($dateTime = static::parse($dateTime)) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Compare the date and time, return the result
        return $this <= $dateTime;
    }

    /**
     * Check whether the specified date and time is between a and b.
     * The $a and $b parameter may not be null at the same time or an exception will be thrown.
     *
     * @param DateTime|PHPDateTime|string|null $a The date and time as DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param DateTime|PHPDateTime|string|null $b The date and time as DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param bool $equals [optional] True to also return true if the date equals one of the specified date and times,
     *     false otherwise.
     *
     * @return bool True if the date is between the specified date and time, or if it equals one of the date and
     *     times while $equals is set to true. False will be returned otherwise.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if a given date time instance is invalid.
     */
    public function isBetween($a = null, $b = null, $equals = true) {
        // The a and b parameters may not be null at the same time
        if($a === null && $b === null)
            throw new InvalidDateTimeException('Both given date time instances are null');

        // Parse the date and times, return null on failure
        if(($a = static::parse($a)) === null || ($b = static::parse($b)) === null)
            throw new InvalidDateTimeException('A given date time instance is invalid');

        // Get the lowest and greatest date
        $aGreater = $a->isGreaterThan($b);
        $lowest = $aGreater ? $b : $a;
        $greatest = $aGreater ? $a : $b;

        // Check whether the dates may equal
        if($equals)
            // Check whether the date is in between or equals, return the result
            return $this->isGreaterOrEqualTo($lowest) && $this->isLessOrEqualTo($greatest);

        // Check whether the date is in between, return the result
        return $this->isGreaterThan($lowest) && $this->isLessThan($greatest);
    }

    /**
     * Get the greatest date and time of this instance and the specified date and time.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     *
     * @return DateTime The greatest DateTime instance.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the given date time instance is invalid.
     */
    public function max($dateTime = null) {
        // Parse the date and time, return false on failure
        if(($dateTime = self::parse($dateTime)) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Return the greatest date and time
        return $this->isGreaterOrEqualTo($dateTime) ? $this : $dateTime;
    }

    /**
     * Get the lowest date and time of this instance and the specified date and time.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     *
     * @return DateTime The lowest DateTime instance.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the given date time instance is invalid.
     */
    public function min($dateTime = null) {
        // Parse the date and time, return false on failure
        if(($dateTime = self::parse($dateTime)) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Return the greatest date and time
        return $this->isLessOrEqualTo($dateTime) ? $this : $dateTime;
    }

    /**
     * Check whether this is a weekday (monday to friday).
     *
     * @return bool True if this is a weekday, false otherwise.
     */
    public function isWeekday() {
        return $this->dayOfWeek != static::SUNDAY && $this->dayOfWeek != static::SATURDAY;
    }

    /**
     * Check whether this is a weekend day (saturday or sunday).
     *
     * @return bool True if this is a weekend day, false otherwise.
     */
    public function isWeekend() {
        return !$this->isWeekday();
    }

    /**
     * Check whether this is today.
     *
     * @return bool True if this is today, false if not.
     */
    public function isToday() {
        return StringUtils::equals($this->toDateString(), static::now($this->getTimezone())->toDateString());
    }

    /**
     * Check whether this is tomorrow.
     *
     * @return bool True if this is tomorrow, false if not.
     */
    public function isTomorrow() {
        return StringUtils::equals($this->toDateString(),
            static::createTomorrow($this->getTimezone())->toDateString());
    }

    /**
     * Check whether this is yesterday.
     *
     * @return bool True if this is yesterday, false if not.
     */
    public function isYesterday() {
        return StringUtils::equals($this->toDateString(),
            static::createYesterday($this->getTimezone())->toDateString());
    }

    /**
     * Check whether this is in the future. If the date and time equals the now() date and time false is returned.
     *
     * @return bool True if this is in the future, false if not.
     */
    public function isFuture() {
        return $this->isGreaterThan(static::now($this->getTimezone()));
    }

    /**
     * Check whether this is in the past. If the date and time equals the now() date and time false is returned.
     *
     * @return bool True if this is in the past, false if not.
     */
    public function isPast() {
        return $this->isLessThan(static::now($this->getTimezone()));
    }

    /**
     * Check whether this is a leap year.
     *
     * @return bool True if this is a leap year, false if not.
     */
    public function isLeapYear() {
        return StringUtils::equals($this->format('L'), '1');
    }

    /**
     * Check whether the time is local, which is the same time as in the default timezone.
     *
     * @return bool True if the time is local, false if not.
     */
    public function isLocal() {
        return $this->getTimezone()->isLocal(DateTimeZoneUtils::getDefaultTimezone());
    }

    /**
     * Check whether the time is in daylight saving time.
     *
     * @return bool True if the time is in daylight saving time, false if not.
     */
    public function isDST() {
        return StringUtils::equals($this->format('I'), '1');
    }

    /**
     * Check whether the current time is equal to the time in the UTC timezone.
     *
     * @return bool True if this is UTC time, false if not.
     */
    public function isUTC() {
        return $this->getOffset() == 0;
    }

    /**
     * Check whether the year equals the specified date.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     *
     * @return bool True if year is the same as the specified date, false otherwise.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the given date time instance is invalid.
     */
    public function isSameYear($dateTime) {
        // Parse the date and time, return false on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Check whether the week is equal, return the result
        return StringUtils::equals($this->format('Y'), $dateTime->format('Y'));
    }

    /**
     * Check whether the month equals the specified date.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     *
     * @return bool True if month is the same as the specified date, false otherwise.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the given date time instance is invalid.
     */
    public function isSameMonth($dateTime) {
        // Parse the date and time, return false on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Check whether the week is equal, return the result
        return StringUtils::equals($this->format('Y-m'), $dateTime->format('Y-m'));
    }

    /**
     * Check whether the ISO-8601 week equals the specified date.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     *
     * @return bool True if the ISO-8601 week is the same as the specified date, false otherwise.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the given date time instance is invalid.
     */
    public function isSameWeek($dateTime) {
        // Parse the date and time, return false on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Check whether the week is equal, return the result
        return StringUtils::equals($this->format('Y-W'), $dateTime->format('Y-W'));
    }

    /**
     * Check whether the date is equal to the specified date.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     *
     * @return bool True if the date is the same as the specified date, false otherwise.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the given date time instance is invalid.
     */
    public function isSameDate($dateTime) {
        // Parse the date and time, return false on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Check whether the date is equal
        return StringUtils::equals($this->toDateString(), $dateTime->toDateString());
    }

    /**
     * Check whether the hour equals the specified date.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param bool $checkDate [optional] True to make sure the dates are equal, false to just compare the time.
     *
     * @return bool True if hour is the same as the specified date, false otherwise.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the given date time instance is invalid.
     */
    public function isSameHour($dateTime, $checkDate = true) {
        // Parse the date and time, return false on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Compare the date
        if($checkDate)
            if(!$this->isSameDate($dateTime))
                return false;

        // Check whether the week is equal, return the result
        return StringUtils::equals($this->format('H'), $dateTime->format('H'));
    }

    /**
     * Check whether the minute equals the specified date.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param bool $checkDate [optional] True to make sure the dates are equal, false to just compare the time.
     *
     * @return bool True if minute is the same as the specified date, false otherwise.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the given date time instance is invalid.
     */
    public function isSameMinute($dateTime, $checkDate = true) {
        // Parse the date and time, return false on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Compare the date
        if($checkDate)
            if(!$this->isSameDate($dateTime))
                return false;

        // Check whether the week is equal, return the result
        return StringUtils::equals($this->format('H:i'), $dateTime->format('H:i'));
    }

    /**
     * Check whether the second equals the specified date.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() time.
     * @param bool $checkDate [optional] True to make sure the dates are equal, false to just compare the time.
     *
     * @return bool True if second is the same as the specified date, false otherwise.
     *
     * @throws InvalidDateTimeException Throws InvalidDateTimeException if the given date time instance is invalid.
     */
    public function isSameTime($dateTime, $checkDate = true) {
        // Parse the date and time, return false on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            throw new InvalidDateTimeException('The given date time instance is invalid');

        // Compare the date
        if($checkDate)
            if(!$this->isSameDate($dateTime))
                return false;

        // Check whether the week is equal, return the result
        return StringUtils::equals($this->toTimeString(), $dateTime->toTimeString());
    }

    // TODO: Continue here with implementing exceptions!

    /**
     * Alter the date and time by incrementing or decrementing based on the modify parameter in a format accepted by
     * PHP's strtotime(). If an empty string, or null is provided null will be returned.
     *
     * @param string $modify A date/time string in a format accepted by PHPs strtotime();.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     */
    public function modify($modify) {
        // Make sure the modify parameter isn't empty
        if(empty($modify))
            return $this;

        // TODO: Throw an exception here, or is this done already in the parent method?
        // TODO: Update the comments of this method!
        // Modify the date and time using the parent method, return null on failure
        if(parent::modify($modify) === false)
            return null;

        // Return the current instance for method chaining
        return $this;
    }

    /**
     * Adds an amount of days, months, years, hours, minutes and seconds to a DateTime object
     *
     * @param DateInterval|PHPDateInterval|string|null $dateInterval A DateInterval or PHPDateInterval instance, the
     *     date interval specification as a string, a relative date and time as a string or null to use a zero date
     *     interval.
     *
     * @return static This instance for method chaining.
     *
     * @throws InvalidDateIntervalException Throws InvalidDateIntervalException if the given date interval instance is invalid.
     */
    public function add($dateInterval) {
        // Parse the date interval, throw an exception on failure
        if(($dateInterval = DateInterval::parse($dateInterval)) === null)
            throw new InvalidDateIntervalException('The given date interval instance is invalid');

        // Add the date interval, return this
        parent::add($dateInterval);
        return $this;
    }

    /**
     * Subtract an amount of days, months, years, hours, minutes and seconds from a DateTime object
     *
     * @param DateInterval|PHPDateInterval|string|null $dateInterval A DateInterval or PHPDateInterval instance, the
     *     date interval specification as a string, a relative date and time as a string or null to use a zero date
     *     interval.
     *
     * @return static This instance for method chaining.
     *
     * @throws InvalidDateIntervalException Throws InvalidDateIntervalException if the given date interval instance is invalid.
     */
    public function sub($dateInterval) {
        // Parse the date interval, throw an exception on failure
        if(($dateInterval = DateInterval::parse($dateInterval)) === null)
            throw new InvalidDateIntervalException('The given date interval instance is invalid');

        // Subtract the date interval, return this
        parent::sub($dateInterval);
        return $this;
    }

    /**
     * Travel the specified number of years forward in time. A positive number of years will travel forward in time,
     * while a negative number of years travels backward.
     *
     * @param int $years The number of years to travel forward in time.
     *
     * @return DateTime The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of years is given.
     */
    public function addYears($years) {
        // Make sure the years value is an integer
        if(!is_int($years))
            throw new InvalidArgumentException('The number of years must be an integer');

        // Travel the specified number of years in time
        // TODO: Throw  another exception here, also for the similar methods!
        if($this->modify($years . ' year') === false)
            return null;

        // Return this for method chaining
        return $this;
    }

    /**
     * Travel a year, or the specified number of years forward in time. A positive number of years will travel forward
     * in time, while a negative number of years travels backward.
     *
     * @param int $years [optional] The number of years to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of years is given.
     */
    public function addYear($years = 1) {
        return $this->addYears($years);
    }

    /**
     * Travel the specified number of years backward in time. A positive number of years will travel backward in time,
     * while a negative number of years travels forward.
     *
     * @param int $years The number of years to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of years is given.
     */
    public function subYears($years) {
        return $this->addYears($years * -1);
    }

    /**
     * Travel a year, or the specified number of years backward in time. A positive number of years will travel
     * backward in time, while a negative number of years will travel forward.
     *
     * @param int $years [optional] The number of years to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of years is given.
     */
    public function subYear($years = 1) {
        return $this->subYears($years);
    }

    /**
     * Travel the specified number of months forward in time. A positive number of months will travel forward in time,
     * while a negative number of months travels backward.
     *
     * @param int $months The number of months to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of months is given.
     */
    public function addMonths($months) {
        // Make sure the months value is an integer
        if(!is_int($months))
            throw new InvalidArgumentException('The number of months must be an integer');

        // Travel the specified number of months in time
        if($this->modify($months . ' month') === false)
            return null;

        // Return this for method chaining
        return $this;
    }

    /**
     * Travel a month, or the specified number of months forward in time. A positive number of months will travel
     * forward in time, while a negative number of months travels backward.
     *
     * @param int $months [optional] The number of months to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of months is given.
     */
    public function addMonth($months = 1) {
        return $this->addMonths($months);
    }

    /**
     * Travel the specified number of months backward in time. A positive number of months will travel backward in
     * time, while a negative number of months travels forward.
     *
     * @param int $months The number of months to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of months is given.
     */
    public function subMonths($months) {
        return $this->addMonths($months * -1);
    }

    /**
     * Travel a month, or the specified number of months backward in time. A positive number of months will travel
     * backward in time, while a negative number of months will travel forward.
     *
     * @param int $months [optional] The number of months to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of months is given.
     */
    public function subMonth($months = 1) {
        return $this->subMonths($months);
    }

    /**
     * Travel the specified number of days forward in time. A positive number of days will travel forward in time,
     * while a negative number of days travels backward.
     *
     * @param int $days The number of days to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of days is given.
     */
    public function addDays($days) {
        // Make sure the days value is an integer
        if(!is_int($days))
            throw new InvalidArgumentException('The number of days must be an integer');

        // Travel the specified number of days in time
        if($this->modify($days . ' day') === false)
            return null;

        // Return this for method chaining
        return $this;
    }

    /**
     * Travel a day, or the specified number of days forward in time. A positive number of days will travel forward in
     * time, while a negative number of days travels backward.
     *
     * @param int $days [optional] The number of days to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of days is given.
     */
    public function addDay($days = 1) {
        return $this->addDays($days);
    }

    /**
     * Travel the specified number of days backward in time. A positive number of days will travel backward in time,
     * while a negative number of days travels forward.
     *
     * @param int $days The number of days to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of days is given.
     */
    public function subDays($days) {
        return $this->addDays($days * -1);
    }

    /**
     * Travel a day, or the specified number of days backward in time. A positive number of days will travel backward
     * in time, while a negative number of days will travel forward.
     *
     * @param int $days [optional] The number of days to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of days is given.
     */
    public function subDay($days = 1) {
        return $this->subDays($days);
    }

    /**
     * Travel the specified number of weekdays forward in time. A positive number of weekdays will travel forward in
     * time, while a negative number of weekdays travels backward.
     *
     * @param int $weekdays The number of weekdays to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of weekdays is given.
     */
    public function addWeekdays($weekdays) {
        // Make sure the weekdays value is an integer
        if(!is_int($weekdays))
            throw new InvalidArgumentException('The number of weekdays must be an integer');

        // Travel the specified number of weekdays in time
        if($this->modify($weekdays . ' weekday') === false)
            return null;

        // Return this for method chaining
        return $this;
    }

    /**
     * Travel a weekday, or the specified number of weekdays forward in time. A positive number of weekdays will travel
     * forward in time, while a negative number of weekdays travels backward.
     *
     * @param int $weekdays [optional] The number of weekdays to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of weekdays is given.
     */
    public function addWeekday($weekdays = 1) {
        return $this->addWeekdays($weekdays);
    }

    /**
     * Travel the specified number of weekdays backward in time. A positive number of weekdays will travel backward in
     * time, while a negative number of weekdays travels forward.
     *
     * @param int $weekdays The number of weekdays to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of weekdays is given.
     */
    public function subWeekdays($weekdays) {
        return $this->addWeekdays($weekdays * -1);
    }

    /**
     * Travel a weekday, or the specified number of weekdays backward in time. A positive number of weekdays will
     * travel backward in time, while a negative number of weekdays will travel forward.
     *
     * @param int $weekdays [optional] The number of weekdays to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of weekdays is given.
     */
    public function subWeekday($weekdays = 1) {
        return $this->subWeekdays($weekdays);
    }

    /**
     * Travel the specified number of weeks forward in time. A positive number of weeks will travel forward in time,
     * while a negative number of weeks travels backward.
     *
     * @param int $weeks The number of weeks to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of weeks is given.
     */
    public function addWeeks($weeks) {
        // Make sure the weeks value is an integer
        if(!is_int($weeks))
            throw new InvalidArgumentException('The number of weeks must be an integer');

        // Travel the specified number of weeks in time
        if($this->modify($weeks . ' week') === false)
            return null;

        // Return this for method chaining
        return $this;
    }

    /**
     * Travel a week, or the specified number of weeks forward in time. A positive number of weeks will travel forward
     * in time, while a negative number of weeks travels backward.
     *
     * @param int $weeks [optional] The number of weeks to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of weeks is given.
     */
    public function addWeek($weeks = 1) {
        return $this->addWeeks($weeks);
    }

    /**
     * Travel the specified number of weeks backward in time. A positive number of weeks will travel backward in time,
     * while a negative number of weeks travels forward.
     *
     * @param int $weeks The number of weeks to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of weeks is given.
     */
    public function subWeeks($weeks) {
        return $this->addWeeks($weeks * -1);
    }

    /**
     * Travel a week, or the specified number of weeks backward in time. A positive number of weeks will travel
     * backward in time, while a negative number of weeks will travel forward.
     *
     * @param int $weeks [optional] The number of weeks to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of weeks is given.
     */
    public function subWeek($weeks = 1) {
        return $this->subWeeks($weeks);
    }

    /**
     * Travel the specified number of hours forward in time. A positive number of hours will travel forward in time,
     * while a negative number of hours travels backward.
     *
     * @param int $hours The number of hours to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of hours is given.
     */
    public function addHours($hours) {
        // Make sure the hours value is an integer
        if(!is_int($hours))
            throw new InvalidArgumentException('The number of hours must be an integer');

        // Travel the specified number of hours in time
        if($this->modify($hours . ' hour') === false)
            return null;

        // Return this for method chaining
        return $this;
    }

    /**
     * Travel a hour, or the specified number of hours forward in time. A positive number of hours will travel forward
     * in time, while a negative number of hours travels backward.
     *
     * @param int $hours [optional] The number of hours to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of hours is given.
     */
    public function addHour($hours = 1) {
        return $this->addHours($hours);
    }

    /**
     * Travel the specified number of hours backward in time. A positive number of hours will travel backward in time,
     * while a negative number of hours travels forward.
     *
     * @param int $hours The number of hours to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of hours is given.
     */
    public function subHours($hours) {
        return $this->addHours($hours * -1);
    }

    /**
     * Travel a hour, or the specified number of hours backward in time. A positive number of hours will travel
     * backward in time, while a negative number of hours will travel forward.
     *
     * @param int $hours [optional] The number of hours to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of hours is given.
     */
    public function subHour($hours = 1) {
        return $this->subHours($hours);
    }

    /**
     * Travel the specified number of minutes forward in time. A positive number of minutes will travel forward in
     * time, while a negative number of minutes travels backward.
     *
     * @param int $minutes The number of minutes to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of minutes is given.
     */
    public function addMinutes($minutes) {
        // Make sure the minutes value is an integer
        if(!is_int($minutes))
            throw new InvalidArgumentException('The number of minutes must be an integer');

        // Travel the specified number of minutes in time
        if($this->modify($minutes . ' minute') === false)
            return null;

        // Return this for method chaining
        return $this;
    }

    /**
     * Travel a minute, or the specified number of minutes forward in time. A positive number of minutes will travel
     * forward in time, while a negative number of minutes travels backward.
     *
     * @param int $minutes [optional] The number of minutes to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of minutes is given.
     */
    public function addMinute($minutes = 1) {
        return $this->addMinutes($minutes);
    }

    /**
     * Travel the specified number of minutes backward in time. A positive number of minutes will travel backward in
     * time, while a negative number of minutes travels forward.
     *
     * @param int $minutes The number of minutes to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of minutes is given.
     */
    public function subMinutes($minutes) {
        return $this->addMinutes($minutes * -1);
    }

    /**
     * Travel a minute, or the specified number of minutes backward in time. A positive number of minutes will travel
     * backward in time, while a negative number of minutes will travel forward.
     *
     * @param int $minutes [optional] The number of minutes to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of minutes is given.
     */
    public function subMinute($minutes = 1) {
        return $this->subMinutes($minutes);
    }

    /**
     * Travel the specified number of seconds forward in time. A positive number of seconds will travel forward in
     * time, while a negative number of seconds travels backward.
     *
     * @param int $seconds The number of seconds to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of seconds is given.
     */
    public function addSeconds($seconds) {
        // Make sure the seconds value is an integer
        if(!is_int($seconds))
            throw new InvalidArgumentException('The number of seconds must be an integer');

        // Travel the specified number of seconds in time
        if($this->modify($seconds . ' second') === false)
            return null;

        // Return this for method chaining
        return $this;
    }

    /**
     * Travel a second, or the specified number of seconds forward in time. A positive number of seconds will travel
     * forward in time, while a negative number of seconds travels backward.
     *
     * @param int $seconds [optional] The number of seconds to travel forward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of seconds is given.
     */
    public function addSecond($seconds = 1) {
        return $this->addSeconds($seconds);
    }

    /**
     * Travel the specified number of seconds backward in time. A positive number of seconds will travel backward in
     * time, while a negative number of seconds travels forward.
     *
     * @param int $seconds The number of seconds to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of seconds is given.
     */
    public function subSeconds($seconds) {
        return $this->addSeconds($seconds * -1);
    }

    /**
     * Travel a second, or the specified number of seconds backward in time. A positive number of seconds will travel
     * backward in time, while a negative number of seconds will travel forward.
     *
     * @param int $seconds [optional] The number of seconds to travel backward in time.
     *
     * @return DateTime|null The DateTime instance for method chaining, or null on failure.
     *
     * @throws InvalidArgumentException Throws InvalidArgumentException if an invalid number of seconds is given.
     */
    public function subSecond($seconds = 1) {
        return $this->subSeconds($seconds);
    }

    /**
     * Get the difference between this and another date and time object.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param bool $absolute True to return the absolute interval, false otherwise.
     *
     * @return DateInterval|null The difference as DateInterval or null on failure.
     */
    public function diff($dateTime = null, $absolute = true) {
        // Parse the date and time, return null on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            return null;

        // Get the difference, return null on failure
        if(($diff = parent::diff($dateTime, $absolute)) === false)
            return null;

        // Parse and return the date interval
        return DateInterval::parse($diff);
    }

    /**
     * Get the difference by the given interval using a filter closure.
     * The callback will be called for each period in the given time frame. If the callback returns true the period is
     * included as difference, false should be returned otherwise.
     *
     * @param DateInterval|PHPDateInterval|string|null $dateInterval A DateInterval or PHPDateInterval instance, the
     *     date interval specification as a string, the date interval relative date and time as a string or null to use
     *     a zero date interval.
     * @param Closure $callback The callback function to call for each period as filter.
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] The DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param boolean $absolute [optional] True to get the absolute difference, false otherwise.
     *
     * @return int|null The difference of the date interval in the given time frame. Null will be returned on failure.
     */
    public function diffFiltered($dateInterval, Closure $callback, $dateTime = null, $absolute = true) {
        // Parse the date interval, return null on failure
        if(($dateInterval = DateInterval::parse($dateInterval)) === null)
            return null;

        // Parse the date and time, return null on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            return null;

        // Define the start and end times, define whether the value should be inverted
        $inverse = !$this->isLessThan($dateTime);
        $start = $inverse ? $this : $dateTime;
        $end = $inverse ? $dateTime : $start;

        // Run the callback for all periods
        $period = new DatePeriod($start, $dateInterval, $end);
        $values = array_filter(iterator_to_array($period), function (DateTime $date) use ($callback) {
            return call_user_func($callback, DateTime::instance($date));
        });

        // Get the difference result
        $diff = count($values);

        // Return the difference result, inverse the value if needed
        return $inverse && !$absolute ? ($diff * -1) : $diff;
    }

    /**
     * Get the difference between this and the specified date in years.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in years.
     *
     * @return int|null The difference in years or null on failure.
     */
    public function diffInYears($dateTime = null, $absolute = true) {
        // Parse the date and time, return null on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            return null;

        // Get the difference and make sure it's valid
        if(($difference = $this->diff($dateTime, $absolute)) === false)
            return null;

        // Get and return the difference in years
        return (int) $difference->format('%r%y');
    }

    /**
     * Get the difference between this and the specified date in months.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in months.
     *
     * @return int|null The difference in months or null on failure.
     */
    public function diffInMonths($dateTime = null, $absolute = true) {
        // Parse the date and time, return null on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            return null;

        // Get the differences and make sure it's valid
        if(($differenceYears = $this->diffInYears($dateTime, $absolute)) === null)
            return null;
        if(($difference = $this->diff($dateTime, $absolute)) === false)
            return null;

        // Get and return the difference in months
        return $differenceYears * static::MONTHS_PER_YEAR + (int) $difference->format('%r%m');
    }

    /**
     * Get the difference between this and the specified date in weeks.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in weeks.
     *
     * @return int|null The difference in weeks or null on failure.
     */
    public function diffInWeeks($dateTime = null, $absolute = true) {
        // Get the difference in days, and make sure it's valid
        if(($differenceDays = $this->diffInDays($dateTime, $absolute)) === null)
            return null;

        // Get and return the difference in weeks
        return (int) ($differenceDays / static::DAYS_PER_WEEK);
    }

    /**
     * Get the difference in weekdays.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param boolean $absolute Get the absolute of the difference.
     *
     * @return int The difference in weekdays, or null on failure.
     */
    public function diffInWeekdays($dateTime = null, $absolute = true) {
        return $this->diffInDaysFiltered(function (DateTime $date) {
            return $date->isWeekday();
        }, $dateTime, $absolute);
    }

    /**
     * Get the difference in weekend days.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param boolean $absolute Get the absolute of the difference.
     *
     * @return int The difference in weekend days, or null on failure.
     */
    public function diffInWeekendDays($dateTime = null, $absolute = true) {
        return $this->diffInDaysFiltered(function (DateTime $date) {
            return $date->isWeekend();
        }, $dateTime, $absolute);
    }

    /**
     * Get the difference between this and the specified date in days.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in days.
     *
     * @return int|null The difference in days or null on failure.
     */
    public function diffInDays($dateTime = null, $absolute = true) {
        // Parse the date and time, return null on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            return null;

        // Get the difference and make sure it's valid
        if(($difference = $this->diff($dateTime, $absolute)) === false)
            return null;

        // Get and return the difference in days
        return (int) $difference->format('%r%a');
    }

    /**
     * Get the difference in days using a filter closure
     *
     * @param Closure $callback The callback function to call for each day as filter.
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param boolean $absolute Get the absolute of the difference.
     *
     * @return int|null The difference in days. Null will be returned on failure.
     */
    public function diffInDaysFiltered(Closure $callback, $dateTime = null, $absolute = true) {
        return $this->diffFiltered(DateInterval::createDay(), $callback, $dateTime, $absolute);
    }

    /**
     * Get the difference between this and the specified date in hours.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in hours.
     *
     * @return int|null The difference in hours or null on failure.
     */
    public function diffInHours($dateTime = null, $absolute = true) {
        // Parse the date and time, return null on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            return null;

        // Get the difference in seconds and make sure it's valid
        if(($differenceSeconds = $this->diffInSeconds($dateTime, $absolute)) === null)
            return null;

        // Get and return the difference in hours
        return (int) ($differenceSeconds / static::SECONDS_PER_MINUTE / static::MINUTES_PER_HOUR);
    }

    /**
     * Get the difference in hours using a filter closure
     *
     * @param Closure $callback The callback function to call for each hour as filter.
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param boolean $absolute Get the absolute of the difference.
     *
     * @return int|null The difference in hours. Null will be returned on failure.
     */
    public function diffInHoursFiltered(Closure $callback, $dateTime = null, $absolute = true) {
        return $this->diffFiltered(DateInterval::createHour(), $callback, $dateTime, $absolute);
    }

    /**
     * Get the difference between this and the specified date in minutes.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in minutes.
     *
     * @return int|null The difference in minutes or null on failure.
     */
    public function diffInMinutes($dateTime = null, $absolute = true) {
        // Parse the date and time, return null on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            return null;

        // Get the difference in seconds, and make sure it's valid
        if(($differenceSeconds = $this->diffInSeconds($dateTime, $absolute)) === null)
            return null;

        // Get and return the difference in minutes
        return (int) ($differenceSeconds / static::SECONDS_PER_MINUTE);
    }

    /**
     * Get the difference between this and the specified date in seconds.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] A DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     * @param boolean $absolute [optional] Get the absolute of the difference in seconds.
     *
     * @return int|null The difference in seconds or null on failure.
     */
    public function diffInSeconds($dateTime = null, $absolute = true) {
        // Parse the date and time, return null on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            return null;

        // Calculate the timestamp difference
        $timestampDifference = $dateTime->getTimestamp() - $this->getTimestamp();

        // Return the result in absolute or regular form
        return $absolute ? abs($timestampDifference) : $timestampDifference;
    }

    /**
     * Get the number of seconds since midnight.
     *
     * @return int The number of seconds.
     */
    public function secondsSinceMidnight() {
        return $this->diffInSeconds($this->copy()->startOfDay());
    }

    /**
     * Get the number of seconds until the end of the day, which is 23:23:59.
     *
     * @return int The number of seconds.
     */
    public function secondsUntilEndOfDay() {
        return $this->diffInSeconds($this->copy()->endOfDay());
    }

    /**
     * Set the time to the start of the day, which is 00:00:00.
     *
     * @return static The DateTime instance.
     */
    public function startOfDay() {
        return $this->setHour(0)->setMinute(0)->setSecond(0);
    }

    /**
     * Set the time to the end of the day, which is 23:59:59.
     *
     * @return static The DateTime instance.
     */
    public function endOfDay() {
        return $this->setHour(23)->setMinute(59)->setSecond(59);
    }

    /**
     * Reset the date to the first day of the month and the time to the beginning of that day, which is 00:00:00.
     *
     * @return static The DateTime instance.
     */
    public function startOfMonth() {
        return $this->setDay(1)->startOfDay();
    }

    /**
     * Resets the date to the last day of the month and the time to the end of that day, which is 23:59:59.
     *
     * @return static The DateTime instance.
     */
    public function endOfMonth() {
        return $this->setDay($this->daysInMonth)->endOfDay();
    }

    /**
     * Resets the date to the start of the year and the time to the beginning of that day, which is 00:00:00.
     *
     * @return static The DateTime instance.
     */
    public function startOfYear() {
        return $this->setMonth(1)->startOfMonth();
    }

    /**
     * Resets the date to the end of t he year and the time to the end of that day, which is 23:59:59.
     *
     * @return static The DateTime instance.
     */
    public function endOfYear() {
        return $this->setMonth(static::MONTHS_PER_YEAR)->endOfMonth();
    }

    /**
     * Resets the date to the start of the decade and the time to the beginning of that day, which is 00:00:00.
     *
     * @return static The DateTime instance.
     */
    public function startOfDecade() {
        return $this->startOfYear()->setYear($this->year - ($this->year % static::YEARS_PER_DECADE));
    }

    /**
     * Resets the date to the end of the decade and the time to the end of that day, which is 23:59:59.
     *
     * @return static
     */
    public function endOfDecade() {
        return $this->endOfYear()->setYear($this->year -
            ($this->year % static::YEARS_PER_DECADE + static::YEARS_PER_DECADE - 1));
    }

    /**
     * Resets the date to the start of the century and the time to the beginning of that day, which is 00:00:00.
     *
     * @return static The DateTime instance.
     */
    public function startOfCentury() {
        return $this->startOfYear()->setYear($this->year - ($this->year % static::YEARS_PER_CENTURY));
    }

    /**
     * Resets the date to the end of the century and the time to the end of that day, which is 23:59:59.
     *
     * @return static The DateTime instance.
     */
    public function endOfCentury() {
        return $this->endOfYear()->setYear($this->year -
            ($this->year % static::YEARS_PER_CENTURY + static::YEARS_PER_CENTURY - 1));
    }

    /**
     * Resets the date to the first day of the ISO-8601 week (Monday) and the time to the beginning of that day, which
     * is 00:00:00.
     *
     * @return static The DateTime instance.
     */
    public function startOfWeek() {
        // Set the date to the first day of the week
        if($this->dayOfWeek != static::MONDAY)
            $this->previous(static::MONDAY);

        // Set the time to the start of the day
        return $this->startOfDay();
    }

    /**
     * Resets the date to the end of the ISO-8601 week (Sunday) and time the end of that day, which is 23:59:59.
     *
     * @return static The DateTime instance.
     */
    public function endOfWeek() {
        // Set the date to the last day of the week
        if($this->dayOfWeek != static::SUNDAY)
            $this->next(static::SUNDAY);

        // Set the time to the end of the day
        return $this->endOfDay();
    }

    /**
     * Modify to the next occurrence of a given day of the week. If no specific day is provided, the next occurrence of
     * the current day of the week is used. This will also reset the time to the start of that day.
     *
     * @param int|null $dayOfWeek [optional] The day of the week, using the day constants such as static::SUNDAY. Or
     *     null to get the next occurrence of the current day.
     *
     * @return static The DateTime instance.
     */
    public function next($dayOfWeek = null) {
        // Use the current day of the week if none was provided
        if($dayOfWeek === null)
            $dayOfWeek = $this->dayOfWeek;

        // Find the next occurrence of the day of the week, and return the result
        return $this->modify('next ' . static::$DAY_NAMES[$dayOfWeek])->startOfDay();
    }

    /**
     * Modify to the previous occurrence of a given day of the week. If no specific day is provided, the previous
     * occurrence of the current day of the week is used. This will also reset the time to the start of that day.
     *
     * @param int|null $dayOfWeek [optional] The day of the week, using the day constants such as static::SUNDAY. Or
     *     null to get the previous occurrence of the current day.
     *
     * @return static The DateTime instance.
     */
    public function previous($dayOfWeek = null) {
        // Use the current day of the week if none was provided
        if($dayOfWeek === null)
            $dayOfWeek = $this->dayOfWeek;

        // Find the previous occurrence of the day of the week, and return the result
        return $this->modify('last ' . static::$DAY_NAMES[$dayOfWeek])->startOfDay();
    }

    /**
     * Modify to the first occurrence of a given day of the week. If no specific day is provided, the first day of the
     * month is used. This will also reset the time to the start of that day.
     *
     * @param int|null $dayOfWeek [optional] The day of the week, using the day constants such as static::SUNDAY. Or
     *     null to get the first day of the month.
     *
     * @return static|null The DateTime instance, or null on failure.
     */
    public function firstOfMonth($dayOfWeek = null) {
        // Use the first day of the week if none was provided
        if($dayOfWeek === null)
            return $this->setDay(1)->startOfDay();

        // Get the first day occurrence in the month, and make sure it's valid
        if(($dateTime = $this->modify('first ' . static::$DAY_NAMES[$dayOfWeek] . ' of ' . $this->format('F') . ' ' .
                $this->year)) === null
        )
            return null;

        // Parse the date and time
        if(($dateTime = static::parse($dateTime)) === null)
            return null;

        // Set the time to the start of the day and return the result
        return $dateTime->startOfDay();
    }

    /**
     * Modify to the last occurrence of a given day of the week. If no specific day is provided, the last day of the
     * month is used. This will also reset the time to the start of that day.
     *
     * @param int|null $dayOfWeek [optional] The day of the week, using the day constants such as static::SUNDAY. Or
     *     null to get the first day of the month.
     *
     * @return static The DateTime instance.
     */
    public function lastOfMonth($dayOfWeek = null) {
        // Use the last day of the month if none was provided
        if($dayOfWeek === null)
            return $this->setDay($this->daysInMonth);

        // Get the last day occurrence in the month
        $dateTime =
            $this->modify('last ' . static::$DAY_NAMES[$dayOfWeek] . ' of ' . $this->format('F') . ' ' . $this->year);

        // Parse the date and time
        if(($dateTime = static::parse($dateTime)) === null)
            return null;

        // Set the time to the start of the day and return the result
        return $dateTime->startOfDay();
    }

    /**
     * Modify to the given occurrence of a given day of the week in the current month.
     * If the given day is outside the current month no modifications are made an null is returned.
     * This will also reset the time to the beginning of the day.
     *
     * @param int $nth The occurrence of the day of the week.
     * @param int $dayOfWeek The day of the week, using the day constants such as static::SUNDAY.
     *
     * @return static|null The DateTime instance, or null on failure.
     */
    public function nthOfMonth($nth, $dayOfWeek) {
        // Get a copy of the date and time set to the first day of the month
        $dateTime = $this->copy()->firstOfMonth();

        // Store the year and month, to use for checking later
        $check = $dateTime->format('Y-m');

        // Add the number of days to the date and time
        if($dateTime->modify('+' . $nth . ' ' . static::$DAY_NAMES[$dayOfWeek]) === null)
            return null;

        // Make sure the year and month are still the same
        if($dateTime->format('Y-m') !== $check)
            return null;

        // Modify and return the date and time
        return $this->modify($dateTime);
    }

    /**
     * Modify to the first occurrence of a given day of the week in the current quarter. If no day of the week if
     * provided, modify to the first day of the current quarter. This will also reset the time to the beginning of that
     * day.
     *
     * @param int|null $dayOfWeek [optional] The day of the week, using the day constants such as static::SUNDAY. Or
     *     null to get the first day of the current quarter.
     *
     * @return static|null The DateTime instance, or null on failure.
     */
    public function firstOfQuarter($dayOfWeek = null) {
        return $this->setDay(1)->setMonth($this->quarter * 3 - 2)->firstOfMonth($dayOfWeek);
    }

    /**
     * Modify to the last occurrence of the given day of the week in the current quarter. If no day of the week is
     * provided, modify to the first day of the current quarter. This will also reset the time to the beginning of that
     * day.
     *
     * @param int|null $dayOfWeek [optional] The day of the week, using day constants such as static::SUNDAY. Or null
     *     to get the last day of the current quarter.
     *
     * @return static|null The DateTime instance, or null on failure.
     */
    public function lastOfQuarter($dayOfWeek = null) {
        return $this->setDay(1)->setMonth($this->quarter * 3)->lastOfMonth($dayOfWeek);
    }

    /**
     * Modify to the given occurrence of a given day of the week in the current quarter.
     * If the given day is outside the current quarter no modifications are made an null is returned.
     * This will also reset the time to the beginning of that day.
     *
     * @param int $nth The occurrence of the day of the week.
     * @param int $dayOfWeek The day of the week, using the day constants such as static::SUNDAY.
     *
     * @return static|null The DateTime instance, or null on failure.
     */
    public function nthOfQuarter($nth, $dayOfWeek) {
        // Get a copy of the date and time set to the first day of the month
        $dateTime = $this->copy()->setDay(1)->setMonth($this->quarter * 3);

        // Get the last month and the year of the quarter
        $last_month = $dateTime->getMonth();
        $year = $dateTime->getYear();

        // Get the nth occurrence of the day of the week in this quarter
        /** @noinspection PhpUndefinedMethodInspection */
        $dateTime->firstOfQuarter()->modify('+' . $nth . ' ' . static::$DAY_NAMES[$dayOfWeek]);

        // Make sure the date is not outside the current quarter
        if($last_month < $dateTime->getMonth() || $year !== $dateTime->getYear())
            return null;

        // Modify the date and time, return the result
        return $this->modify($dateTime);
    }

    /**
     * Modify to the first occurrence of a given day of the week in the current year. If no day of the week is provided
     * the first day of the year is used. This will also reset the time to the beginning of that day.
     *
     * @param int|null $dayOfWeek [optional] The day of the week, using day constants such as static::SUNDAY. Or null
     *     to get the first day of the current year.
     *
     * @return static|null The DateTime instance, or null on failure.
     */
    public function firstOfYear($dayOfWeek = null) {
        return $this->setMonth(1)->firstOfMonth($dayOfWeek);
    }

    /**
     * Modify to the last occurrence of a given day of the week in the current year. If no day of the week is provided
     * the last day of the year is used. This will also reset the time to the beginning of that day.
     *
     * @param int|null $dayOfWeek [optional] The day of the week, using day constants such as static::SUNDAY. Or null
     *     to get the  last day of the current year.
     *
     * @return static|null The DateTime instance, or null on failure.
     */
    public function lastOfYear($dayOfWeek = null) {
        return $this->setMonth(static::MONTHS_PER_YEAR)->lastOfMonth($dayOfWeek);
    }

    /**
     * Modify to the given occurrence of a given day of the week in the current year.
     * If the given day is outside the current year no modifications are made an null is returned.
     * This will also reset the time to the beginning of that day.
     *
     * @param int $nth The occurrence of the day of the week.
     * @param int $dayOfWeek The day of the week, using the day constants such as static::SUNDAY.
     *
     * @return static|null The DateTime instance, or null on failure.
     */
    public function nthOfYear($nth, $dayOfWeek) {
        // Create a copy of the date and time, and get the nth day of the week
        $dateTime = $this->copy()->firstOfYear()->modify('+' . $nth . ' ' . static::$DAY_NAMES[$dayOfWeek]);

        // Make sure the date isn't outside the current year
        if($this->year != $dateTime->year)
            return null;

        // Modify and return the date and time
        return $this->modify($dateTime);
    }

    /**
     * Get the age of the date and time compared to the current date and time specified by the now() method.
     *
     * @return int The age of the date and time.
     */
    public function getAge() {
        return $this->diffInYears(null, false);
    }

    /**
     * Check if it's the birthday. This check whether the month and day are equal to the specified date and time.
     *
     * @param DateTime|PHPDateTime|string|null $birthday [optional] The DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     *
     * @return boolean True if it's the birthday, false otherwise. False is also returned on failure.
     */
    public function isBirthday($birthday) {
        // Parse the date and time, return null on failure
        if(($birthday = static::parse($birthday, $this->getTimezone())) === null)
            return null;

        // Check whether the month and day are equal, return the result
        return StringUtils::equals($this->format('md'), $birthday->format('md'));
    }

    /**
     * Modify the date and time to the average of the date and time and the specified date and time.
     *
     * @param DateTime|PHPDateTime|string|null $dateTime [optional] The DateTime or PHPDateTime instance, the date and
     *     time as a string or null to use the now() date and time.
     *
     * @return static|null The DateTime instance, or null on failure.
     */
    public function average($dateTime = null) {
        // Parse the date and time, return null on failure
        if(($dateTime = static::parse($dateTime, $this->getTimezone())) === null)
            return null;

        // Calculate the difference in seconds and make sure it's valid
        if(($differenceSeconds = $this->diffInSeconds($dateTime, false)) === null)
            return null;

        // Apply the average difference and return the result
        return $this->addSeconds((int) ($differenceSeconds / 2));
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
     * @param float|null $latitude [optional] The latitude, or null to use the default latitude specified by
     *     'date.default_latitude' in PHPs INI.
     * @param float|null $longitude [optional] The longitude, or null to use the default longitude specified by
     *     'date.default_longitude' in PHPs INI.
     *
     * @return Array|null An array with information about sunset/sunrise and twilight begin/end, or null on failure.
     */
    public function getSunInfo($latitude = null, $longitude = null) {
        // Get the timestamp
        $timestamp = $this->copy()->startOfDay()->getTimestamp();

        // Parse the latitude and longitudes
        if($latitude === null)
            $latitude = ini_get('date.default_latitude');
        if($longitude === null)
            $longitude = ini_get('date.default_longitude');

        // Get the sun info array, return null on failure
        if(($sunInfo = date_sun_info($timestamp, $latitude, $longitude)) === false)
            return null;

        // Replace the timestamps with DateTime objects
        foreach($sunInfo as $key => &$value)
            $value = new self($value);

        // Return the array with the sun info
        return $sunInfo;
    }

    /**
     * Get the sunrise time at the specified time for a given location.
     *
     * @param float|null $latitude [optional] The latitude, or null to use the default latitude specified by
     *     'date.default_latitude' in PHPs INI.
     * @param float|null $longitude [optional] The longitude, or null to use the default longitude specified by
     *     'date.default_longitude' in PHPs INI.
     * @param float|null $zenith [optional] The sunrise zenith, or null to use the default value specified by
     *     'date.sunrise_zenith' in PHPs INI.
     *
     * @return DateTime|null The sunrise time as DateTime object, or null on failure.
     *
     * @throws Exception Throws an exception on failure.
     */
    public function getSunrise($latitude = null, $longitude = null, $zenith = null) {
        // Get the timestamp
        $timestamp = $this->copy()->startOfDay()->getTimestamp();

        // Parse the latitude and longitudes
        if($latitude === null)
            $latitude = ini_get('date.default_latitude');
        if($longitude === null)
            $longitude = ini_get('date.default_longitude');

        // Parse the zenith
        if($zenith === null)
            $zenith = ini_get('date.sunrise_zenith');

        // Get the sunrise information, return null on failure
        if(($sunriseTimestamp = date_sunrise($timestamp, SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, $zenith)) === false)
            return null;

        // Create and return a DateTime object based on the timestamp, return the result
        return static::createFromTimestamp($sunriseTimestamp, $this->getTimezone());
    }

    /**
     * Get the sunset time at the specified time for a given location.
     *
     * @param float|null $latitude [optional] The latitude, or null to use the default latitude specified by
     *     'date.default_latitude' in PHPs INI.
     * @param float|null $longitude [optional] The longitude, or null to use the default longitude specified by
     *     'date.default_longitude' in PHPs INI.
     * @param float|null $zenith [optional] The sunset zenith, or null to use the default value specified by
     *     'date.sunset_zenith' in PHPs INI.
     *
     * @return DateTime|null The sunset time as DateTime object, or null on failure.
     *
     * @throws Exception Throws an exception on failure.
     */
    public function getSunset($latitude = null, $longitude = null, $zenith = null) {
        // Get the timestamp
        $timestamp = $this->copy()->startOfDay()->getTimestamp();

        // Parse the latitude and longitudes
        if($latitude === null)
            $latitude = ini_get('date.default_latitude');
        if($longitude === null)
            $longitude = ini_get('date.default_longitude');

        // Parse the zenith
        if($zenith === null)
            $zenith = ini_get('date.sunset_zenith');

        // Get the sunset information, return null on failure
        if(($sunsetTimestamp = date_sunset($timestamp, SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, $zenith)) === false)
            return null;

        // Create and return a DateTime object based on the timestamp, return the result
        return static::createFromTimestamp($sunsetTimestamp, $this->getTimezone());
    }

    /**
     * Get the preferred format to return the date and time in by default when using format and toString methods.
     *
     * @return string The preferred date and time format.
     */
    public function getPreferredFormat() {
        return self::$preferredFormat;
    }

    /**
     * Set the preferred format to return the date and time in by default when using format and toString methods.
     *
     * @param string|null $preferredFormat The preferred format, or null to use the default format.
     *
     * @return string The preferred date and time format.
     */
    public function setPreferredFormat($preferredFormat = null) {
        // Reset the default format
        if($preferredFormat === null)
            $preferredFormat = self::DEFAULT_FORMAT;

        // Make sure the format is a string, or throw an exception
        if(!is_string($preferredFormat))
            throw new DomainException('The format must be a string or null');

        // Set the preferred format
        self::$preferredFormat;
    }

    /**
     * Get the date and time as a string formatted according to given format.
     *
     * @param string|null $format [optional] The desired format for the date and time, or null to use the default
     *     format.
     *
     * @return string|null The date and time as a string, or null on failure.
     */
    public function format($format = null) {
        // Use the default format if the format parameter is null
        if($format === null)
            $format = self::getPreferredFormat();

        // Get and return the date and time with the proper format, return null on failure
        return ($result = parent::format($format)) === false ? null : $result;
    }

    /**
     * Format the date and time as a string.
     *
     * @param string|null $format [optional] The format to return the date and time with as a string.
     *
     * @return string|null The date and time as a string, or null on failure.
     */
    public function toString($format = null) {
        // Use the default format if it's set to null
        if($format === null)
            $format = self::getPreferredFormat();

        // Get the date and time as a string with the proper format and return the result, return null on failure
        return ($result = $this->format($format)) === false ? null : $result;
    }

    /**
     * Format the date and time as a string.
     *
     * @return string The date and time as a string, with the default format.
     */
    public function __toString() {
        // Get the date and time as a string, return an empty string on failure
        return ($result = $this->toString()) === null ? '' : $result;
    }

    /**
     * Format the date as a string.
     *
     * @return string The date as a string.
     */
    public function toDateString() {
        return $this->format(static::DEFAULT_FORMAT_DATE);
    }

    /**
     * Format the time as a string.
     *
     * @return string The time as a string.
     */
    public function toTimeString() {
        return $this->format(static::DEFAULT_FORMAT_TIME);
    }

    /**
     * Convert the date and time into a string with complete formatting.
     *
     * @return string|null The date and time as a string, or null on failure.
     */
    public function toCompleteString() {
        return $this->toString(static::DEFAULT_FORMAT_COMPLETE);
    }
}