<?php

namespace carbon\core\util;

// Prevent direct requests to this set_file due to security reasons
use Closure;

defined('CARBON_CORE_INIT') or die('Access denied!');

class Utils {

    // TODO: Move all these methods to a proper utilities class!

    /**
     * Determine whether a closure is valid. This is an approximation and may return an incorrect result.
     *
     * @param mixed $closure The closure to check.
     *
     * @return bool True if the object is a valid closure, false otherwise.
     */
    public static function isValidClosure($closure) {
        return is_object($closure) && ($closure instanceof Closure);
    }
}
