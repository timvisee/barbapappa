<?php

namespace app\database;

use app\language\Language;
use app\language\LanguageManager;
use Exception;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class DatabaseValueTranslations {

    /** @var Array The raw database value. */
    private $values = Array();
    /** @var string|null The default value. */
    private $default = null;

    /**
     * Constructor.
     *
     * @param Array|null $values The raw database value with the translations.
     * @param string|null $default The default value returned on failure.
     */
    public function __construct($values, $default = null) {
        // Set the value and the default
        $this->setValues($values);
        $this->default = $default;
    }

    /**
     * Get the translation values.
     *
     * @return Array|null The translation values.
     */
    public function getValues() {
        return $this->values;
    }

    /**
     * Encode the translation values and return the result.
     *
     * @return string|null The encoded values as a string, or null if not translation values are set.
     */
    public function getValuesEncoded() {
        // Make sure any translations are set, return null if not
        if($this->values === null || sizeof($this->values) <= 0)
            return null;

        // Encode and return the array of translation values to JSON
        return json_encode($this->values);
    }

    /**
     * Set the translation values.
     *
     * @param Array|null $values The translation values.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function setValues($values) {
        // Trim the values if it's a string
        if(is_string($values))
            $values = trim($values);

        // Parse null values
        if($values === null || strlen($values) < 2) {
            $this->values = Array();
            return;
        }

        // Set the values if it's an array
        if(is_array($values)) {
            $this->values = $values;
            return;
        }

        // Parse a JSON string
        $values = json_decode($values, true);

        // Set the values if they're valid
        $this->values = $values;
    }

    /**
     * Get the default value.
     *
     * @return string|null The default value, or null if no default is set.
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * Check whether this database value has a default set.
     *
     * @return bool True if a default is set, false if not.
     */
    public function hasDefault() {
        return $this->default !== null;
    }

    /**
     * Set the default value returned if no valid translation was found.
     *
     * @param string|null $default [optional] The default value, or null to clear the default value.
     */
    public function setDefault($default = null) {
        $this->default = $default;
    }

    /**
     * Get a translation value.
     *
     * @param Language|string|null $language The language instance, the language tag or null to use the current language.
     *
     * @return string|null The translation value or the default if it doesn't exist.
     *
     * @throws Exception Throws if the language is invalid.
     */
    public function getValue($language = null) {
        // Parse the language and make sure it's valid
        if(($language = LanguageManager::parseLanguageTag($language)) === null)
            throw new Exception('The language tag is invalid or the language isn\'t loaded.');

        // Make sure the translation exists, return the default if not
        if(!$this->hasTranslation($language))
            return $this->getDefault();

        // Return the translation
        return $this->values[$language];
    }

    /**
     * Check weather a specific translation is set.
     *
     * @param Language|string|null $language The language as language instance, the language tag or null to use the current preferred language.
     *
     * @return bool True if a translation for this language exists, false if not.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function hasTranslation($language) {
        // Parse the language tag and make sure it's valid
        if(($language = LanguageManager::parseLanguageTag($language)) === null)
            throw new Exception('The language tag is invalid or the language isn\'t loaded.');

        // Check whether the values array has this language key, return the result
        return array_key_exists($language, $this->values);
    }

    /**
     * Set the translation for a specific language.
     * The translation will replace previous translations. If no translation is available yet for this language it will
     * be created.
     *
     * @param Language|string|null $language The language as language instance, the language tag or null to use the
     * current preferred language.
     * @param string|null $value The translation value, or null to delete the translation if it exists.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function setTranslation($language, $value) {
        // Parse the language tag and make sure it's valid
        if(($language = LanguageManager::parseLanguageTag($language)) === null)
            throw new Exception('The language tag is invalid or the language isn\'t loaded.');

        // Set or delete the translation value depending on the value
        if($value !== null)
            $this->values[$language] = $value;
        else
             unset($this->values[$language]);
    }

    /**
     * Delete a translation value for a specific language if it exists.
     *
     * @param Language|string|null $language The language as language instance, the language tag or null to use the current preferred language.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function deleteTranslation($language) {
        $this->setTranslation($language, null);
    }

    /**
     * Get all language tags a translation is set for.
     *
     * @return array Language tags.
     */
    public function getLanguageTags() {
        return array_keys($this->values);
    }

    /**
     * Get all languages a translation is set for.
     *
     * @return array Languages.
     */
    public function getLanguages() {
        // Get the list of language tags
        $tags = $this->getLanguageTags();

        // Create a list with languages
        $languages = Array();

        // Convert each tag into a language
        foreach($tags as $tag)
            $languages[] = LanguageManager::getByTag($tag);

        // Return the list of languages
        return $languages;
    }
}
