<?php

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use App\Perms\Builder\Config as PermsConfig;

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
