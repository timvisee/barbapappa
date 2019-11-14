<?php

use App\Perms\Builder\Config as PermsConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;

/**
 * Format the balance as plain text.
 */
const BALANCE_FORMAT_PLAIN = 0;

/**
 * Format the balance as colored text, depending on the value.
 */
const BALANCE_FORMAT_COLOR = 1;

/**
 * Format the balance as colored label, depending on the value.
 */
const BALANCE_FORMAT_LABEL = 2;

// Build the barauth function
if(!function_exists('barauth')) {
    /**
     * Get the bar authentication manager singleton instance.
     *
     * @return \App\Services\BarAuthManager
     */
    function barauth() {
        return app('barauth');
    }
}

// Build the lang manager function
if(!function_exists('langManager')) {
    /**
     * Get the language manager service singleton instance.
     *
     * @return \App\Services\LanguageManagerService
     */
    function langManager() {
        return app('langManager');
    }
}

// Build the permissions manager function
if(!function_exists('perms')) {
    /**
     * Get the permissions manager singleton instance.
     * If a configuration is given as parameter, it is immediately evaluated.
     * An optional request parameter may be given as well.
     *
     * @param string|PermsConfig|null [$config] A permissions configuration to
     *      evaluate.
     * @param Request|null [$request] The request to evaluate permissions for.
     *
     * @return \App\Services\PermissionManager
     */
    function perms($config = null, $request = null) {
        if($config !== null)
            return app('perms')->evaluateRequest($config, $request !== null ? $request : request());
        else
            return app('perms');
    }
}

// Build the logo provider function
if(!function_exists('logo')) {
    /**
     * Get the logo provider singleton instance.
     *
     * @return \App\Services\LogoService
     */
    function logo() {
        return app('logo');
    }
}

// Build the random translation function
if(!function_exists('trans_random')) {
    /**
     * Pick a random translation from the given key.
     *
     * @param string $key Translation key to use.
     * @param array $replace=[] Map with elements to replace.
     * @param string|null $locale=null Use the specified locale.
     * @return string Random translation.
     */
    function trans_random($key, array $replace = [], $locale = null) {
        // Get the translator
        $translator = app('translator');

        // Get the options to choose from, then pick a random option
        $options = explode(
            '|',
            $translator->get($key, $replace, $locale)
        );
        $line = $options[array_rand($options)];

        // Note: The following code should be processed by the translator service, but their methods are protected.

        // Just return the line if there are no replacements configured
        if(empty($replace))
            return $line;

        // Sort the replacements
        $replace = (new Collection($replace))->sortBy(function($value, $key) {
            return mb_strlen($key) * -1;
        })->all();

        // Replace the entries in the line
        foreach ($replace as $key => $value)
            $line = str_replace(
                [':'.$key, ':'.Str::upper($key), ':'.Str::ucfirst($key)],
                [$value, Str::upper($value), Str::ucfirst($value)],
                $line
            );

        // Return the formatted line
        return $line;
    }
}

// Custom function to assert a checkbox value
if(!function_exists('is_checked')) {
    /**
     * Check whether a checkbox value represents true or false.
     * Different types of checkboxes may return different kinds of values.
     *
     * For example:
     * `$isChecked = is_checked($request->input('checkbox_name'));`
     *
     * @param string $value The checkbox value to check.
     * @return boolean True if checked, false if not.
     */
    function is_checked($value) {
        return $value != null && ($value == 'true' || $value == 'on' || $value == '1');
    }
}

// Custom function to render yes or no
if(!function_exists('yesno')) {
    /**
     * Print yes or no depending on the given value.
     * The `is_checked` function is used to determine whehter a value should be
     * yes.
     *
     * @param string $value The value.
     * @return string Yes or no.
     */
    function yesno($value) {
        return __('general.' . (is_checked($value) ? 'yes' : 'no'));
    }
}

if(!function_exists('escape_like')) {
    /**
     * Escape special characters for a LIKE query.
     *
     * @param string $value
     * @param string $char
     *
     * @return string
     */
    function escape_like(string $value, string $char = '\\'): string {
        return str_replace(
            [$char, '%', '_'],
            [$char.$char, $char.'%', $char.'_'],
            $value
        );
    }
}

if(!function_exists('rand_float')) {
    /**
     * Generate a random float in [0.0, 1.0).
     *
     * @return Random float in [0.0, 1.0).
     */
    function rand_float() {
        return mt_rand() / mt_getrandmax();
    }
}

if(!function_exists('ref_format')) {
    /**
     * Format the given string in chunks, with spaces in between.
     *
     * @param string $str The string to format.
     * @param int $step Characters per chunk.
     * @param bool [$reverse=false] Reverse chunking.
     *
     * @return string The formatted string.
     */
    function ref_format($str, $step, $reverse = false) {
        if($reverse)
            return rtrim(strrev(chunk_split(strrev($str), $step, ' ')), ' ');
        return rtrim(chunk_split($str, $step, ' '), ' ');
    }
}

if(!function_exists('format_iban')) {
    /**
     * Format the given IBAN in a more readable format.
     *
     * @param string $iban The IBAN to format.
     *
     * @return The formatted IBAN.
     */
    function format_iban($iban) {
        return ref_format($iban, 4);
    }
}

if(!function_exists('format_bic')) {
    /**
     * Format the given BIC in a more readable format.
     *
     * @param string $iban The BIC to format.
     *
     * @return The formatted BIC.
     */
    function format_bic($iban) {
        return ref_format($iban, 4);
    }
}

if(!function_exists('format_payment_reference')) {
    /**
     * Format the given payment reference in a more readable format.
     *
     * @param string $reference The payment reference to format.
     *
     * @return The formatted payment reference.
     */
    function format_payment_reference($reference) {
        return ref_format($reference, 4);
    }
}

if(!function_exists('random_str')) {
    /**
     * Generate a random string, using a cryptographically secure 
     * pseudorandom number generator (random_int)
     *
     * For PHP 7, random_int is a PHP core function
     *
     * @param int $length      How many characters do we want?
     * @param string $keyspace A string of all possible characters
     *                         to select from
     * @return string
     */
    function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i)
            $pieces []= $keyspace[random_int(0, $max)];
        return implode('', $pieces);
    }
}

if(!function_exists('assert_transaction')) {
    /**
     * Assert/require that a database transaction is currently active.
     *
     * @throws \Exception Throws if no database transaction is active right now.
     */
    function assert_transaction() {
        if(DB::transactionLevel() <= 0)
            throw new \Exception('No database transaction active, while this is required');
    }
}

if(!function_exists('add_session_error')) {
    /**
     * Add a custom error for a field to the session.
     *
     * @param string $field The name of the field the error is for.
     * @param string $message The error to show.
     */
    function add_session_error($field, $message) {
        $errors = session('errors') ?? new ViewErrorBag;
        session()->flash(
            'errors',
            $errors->put(
                'default',
                ($errors->getBag('default') ?? new MessagBag)->add($field, $message)
            )
        );
    }
}

if(!function_exists('is_url_secure')) {
    /**
     * Check whether the given URL is secure, and is using HTTPS.
     * If no URL is given, the base URL of this application is checked.
     *
     * @param string|null [$url=null] The URL to check, or null to use the
     *      application base URL.
     *
     * @return bool True if secure, false if not.
     */
    function is_url_secure(string $url = null) {
        return trim(strtolower(parse_url($url ?? url('/'), PHP_URL_SCHEME))) == 'https';
    }
}

if(!function_exists('name_case')) {
    /**
     * Normalize the given name.
     *
     * - Re-capitalize, take inserts into account (especially for last names)
     * - Remove excess white spaces
     *
     * Inspired by: https://www.media-division.com/correct-name-capitalization-in-php/
     *
     * @param string $name The name.
     * @return string The normalized name.
     */
    function name_case($name) {
        // A list of properly cased parts
        $CASED = collect(array(
            "O'", "l'", "d'", 'St.', 'Mc', 'the', 'van', 'het', 'ten', 'den',
            'von', 'und', 'der', 'de', 'da', 'of', 'and', 'III', 'IV', 'VI',
            'VII', 'VIII', 'IX'
        ));

        // Trim whitespace sequences to one space, append space to properly chunk
        $name = preg_replace('/\s+/', ' ', $name) . ' ';

        // Break name up into parts split by name separators
        $parts = preg_split('/( |-|O\'|l\'|d\'|St\\.|Mc)/i', $name, -1, PREG_SPLIT_DELIM_CAPTURE);

        // Chunk parts, use $CASED or uppercase first, remove unfinished chunks
        $name = collect($parts)
            ->chunk(2)
            ->filter(function($part) {
                return $part->count() == 2;
            })
            ->mapSpread(function($part, $separator = null) use($CASED) {
                // Use specified case for separator if set
                $cased = $CASED->first(function($i) use($separator) {
                    return strcasecmp($i, $separator) == 0;
                });
                $separator = $cased ?? $separator;

                // Choose specified part case, or uppercase first as default
                $cased = $CASED->first(function($i) use($part) {
                    return strcasecmp($i, $part) == 0;
                });
                return [$cased ?? ucfirst(strtolower($part)), $separator];
            })
            ->map(function($part) {
                return implode($part);
            })
            ->join('');

        // Trim and return normalized name
        return trim($name);
    }
}

if(!function_exists('normalize_price')) {
    /**
    * Normalize the given price value, into a float.
    *
    * This makes sure the comma and period is parsed well.
    * A string format is converted into 
    *
    * @param string|number|float $price The price.
    * @return float The normalized price.
    *
    * @throws \Exception Throws if the given price value was invalid.
    */
    function normalize_price($price): float {
        return (float) str_replace(',', '.', $price);
    }
}
