<?php

/**
 * DateTimeZoneArrayUtils.php
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Carbon CMS 2015. All rights reserved.
 */

namespace carbon\core\datetime\zone;

use carbon\core\datetime\DateTime;
use carbon\core\util\Utils;
use Closure;
use DateTime as PHPDateTime;
use DateTimeZone as PHPDateTimeZone;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Class DateTimeZoneArrayUtils
 *
 * @package carbon\core\datetime\zone
 */
class DateTimeZoneArrayUtils {

    // TODO: Create all methods for this class!
    // TODO: Create these methods while they're needed throughout the Carbon CORE/CMS project.

    /**
     * Parse an array of timezone objects. A new instance will be created for each object if required.
     *
     * @param Array|DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $timezones [optional] An array of the
     *     time zones the specified date and times are in, or null to use the default time zones if the $timezones
     *     param isn't a DateTimeZone instance. A DateTime or PHPDateTime instance to use it's timezone. A single
     *     instance will be converted into an array. The array may be recursive.
     * @param bool $all [optional] True to require all timezone objects to be parsed successfully, false if not.
     * @param bool $recursive [optional] True to recursively parse the array, false if not and leave recursive arrays
     *     the way they are.
     * @param mixed|null $default [optional] The default value returned on failure. This object is also put in the
     *     array as results for objects that couldn't be parsed if $all is false.
     *
     * @return Array|mixed An array of DateTimeZone instances or default values, or just the default value on failure.
     */
    public static function parseArray($timezones = null, $all = false, $recursive = true, $default = null) {
        // Make sure sure the timezone parameter is an arrays
        if(!is_array($timezones))
            $timezones = Array($timezones);

        // Parse each timezone
        foreach($timezones as $key => &$value) {
            // Make sure the object isn't an array by itself
            if(is_array($value)) {
                // Parse the array if recursive mode is enabled, return the default value on failure
                if($recursive)
                    if(($value = static::parseArray($value, $all, $recursive, $default)) === $default && $all)
                        return $default;

                // Continue to the next element
                continue;
            }

            // Parse the current object, return the default value if the parsing failed while all objects must be parsed
            if(($value = DateTimeZoneUtils::parse($value, $default)) === $default && $all)
                return $default;
        }

        // Return the result
        return $timezones;
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
     * The closure filter one argument, which is an array of all the relevant DateTimeZone objects for that iteration.
     * The number of objects in this array equals the number of available sets. The filter must return true if the
     * result is correct, or false if not. Null may be returned on failure.
     *
     * @param Array|DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $sets [optional] An array containing
     *     all sets as arrays, with values accepted by DateTimeArrayUtils::parse().
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
                if(($setValues[$valueIndex] = DateTimeZone::parse($sets[$valueIndex])) === null)
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
                        if(($setValue2 = DateTimeZone::parse($setValue2)) === null)
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

    /**
     * Check whether timezone sets given as a and b are equal.
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
     * @param Array|DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $a [optional] A value accepted by
     *     DateTimeZoneArrayUtils::parse().
     * @param Array|DateTimeZone|PHPDateTimeZone|string|DateTime|PHPDateTime|null $b [optional] A value accepted by
     *     DateTimeZoneArrayUtils::parse().
     * @param bool $all [optional] True to require all object sets to equal, false if not.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return bool|mixed True if any object set equals, or if all sets equal if <var>$all</var> is set to true. The
     *     default value will be returned on failure.
     */
    public static function equalSets($a = null, $b = null, $all = true, $default = null) {
        // Compare the array items, return the result
        return static::compareSets(Array($a, $b), function ($args) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $args[0]->equals($args[1]);
        }, $all, $default);
    }
}
