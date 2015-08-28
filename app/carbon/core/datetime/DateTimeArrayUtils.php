<?php

/**
 * DateTimeArrayUtils.php
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Carbon CMS 2015. All rights reserved.
 */

namespace carbon\core\datetime;

use carbon\core\datetime\zone\DateTimeZone;
use carbon\core\datetime\zone\DateTimeZoneArrayUtils;
use carbon\core\util\Utils;
use Closure;
use DateTime as PHPDateTime;
use DateTimeZone as PHPDateTimeZone;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Class DateTimeArrayUtils
 *
 * @package carbon\core\datetime
 */
class DateTimeArrayUtils {

    // TODO: Review this whole class, it's a mess right now!
    // TODO: Create all methods for this class!
    // TODO: Create these methods while they're needed throughout the Carbon CORE/CMS project.

    /**
     * Parse an array of date and time objects with optional time zones. A new instance will be created for each object
     * if required.
     *
     * If the $dateTimes parameter is a DateTime zone instance, the instance will be returned and the $timezone
     * parameter is ignored. If the $dateTime parameter is anything other than a DateTime zone the date, time and the
     * time zone is parsed through the constructor.
     *
     * @param Array|DateTime|string|null $dateTimes [optional] An array of DateTime or PHPDateTime instance, the time
     *     as a string, or null to use the now() time. A single instance will be converted into an array. The array may
     *     be recursive.
     * @param Array|DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $timezones [optional] An array of the
     *     time zones the specified date and times are in, or null to use the default time zones if the $time param
     *     isn't a DateTime instance. A DateTime or PHPDateTime instance to use it's timezone. The number of timezones
     *     must be equal to the number of date and time objects. If only one timezone is given the timezone will be
     *     used for all date and time objects. The array may be recursive.
     * @param bool $recursive [optional] True to recursively parse the array, false if not and leave recursive arrays
     *     the way they are.
     * @param bool $flat [optional] True to force the returned array to be flat, false to keep the same levels of the given array.
     * @param bool $all [optional] True to require all date time objects to be parsed successfully, false if not.
     * @param mixed|null $default [optional] The default value returned on failure. This object is also put in the
     *     array as results for objects that couldn't be parsed if $all is false.
     *
     * @return Array|mixed An array of DateTime instances or default values, or just the default value on failure.
     */
    public static function parseArray($dateTimes = null, $timezones = null, $recursive = true, $flat = false,
                                      $all = false, $default = null) {
        // Make sure sure the date and time and the timezone parameters are arrays
        if(!is_array($dateTimes))
            $dateTimes = Array($dateTimes);

        // Parse the timezones array, return the default value on failure
        if(($timezones = DateTimeZoneArrayUtils::parseArray($timezones, $all, $recursive, null)) === null)
            return $default;

        // Make sure the number of timezones is equal to one or the number of date and time objects
        $dateTimesCount = count($dateTimes);
        $timezonesCount = count($timezones);
        if($timezonesCount != 1 && $timezonesCount != $dateTimesCount)
            return $default;

        // Parse each date and time
        foreach($dateTimes as $key => &$value) {
            // Get the timezone for the current object
            $timezone = $timezonesCount > 1 ? $timezones[$key] : $timezones[0];

            // Make sure the object isn't an array by itself
            if(is_array($value)) {
                // Parse the array if recursive mode is enabled, return the default value on failure
                if($recursive) {
                    // Make the array flat if required
                    if($flat)
                        array_splice($dateTimes, $key, 1, $value);

                    // Parse the value
                    if(($value = static::parseArray($value, $timezone, $recursive, $flat, $all, $default)) === $default && $all)
                        return $default;
                }

                // Continue to the next element
                continue;
            }

            // Parse the current object, return the default value if the parsing failed while all objects must be parsed
            if(($value = DateTimeUtils::parse($value, $timezone, $default)) === $default && $all)
                return $default;
        }

        // Return the result
        return $dateTimes;
    }

    /**
     * Parse a single DateTime object in an array.
     *
     * @param Array $dateTimes The array of DateTime objects.
     * @param int $offset [optional] The offset of the object to parse.
     * @param bool $recursive [optional] True to parse all values recursively if the item specified by the offset is an array. False to keep these arrays the way they are.
     * @param bool $flat [optional] True to flatten sub-arrays and put them in the main array, false if not.
     *
     * @return bool True of the object was parsed successfully, false otherwise.
     */
    // TODO: Should we add a timezones parameter?
    private static function parseArrayItem(&$dateTimes, $offset = 0, $recursive = true, $flat = false) {
        // Make sure dateTimes is an array, and that the offset is in-bound, return false if that's not the case
        if(!is_array($dateTimes) || count($dateTimes) <= $offset)
            return false;

        // Get the current item
        $item = &$dateTimes[$offset];

        // Check whether the item is an array
        if(is_array($item)) {
            // Should we parse the array recursively
            if($recursive)
                // Parse the array, return false on failure
                if(($item = static::parseArray($item, null, true, $flat, true, null)) === null)
                    return false;

            // Should we make the array flat
            if($flat) {
                do {
                    // Flatten the array
                    array_splice($dateTimes, $offset, 1, $item);

                    // Reassign the item
                    $item = &$dateTimes[$offset];

                    // Parse the first flattened item
                    if(!is_array($item))
                        if(($item = DateTime::parse($item)) === null)
                            return false;

                } while(is_array($item));
            }

            // Parsed successfully, return true
            return true;
        }

        // Parse the item, return true on success and false on failure
        return ($item = DateTime::parse($item)) !== null;
    }

    /**
     * Check whether the result for the given sets is true based on the specified closure filter.
     * By default true will be returned if any object set has the result true, if <var>$all</var> is set to true all
     * object sets must have a result of true. False is returned otherwise.
     *
     * The number of objects in all sets must be equal, some sets may equal one.
     * If the number of objects in all sets equals, all objects will be compared to the object in the other set at the
     * same set index. If a set contains one object, this one object will be compared to the relevant objects of the
     * other sets.
     *
     * A non-array object may be given which is then converted into an array.
     *
     * The closure filter one argument, which is an array of all the relevant DateTime objects for that iteration. The
     * number of objects in this array equals the number of available sets. The filter must return true if the result
     * is correct, or false if not. Null may be returned on failure.
     *
     * @param Array $sets [optional] An array containing all sets as arrays, with values accepted by
     *     DateTimeArrayUtils::parse().
     * @param Closure $callback The filter to run for each object set.
     * @param bool $all [optional] True to require all object sets to return true, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if any of the iterations gives a result of true, if <var>$all</var> is set to true all
     *     iterations must give a result of true to return true, false will be returned otherwise. The default value
     *     will be returned on failure.
     */
    public static function compareSets($sets, Closure $callback, $all = true, $default = null) {
        // Make sure the sets parameter is an array
        if(!is_array($sets))
            return $default;

        // Make sure all sets are arrays
        foreach($sets as &$value)
            if(!is_array($value))
                $value = Array($value);

        // Unset the value reference to prevent accidental modifications
        unset($value);

        // Make sure the closure is valid
        if(!Utils::isValidClosure($callback))
            return $default;

        // Count the values of all sets
        $setCounts = Array();
        foreach($sets as $value)
            $setCounts[] = count($value);
        $countMax = max($setCounts);
        $setCount = count($sets);

        // Make sure all counts equal the maximum count or one
        foreach($setCounts as $count)
            if($count != 1 && $count != $countMax)
                return $default;

        // Define the variables for all set values
        $setValues = array_fill(0, $setCount, null);

        // Predefine and parse sets with a single element for better performance
        for($valueIndex = 0; $valueIndex < $setCount; $valueIndex++)
            if($setCounts[$valueIndex] == 1)
                if(($setValues[$valueIndex] = DateTime::parse($sets[$valueIndex], null)) === null)
                    return $default;

        // Loop through the objects
        for($valueIndex = 0; $valueIndex < $countMax; $valueIndex++) {
            // Update all values for the sets that have more than one item
            foreach($sets as $setIndex => $value)
                if($setCounts[$setIndex] > 1)
                    $setValues[$setIndex] = $value[$valueIndex];

            // Check whether any of the set values is an array
            $isArray = false;
            foreach($setValues as $value) {
                if(is_array($value)) {
                    $isArray = true;
                    break;
                }
            }

            // Handle recursive arrays
            if($isArray) {
                // Get the result from the recursive array
                $result = static::compareSets($setValues, $callback, $all, null);

                // Return the default value if the result is invalid
                if($result === null)
                    return $default;

            } else {
                // Parse all set values that have sets with more than one item, return the default value on failure
                foreach($setValues as $setIndex2 => &$setValue2)
                    if($setCounts[$setIndex2] > 1)
                        if(($setValue2 = DateTime::parse($setValue2, null)) === null)
                            return $default;

                // Call the closure to get the result
                $result = call_user_func($callback, $setValues);

                // Make sure the value is boolean, or return the default value
                if(!is_bool($result))
                    return $default;
            }

            // Return false if the result is false while everything must be true
            if(!$result && $all)
                return false;

            // Return true if the result is true while anything must be true
            if($result && !$all)
                return true;
        }

        // Return the result
        return (bool) $all;
    }

    // TODO: Only equal one way, not two!
    // TODO: Three equal objects returns 6, fix this!
    public static function equalsCount($dateTimes, $maxCount = -1, $default = null) {
        // Make sure the dateTimes parameter is an array
        if(!is_array($dateTimes))
            return $default;

        // Define a variable to store the last parsed index, and the number of equal objects
        $lastParsed = -1;
        $equals = 0;

        // Loop through all objects, except the last one
        for($i = 0; $i < count($dateTimes) - 1; $i++) {
            // Get the object as a
            $a = &$dateTimes[$i];

            // Parse this object if it hasn't been parsed yet
            if($i > $lastParsed) {
                // Parse the item, return the default value on failure
                if(!static::parseArrayItem($dateTimes, $i, false, true))
                    return $default;

                // Set the last parsed object
                $lastParsed = $i;
            }

            // Loop through all the remaining objects and check whether any equals
            for($j = 0; $j < count($dateTimes); $j++) {
                // Get the second object as b
                $b = &$dateTimes[$j];

                // Parse this object if it hasn't been parsed yet
                if($j > $lastParsed) {
                    // Parse the item, return the default value on failure
                    if(!static::parseArrayItem($dateTimes, $j, false, true))
                        return $default;

                    // Set the last parsed object
                    $lastParsed = $j;
                }

                // Check whether both objects equal
                /** @noinspection PhpUndefinedMethodInspection */
                if($a->equals($b))
                    $equals++;

                // Check whether the maximum equal count is reached, return the count if that's the case
                if($maxCount >= 0 && $equals >= $maxCount)
                    return $equals;
            }
        }

        // Return the number of equal objects
        return $equals;
    }

    /**
     * Check whether date and time sets given as a and b are equal.
     * By default true will be returned if any object set equals, if <var>$all</var> is set to true all object sets
     * must equal to return true. False is returned otherwise.
     *
     * The number of objects in a and b must be equal, or one of the arrays must have one object.
     * If the number of objects in both arrays equals, all objects will be compared to the object in the other array at
     * the same array index. If one of the arrays contains one object, this one object will be compared to all of the
     * objects in the other array.
     *
     * A non-array object may be given which is then converted into an array.
     *
     * This method does support recursive arrays.
     *
     * @param Array|DateTime|PHPDateTime|string|null $a [optional] A value accepted by DateTimeArrayUtils::parse().
     * @param Array|DateTime|PHPDateTime|string|null $b [optional] A value accepted by DateTimeArrayUtils::parse().
     * @param bool $all [optional] True to require all object sets to equal, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if any object set equals, or if all sets equal if <var>$all</var> is set to true. The
     *     default value will be returned on failure.
     */
    public static function equalSets($a = null, $b = null, $all = true, $default = null) {
        // Compare the array items, return the result
        return static::compareSets(Array($a, $b), function($args) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $args[0]->equals($args[1]);
        }, $all, $default);
    }

    // TODO: Should we keep these methods below? Or should we rename to 'set' methods?

    /*/**
     * Check whether date and time sets of a are greater than b.
     * By default true will be returned if any object set of a is greater than b, if <var>$all</var> is set to true all
     * object sets of a must be greater than b. False is returned otherwise.
     *
     * The number of objects in a and b must be equal, or one of the arrays must have one object.
     * If the number of objects in both arrays is greater than, all objects will be compared to the object in the other
     * array at the same array index. If one of the arrays contains one object, this one object will be compared to all
     * of the objects in the other array.
     *
     * A non-array object may be given which is then converted into an array.
     *
     * This method does support recursive arrays.
     *
     * @param Array|DateTime|PHPDateTime|string|null $a [optional] A value accepted by DateTimeArrayUtils::parse().
     * @param Array|DateTime|PHPDateTime|string|null $b [optional] A value accepted by DateTimeArrayUtils::parse().
     * @param bool $all [optional] True to require all object sets to equal, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if any object set of a is greater than b, or if all sets of a are greater than b if
     *     <var>$all</var> is set to true. The default value will be returned on failure.
     * /
    public static function isGreaterThan($a = null, $b = null, $all = true, $default = null) {
        return static::compareSets(Array($a, $b), function($args) {
            /** @noinspection PhpUndefinedMethodInspection * /
            return $args[0]->isGreaterThan($args[1]);
        }, $all, $default);
    }

    /**
     * Check whether date and time sets of a are greater than b.
     * By default true will be returned if any object set of a is less than b, if <var>$all</var> is set to true all
     * object sets of a must be greater than b. False is returned otherwise.
     *
     * The number of objects in a and b must be equal, or one of the arrays must have one object.
     * If the number of objects in both arrays is less than, all objects will be compared to the object in the other
     * array at the same array index. If one of the arrays contains one object, this one object will be compared to all
     * of the objects in the other array.
     *
     * A non-array object may be given which is then converted into an array.
     *
     * This method does support recursive arrays.
     *
     * @param Array|DateTime|PHPDateTime|string|null $a [optional] A value accepted by DateTimeArrayUtils::parse().
     * @param Array|DateTime|PHPDateTime|string|null $b [optional] A value accepted by DateTimeArrayUtils::parse().
     * @param bool $all [optional] True to require all object sets to equal, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if any object set of a is less than b, or if all sets of a are greater than b if
     *     <var>$all</var> is set to true. The default value will be returned on failure.
     * /
    public static function isLessThan($a = null, $b = null, $all = true, $default = null) {
        return static::compareSets(Array($a, $b), function($args) {
            /** @noinspection PhpUndefinedMethodInspection * /
            return $args[0]->isLessThan($args[1]);
        }, $all, $default);
    }

    /**
     * Check whether date and time sets of a are greater than b.
     * By default true will be returned if any object set of a is greater or equal to b, if <var>$all</var> is set to
     * true all object sets of a must be greater than b. False is returned otherwise.
     *
     * The number of objects in a and b must be equal, or one of the arrays must have one object.
     * If the number of objects in both arrays is greater or equal to, all objects will be compared to the object in
     * the other array at the same array index. If one of the arrays contains one object, this one object will be
     * compared to all of the objects in the other array.
     *
     * A non-array object may be given which is then converted into an array.
     *
     * This method does support recursive arrays.
     *
     * @param Array|DateTime|PHPDateTime|string|null $a [optional] A value accepted by DateTimeArrayUtils::parse().
     * @param Array|DateTime|PHPDateTime|string|null $b [optional] A value accepted by DateTimeArrayUtils::parse().
     * @param bool $all [optional] True to require all object sets to equal, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if any object set of a is greater or equal to b, or if all sets of a are greater than b
     *     if <var>$all</var> is set to true. The default value will be returned on failure.
     * /
    public static function isGreaterOrEqualTo($a = null, $b = null, $all = true, $default = null) {
        return static::compareSets(Array($a, $b), function($args) {
            /** @noinspection PhpUndefinedMethodInspection * /
            return $args[0]->isGreaterOrEqualTo($args[1]);
        }, $all, $default);
    }

    /**
     * Check whether date and time sets of a are greater than b.
     * By default true will be returned if any object set of a is less or equal to b, if <var>$all</var> is set to true
     * all object sets of a must be greater than b. False is returned otherwise.
     *
     * The number of objects in a and b must be equal, or one of the arrays must have one object.
     * If the number of objects in both arrays is less or equal to, all objects will be compared to the object in the
     * other array at the same array index. If one of the arrays contains one object, this one object will be compared
     * to all of the objects in the other array.
     *
     * A non-array object may be given which is then converted into an array.
     *
     * This method does support recursive arrays.
     *
     * @param Array|DateTime|PHPDateTime|string|null $a [optional] A value accepted by DateTimeArrayUtils::parse().
     * @param Array|DateTime|PHPDateTime|string|null $b [optional] A value accepted by DateTimeArrayUtils::parse().
     * @param bool $all [optional] True to require all object sets to equal, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if any object set of a is less or equal to b, or if all sets of a are greater than b if
     *     <var>$all</var> is set to true. The default value will be returned on failure.
     * /
    public static function isLessOrEqualTo($a = null, $b = null, $all = true, $default = null) {
        return static::compareSets(Array($a, $b), function($args) {
            /** @noinspection PhpUndefinedMethodInspection * /
            return $args[0]->isLessOrEqualTo($args[1]);
        }, $all, $default);
    }

    /**
     * Check whether date and time sets of a are greater than b.
     * By default true will be returned if any object set of a is less or equal to b, if <var>$all</var> is set to true
     * all object sets of a must be greater than b. False is returned otherwise.
     *
     * The number of objects in a and b must be equal, or one of the arrays must have one object.
     * If the number of objects in both arrays is less or equal to, all objects will be compared to the object in the
     * other array at the same array index. If one of the arrays contains one object, this one object will be compared
     * to all of the objects in the other array.
     *
     * A non-array object may be given which is then converted into an array.
     *
     * This method does support recursive arrays.
     *
     * @param Array|DateTime|PHPDateTime|string|null $dateTime [optional] A value accepted by
     *     DateTimeArrayUtils::parse().
     * @param Array|DateTime|PHPDateTime|string|null $a [optional] A value accepted by DateTimeArrayUtils::parse().
     * @param Array|DateTime|PHPDateTime|string|null $b [optional] A value accepted by DateTimeArrayUtils::parse().
     * @param bool $equals [optional] Defines whether the date and time object may be equal to a or b.
     * @param bool $all [optional] True to require all object sets to equal, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if any object set of a is less or equal to b, or if all sets of a are greater than b if
     *     <var>$all</var> is set to true. The default value will be returned on failure.
     * /
    public static function isBetween($dateTime = null, $a = null, $b = null, $equals = true, $all = true, $default = null) {
        return static::compareSets(Array($dateTime, $a, $b), function($args) use($equals) {
            /** @noinspection PhpUndefinedMethodInspection * /
            return $args[0]->isBetween($args[1], $args[2], $equals);
        }, $all, $default);
    }*/

    // TODO: Should we include other array utility methods? All methods from the utilities class, or just a few?
}