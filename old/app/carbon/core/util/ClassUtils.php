<?php

/**
 * ClassUtils.php
 * ClassUtils class for Carbon Core.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2014, All rights reserved.
 */

namespace carbon\core\util;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * ClassUtils class
 *
 * @package carbon\core\util
 * @author Tim Visee
 */
class ClassUtils {

    /**
     * Get the namespace of a class
     *
     * @param string $class Class with namespace.
     *
     * @return string|null The class' namespace. An empty string will be returned if the class doesn't have a namespace.
     * Null will be returned on failure.
     */
    public static function getNamespace($class) {
        // Make sure the $class param is a string
        if(!is_string($class))
            return null;

        // Trim the class name and make sure it's at least one character long
        $class = trim($class);
        if(strlen($class) < 1)
            return null;

        // Get the position of the last namespace separator
        $pos = strrpos($class, '\\');

        // Make sure any namespace separator was found, if not,
        // return an empty string because the class doesn't have a namespace.
        if($pos === false)
            return '';

        // Return the namespace of the class
        return substr($class, 0, $pos);
    }

    /**
     * Get the name of a class, without the namespace.
     *
     * @param string $class Class with namespace.
     *
     * @return string|null The class' name, without the namespace. Null will be returned on failure.
     */
    public static function getClassName($class) {
        // Make sure the $class param is a string
        if(!is_string($class))
            return null;

        // Trim the class name and make sure it's at least one character long
        $class = trim($class);
        if(strlen($class) < 1)
            return null;

        // Get the position of the last namespace separator
        $pos = strrpos($class, '\\');

        // Make sure any namespace separator was found, if not, return the class
        if($pos === false)
            return $class;

        // Return the name of the class.
        return substr($class, $pos + 1);
    }
}