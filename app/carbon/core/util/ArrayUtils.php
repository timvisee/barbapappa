<?php

/**
 * ArrayUtils.php
 * ArrayUtils class for Carbon CMS.
 * Array utilities class.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2013, All rights reserved.
 */

namespace carbon\core\util;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * ArrayUtils class
 *
 * @package core\util
 * @author Tim Visee
 */
class ArrayUtils {

    /**
     * Create a copy of an array.
     *
     * @param array $arr Array to copy.
     *
     * @return array Copy of the array.
     */
    public static function copyArray($arr) {
        return array_merge(Array(), $arr);
    }

    /**
     * Checks whether an array is associative.
     *
     * @param array $arr The array to check
     *
     * @return bool True if the array is associative, false otherwise. May return false if it'statements not a valid array.
     */
    public static function isAssoc($arr) {
        // Make sure the $arr param is a valid array and isn't empty
        if(!is_array($arr) || empty($arr))
            return false;

        $i = 0;
        $arrayKeys = array_keys($arr);
        foreach($arrayKeys as $k) {
            if($k !== $i)
                return true;

            $i++;
        }

        return false;
    }
}