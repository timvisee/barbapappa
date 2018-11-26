<?php

namespace App\Utils;

use App\Helpers\ValidationDefaults;

class SlugUtils {

    /**
     * Check whether the given string is a valid slug.
     * Note: this does not check whether the given slug exists in the database.
     *
     * @param string $string The string to check.
     * @return bool True if a slug, false if not.
     */
    public static function isValid($string) {
        return preg_match(ValidationDefaults::SLUG_REGEX, $string) == 1;
    }
}
