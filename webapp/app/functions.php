<?php

use App\Perms\Builder\Config as PermsConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;

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
            return app('perms')->evaluate($config, $request !== null ? $request : request());
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

// Custom function to render balance
if(!function_exists('balance')) {
    /**
     * Format the given balance as human readable text using the proper
     * currency format.
     *
     * @param decimal $balance The balance.
     * @param string $currency The currency code, such as `USD` or `EUR`.
     * @param int [$format=BALANCE_FORMAT_PLAIN] The balance formatting rules.
     * @param array [$options=null] An array of options.
     *
     * @return string Formatted balance.
     */
    function balance($balance, $currency, $format = BALANCE_FORMAT_PLAIN, $options = []) {
        // Take parameters out of options, use defaults
        $prefix = $options['prefix'] ?? null;
        $neutral = $options['neutral'] ?? false;
        $color = $options['color'] ?? true;

        // If neutrally formatting, always show positive number
        if($neutral)
            $balance = abs($balance);

        // Format the balance
        $out = currency_format($balance, $currency);

        // Prefix
        if(!empty($prefix))
            $out = $prefix . $out;

        // Add color for negative values
        switch($format) {
            case null:
            case BALANCE_FORMAT_PLAIN:
                break;
            case BALANCE_FORMAT_COLOR:
                if(!$color) {}
                else if($neutral)
                    // TODO: style instead of giving an explicit neutral color
                    $out = '<span style="color: #2185d0;">' . $out . '</span>';
                else if($balance < 0)
                    $out = '<span style="color: red;">' . $out . '</span>';
                else if($balance > 0)
                    $out = '<span style="color: green;">' . $out . '</span>';
                break;
            case BALANCE_FORMAT_LABEL:
                // TODO: may want to add horizontal class to labels
                if(!$color)
                    $out = '<div class="ui label">' . $out . '</div>';
                else if($neutral)
                    $out = '<div class="ui blue label">' . $out . '</div>';
                else if($balance < 0)
                    $out = '<div class="ui red label">' . $out . '</div>';
                else if($balance > 0)
                    $out = '<div class="ui green label">' . $out . '</div>';
                else
                    $out = '<div class="ui label">' . $out . '</div>';
                break;
            default:
                throw new \Exception("Invalid balance format type given");
        }

        return $out;
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
