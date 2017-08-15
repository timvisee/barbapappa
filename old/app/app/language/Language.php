<?php

namespace app\language;

// Prevent direct requests to this set_file due to security reasons
use carbon\core\io\filesystem\file\File;
use Exception;

defined('CARBON_CORE_INIT') or die('Access denied!');

class Language {

    /** @var string The language tag. */
    private $tag;
    /** @var array Language file content. */
    private $content;

    /**
     * Constructor.
     *
     * @param File $file Language file to load.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function __construct($file) {
        // Load the language
        $this->load($file);
    }

    /**
     * Get the language tag.
     *
     * @return string Language tag.
    */
    public function getTag() {
        return $this->tag;
    }

    /**
     * Get the parsed content of the language file as an multidimensional array.
     *
     * @return array Content.
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Load the language from a file.
     *
     * @param File $file Language file to load.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function load($file) {
        // Make sure the file a proper instance
        if(!($file instanceof File))
            throw new Exception('Failed to load language file, invalid file!');

        // Make sure the file exists
        if(!$file->isFile())
            throw new Exception('Failed to load language file, the specified file doesn\'t exist!');

        // Gather and set the language tag from the file name, also make sure it's valid
        $tag = trim($file->getBasename($file->getExtension(true)));
        if(strlen($tag) <= 0)
            throw new Exception('An error occurred when loading a language file.');
        $this->tag = $tag;

        // Load the language file, make sure it succeed
        $langContent = parse_ini_file($file->getAbsolutePath(), true, INI_SCANNER_RAW);
        if($langContent === false)
            throw new Exception('An error occurred when loading a language file.');

        // Set the language content
        $this->content = $langContent;
    }

    /**
     * Get a language value.
     *
     * @param string $section The section of the value.
     * @param string $key The key of the value.
     * @param string|mixed|null $default The default returned if the value doesn't exist.
     * Or null to use a representation of the section and key as default value.
     *
     * @return string|mixed The language value, or the default.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function get($section, $key, $default = null) {
        // Make sure the section and key are valid
        if(!is_string($section) || !is_string($key))
            throw new Exception('Unable to get language string, invalid section or key.');

        // Parse the default value
        if($default === null)
            $default = '&lt;' . ((string) $section) . '.' . ((string) $key) . '&gt;';

        // Make sure this section exists
        if(!array_key_exists($section, $this->content))
            return $default;

        // Make sure this key exists
        if(!array_key_exists($key, $this->content[$section]))
            return $default;

        // Get the value
        $val = $this->content[$section][$key];

        // Return the value if it's a string
        if(is_string($val))
            return $val;

        // Return a random value if it's an array
        if(is_array($val))
            return $val[mt_rand(0, sizeof($val) - 1)];

        // Return the raw value
        return $val;
    }

    /**
     * Check whether a language value exists.
     *
     * @param string $section The section of the value.
     * @param string $key The key of the value.
     *
     * @return bool True if the section and key exist.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function has($section, $key) {
        // Make sure the section and key are valid
        if(!is_string($section) || !is_string($key))
            return false;

        // Check whether the section and key exist
        return $this->get($section, $key, false) !== false;
    }
}
