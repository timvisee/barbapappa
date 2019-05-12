<?php

namespace App\Services;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

/**
 * A service to provide the logo in a view.
 *
 * Class LogoService
 * @package App\Services
 */
class LogoService {

    /**
     * The base path for the logo assets .
     */
    const BASE_PATH = 'img/logo/';

    /**
     * The default logo file that is wrapped.
     */
    const LOGO_WRAP = 'logo_wrap.svg';

    /**
     * The default logo file that is not wrapped.
     */
    const LOGO_NOWRAP = 'logo_nowrap.svg';

    /**
     * Application instance.
     * @var Application
     */
    private $app;

    /**
     * Logo provider service constructor.
     *
     * @param Application $app Application instance.
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }

    /**
     * Get the filename for the logo.
     *
     * @param bool $wrap=false True to get the wrapped logo, false to get the unwrapped.
     * @return string Logo filename.
     */
    private function getLogoFilename($wrap = false) {
        // Build the translation node
        $logoFilenameNode = 'app.logo.' . ($wrap ? '' : 'no') . 'wrapFilename';

        // Return the file name from a language file
        if (Lang::has($logoFilenameNode))
            return trans($logoFilenameNode);

        // Return the default
        return $wrap ? self::LOGO_WRAP : self::LOGO_NOWRAP;
    }

    /**
     * Get the asset path for the logo.
     *
     * @param bool $wrap=false True to get the wrapped logo, false to get the unwrapped.
     * @return string Logo asset path.
     */
    private function getLogoAssetPath($wrap) {
        // Build the translation node
        $pathNode = 'app.logo.path';

        // Return the custom path, or the default
        return (Lang::has($pathNode) ? trans($pathNode) : self::BASE_PATH)
            . $this->getLogoFilename($wrap);
    }

    /**
     * Alias for {@code getLogoUrl}.
     *
     * Get the public URL for the logo asset.
     *
     * @param bool $wrap=false True to get the wrapped logo, false to get the unwrapped.
     * @return string Public URL for the logo asset.
     */
    public function url($wrap = false) {
        return $this->getLogoUrl($wrap);
    }

    /**
     * Get the public URL for the logo asset.
     *
     * @param bool $wrap=false True to get the wrapped logo, false to get the unwrapped.
     * @return string Public URL for the logo asset.
     */
    public function getLogoUrl($wrap = false) {
        return asset($this->getLogoAssetPath($wrap));
    }

    /**
     * Alias for {@code getLogoElement}.
     *
     * Get the HTML element to render the application logo.
     *
     * @param bool $wrap=false True to render the wrapped logo, false to render the unwrapped.
     * @param array $attributes=[] An array with optional attributes.
     *
     * @return HtmlString The element as HTML string.
     */
    public function element($wrap = false, $attributes = []) {
        return $this->getLogoElement($wrap, $attributes);
    }

    /**
     * Get the HTML element to render the application logo.
     *
     * @param bool $wrap=false True to render the wrapped logo, false to render the unwrapped.
     * @param array $attributes=[] An array with optional attributes.
     *
     * @return HtmlString The element as HTML string.
     */
    public function getLogoElement($wrap = false, $attributes = []) {
        // Set the source
        $attributes['src'] = $this->getLogoUrl($wrap);

        // Set the alt if not set
        if(array_key_exists('alt', $attributes))
            $attributes['alt'] = config('app.name') . ' ' . __('misc.logo');

        // Build the HTML string and return it
        return new HtmlString('<img' . $this->attributes($attributes) . ' />');
    }

    /**
     * Build the attributes part for an HTML element.
     *
     * @param array $attributes Key-value pair of attributes.
     * @return string A string with all attributes.
     */
    private function attributes($attributes) {
        // Create a list of processed attributes
        $items = [];

        // Format each attribute properly
        foreach((array) $attributes as $key => $value) {
            // Build the element, add it to the list
            $element = $this->attributeElement($key, $value);
            if(!is_null($element))
                $items[] = $element;
        }

        // Merge the items and return
        return count($items) > 0 ? ' ' . implode(' ', $items) : '';
    }

    /**
     * Process a key and value pair into an HTML element attribute.
     *
     * If a number is given as key, it is assumed the value already is in proper attribute format.
     *
     * @param string|number $key The key of the attribute.
     * @param string $value The attribute value.
     * @return string The formatted attribute.
     */
    private function attributeElement($key, $value) {
        // Just return the value if the keys are numeric,
        // in that case the value should already contain the full element
        if(is_numeric($key))
            return $value;

        // Treat boolean attributes as HTML properties
        if(is_bool($value) && $key != 'value')
            return $value ? $key : '';

        // Escape and render if not null
        return !is_null($value) ? ($key . '="' . e($value) . '"') : null;
    }
}