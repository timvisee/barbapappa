<?php

namespace App\Helpers;

use Illuminate\Support\HtmlString;
use Illuminate\Support\MessageBag;

/**
 * Class ErrorRenderer.
 *
 * This class helps with rendering errors in a consistent way inside templates.
 *
 * The {@method alert} and {@method inline} methods can be used to render alert or inline styled error messages.
 * The method return HTML as a string if the specified error occurred and is rendered, else an empty string is returned.
 *
 * @package App\Helpers
 */
class ErrorRenderer {

    /**
     * Render errors with the given name in the alert format.
     * The error name usually correspond to field names in forms.
     *
     * If there are any errors, a string is returned with the HTML representing the errors.
     * If there were no errors, an empty string is returned.
     *
     * @param string|null $name The error name to render, if available. If null is given, all errors with any name are rendered.
     * @param integer|null $limit=null The maximum amount of errors to render with this name, null to render all.
     * @param boolean $consume=true True to consume the errors being rendered, this removes them from the list so they aren't rendered somewhere else.
     *
     * @return HtmlString|string An HTML string or regular string to render.
     */
    public static function alert($name, $limit = null, $consume = true) {
        return self::hasError($name)
            ? self::renderErrorView('error.alert', $name, $limit, $consume)
            : '';
    }

    /**
     * Render errors with the given name in the alert format.
     * The error name usually correspond to field names in forms.
     *
     * If there are any errors, a string is returned with the HTML representing the errors.
     * If there were no errors, an empty string is returned.
     *
     * @param string|null $name The error name to render, if available. If null is given, all errors with any name are rendered.
     * @param integer|null $limit=null The maximum amount of errors to render with this name, null to render all.
     * @param boolean $consume=true True to consume the errors being rendered, this removes them from the list so they aren't rendered somewhere else.
     *
     * @return HtmlString|string An HTML string or regular string to render.
     */
    public static function inline($name, $limit = null, $consume = true) {
        return self::hasError($name)
            ? self::renderErrorView('error.inline', $name, $limit, $consume)
            : '';
    }

    /**
     * Get the errors object form the session, if it exists.
     *
     * @return mixed|null Errors object or null if it doesn't exist.
     */
    private static function getErrorsObject() {
        return session('errors');
    }

    /**
     * Check whether this session has any errors.
     *
     * @return bool True if it has any errors, false if not.
     */
    public static function hasErrors() {
        return self::getErrorsObject() != null;
    }

    /**
     * Check whether this session has any error with the given name.
     *
     * @param string|null $name Error name, or null to allow any error name.
     * @return bool True if an error with this name exists, false if not.
     */
    private static function hasError($name) {
        return self::hasErrors() && ($name == null || self::getErrorsObject()->has($name));
    }

    /**
     * Get all errors for the given name.
     *
     * @param string|null $name Error name. Or null to allow all errors.
     * @param integer|null $limit=null The maximum number of errors to render with this name, null to render all.
     * @param boolean $consume=true True to consume the errors being fetched, this removes them from the list so they aren't rendered somewhere else.
     *
     * @return array|null The errors as strings, in an array.
     */
    private static function getErrors($name, $limit = null, $consume = true) {
        // Make sure any errors are available
        if(!self::hasError($name))
            return null;

        // Define a list of errors to use
        $use = [];

        // Use some advanced logic when we also need to consume
        if($consume) {
            // Get the list of all errors and create a keeping bag
            $errors = self::getErrorsObject()->all(':key|:message');
            $keepingBag = new MessageBag();

            // Parse the list of errors
            foreach($errors as $error) {
                // Explode the error
                $exploded = explode('|', $error, 2);

                // Continue if we don't have a message
                if(empty($exploded[1]))
                    continue;

                // Check whether to use this error (check name and limit)
                if(($limit == null || count($use) < $limit) && ($name == null || $exploded[0] == $name))
                    $use[] = $exploded[1];

                else
                    // Add it to the keeping bag instead
                    $keepingBag->add($exploded[0], $exploded[1]);
            }

            // Reset the error bag to the keeping bag
            // TODO: Is it ok that we're only refreshing the 'default' bag when consuming?
            session('errors')->put('default', $keepingBag);

        } else {
            // Get the errors to use
            $use = $name == null
                ? self::getErrorsObject()->all()
                : self::getErrorsObject()->get($name);

            // Limit them
            if($limit != null)
                $use = array_slice($use, 0, $limit);
        }

        // Return the list to use
        return $use;
    }

    /**
     * Render the given view with the given properties.
     *
     * @param string $view Name/path of the view to render.
     * @param array|null $data=null The data to pass into the view, or null for no data.
     *
     * @return HtmlString An HTML string instance to render in a template.
     */
    private static function renderView($view, $data = null) {
        // Build the view
        $view = view($view);

        // Add the data
        if(!empty($data))
            $view = $view->with($data);

        // Render
        return new HtmlString($view->render());
    }

    /**
     * Render a view for a named error.
     * This renders multiple errors when available for this name.
     *
     * @param string $view Name/path of the view to render.
     * @param string|null $name Name of the error to render, or null to render errors with any name.
     * @param integer|null $limit=null The maximum number of errors to render with this name, null to render all.
     * @param boolean $consume=true True to consume the errors being rendered, this removes them from the list so they aren't rendered somewhere else.
     *
     * @return HtmlString An HTML string instance to render in a template.
     */
    private static function renderErrorView($view, $name, $limit = null, $consume = true) {
        // Get the errors and render
        return self::renderView($view, [
            'messages' => self::getErrors($name, $limit, $consume)
        ]);
    }
}