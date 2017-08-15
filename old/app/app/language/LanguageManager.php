<?php

namespace app\language;

use app\config\Config;
use app\session\SessionManager;
use app\user\User;
use carbon\core\cookie\CookieManager;
use carbon\core\io\filesystem\directory\Directory;
use carbon\core\io\filesystem\directory\DirectoryScanner;
use carbon\core\util\StringUtils;
use Exception;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Class LanguageManager.
 *
 * @package app\language
 */
class LanguageManager {

    /** The name of the language tag cookie. */
    const LANGUAGE_COOKIE_NAME = 'lang_tag';
    /** The time it takes for the language tag cookie to expire. */
    const LANGUAGE_COOKIE_EXPIRE = '+1 week';
    /** The user meta key for the preferred language. */
    const LANGUAGE_USER_META_KEY = 'language.tag';

    /** @var array List of loaded languages. */
    private static $languages = Array();
    /** @var string The default language tag used when not specified. */
    private static $defaultTag = 'en-US';
    /** @var string|null The preferred language that should currently be used, or null if undefined. */
    private static $currentTag = null;

    /**
     * Initialize.
     *
     * @param bool $load [optional] True to automatically load all available languages.
     * @param string|null $defaultLanguageTag [optional] The default language tag to use, or null to keep the current setting.
     */
    public static function init($load = true, $defaultLanguageTag = null) {
        // Load the languages
        if($load)
            static::load();

        // Set the default language tag
        if(is_string($defaultLanguageTag))
            static::$defaultTag = $defaultLanguageTag;
    }

    /**
     * Add a new language to the manager.
     *
     * @param Language $language The language.
     *
     * @return bool True if the language has been added, false if not because a language with this tag is already loaded.
     *
     * @throws Exception Throws an exception if the language instance is invalid.
     */
    public static function add($language) {
        // Make sure the instance is valid
        if(!($language instanceof Language))
            throw new Exception('Unable to add language, invalid instance.');

        // Make sure the language tag is unique
        if(static::isWithTag($language->getTag()))
            return false;

        // Add the language, return the result
        static::$languages[] = $language;
        return true;
    }

    /**
     * Check whether a language is loaded with a specific tag.
     *
     * @param string $tag The language tag.
     *
     * @return bool True if a language exists with this tag, false if not.
     */
    public static function isWithTag($tag) {
        return static::getByTag($tag) !== null;
    }

    /**
     * Get a loaded language by it's language tag.
     *
     * @param string $tag The language tag.
     *
     * @return Language|null The language, or null if no language is loaded with this tag.
     */
    public static function getByTag($tag) {
        // Loop through all languages to return the proper language
        foreach(static::$languages as $language)
            if($language instanceof Language && StringUtils::equals($language->getTag(), $tag, false))
                return $language;

        // Not language found for this tag, return null
        return null;
    }

    /**
     * Get all loaded languages.
     *
     * @return array Loaded languages.
     */
    public static function getLanguages() {
        return static::$languages;
    }

    /**
     * Load all languages from the language directory.
     */
    public static function load() {
        // Get all language files
        $files = static::getLanguageFiles();

        // Define a list to put the loaded languages in
        $languages = Array();

        // Load each language and add it to the list
        foreach($files as $file)
            $languages[] = new Language($file);

        // Set the list of loaded languages
        static::$languages = $languages;
    }

    /**
     * Get the language files directory.
     *
     * @return Directory The directory.
     */
    public static function getLanguageDirectory() {
        return new Directory(CARBON_SITE_ROOT, Config::getValue('app', 'language.directory', '/language'));
    }

    /**
     * Get all language files in the language directory.
     *
     * @return array Language files.
     *
     * @throws Exception Throws if an error occurred.
     */
    private static function getLanguageFiles() {
        // Get the language directory and make sure it exists
        $langDir = static::getLanguageDirectory();
        if(!$langDir->isDirectory())
            throw new Exception('Language file directory doesn\'t exist, or isn\'t accessible!');

        // Create a directory scanner to list all language files
        $scanner = new DirectoryScanner($langDir);

        // Define an array for the language files
        $langFiles = Array();

        // Process each file in this directory
        while(($file = $scanner->read()) !== null) {
            // Make sure this file is a file
            if(!$file->isFile())
                continue;

            // Make sure the extension is valid
            if(!StringUtils::equals($file->getExtension(false), 'ini', false, true))
                continue;

            // Add the file to the language files list
            $langFiles[] = $file;
        }

        // Return the list of files
        return $langFiles;
    }

    /**
     * Get the default language if loaded.
     *
     * @return Language|null Default language, or null if not loaded.
     */
    public static function getDefaultLanguage() {
        return static::getByTag(static::getDefaultLanguageTag());
    }

    /**
     * Get the default language tag.
     *
     * @return string Default language tag.
     */
    public static function getDefaultLanguageTag() {
        return static::$defaultTag;
    }

    /**
     * Get the preferred language to use. This is based on the user language tag, the default language tag and the loaded languages.
     *
     * @return Language|null The preferred language to use, or null if not language is loaded.
     */
    public static function getPreferredLanguage() {
        // Return the current language if set
        if(($lang = static::getCurrentLanguage()) !== null)
            return $lang;

        // Return the cookie language if set
        if(($lang = static::getCookieLanguage()) !== null)
            return $lang;

        // Return the user language if set
        if(($lang = static::getUserLanguage()) !== null)
            return $lang;

        // Return the default language if valid
        if(($defLang = static::getDefaultLanguage()) !== null)
            return $defLang;

        // If any language is loaded, return the first
        if(sizeof(static::$languages) > 0)
            return static::$languages[0];

        // No language preferred, return null
        return null;
    }

    /**
     * Get the preferred language to use for the specified user.
     * The possible best language will be returned if the user doesn't have a preferred language set.
     *
     * @param User|null $user [optional] The user to get the preferred language for, or null to get the language for the
     * currently logged in user.
     *
     * @return Language|null The preferred language to use, or null if no language could be selected or if the language
     * isn't loaded.
     */
    public static function getPreferredLanguageFromUser($user = null) {
        // Parse the user, and make sure it's valid
        if($user === null)
            $user = SessionManager::getLoggedInUser();
        if(!($user instanceof User))
            return null;

        // Return the user language if set
        if(($lang = static::getUserLanguage($user)) !== null)
            return $lang;

        // Return the current language if set
        if(($lang = static::getCurrentLanguage()) !== null)
            return $lang;

        // Return the cookie language if set
        if(($lang = static::getCookieLanguage()) !== null)
            return $lang;

        // Return the default language if valid
        if(($defLang = static::getDefaultLanguage()) !== null)
            return $defLang;

        // If any language is loaded, return the first
        if(sizeof(static::$languages) > 0)
            return static::$languages[0];

        // No language preferred, return null
        return null;
    }

    /**
     * Get the current language if specified and loaded.
     *
     * @return Language|null The current language, or null if not loaded or not specified.
     */
    public static function getCurrentLanguage() {
        return static::getByTag(static::getCurrentLanguageTag());
    }

    /**
     * Get the current language tag.
     *
     * @return string|null Current language tag, or null if not specified.
     */
    public static function getCurrentLanguageTag() {
        return static::$currentTag;
    }

    /**
     * Set the current preferred language tag.
     *
     * @param string|null $langTag The current preferred language tag, or null.
     *
     * @throws Exception Throws if the language tag is unknown or invalid.
     */
    public static function setCurrentLanguageTag($langTag) {
        // Make sure the tag is valid or null
        if($langTag !== null && !static::isWithTag($langTag))
            throw new Exception('Invalid or unknown language tag.');

        // Set the user tag
        static::$currentTag = $langTag;
    }

    /**
     * Get the cookie language if specified and loaded.
     *
     * @return Language|null The cookie language, or null if not loaded or not specified.
     */
    public static function getCookieLanguage() {
        return static::getByTag(static::getCookieLanguageTag());
    }

    /**
     * Get the language tag from the user's cookie if specified.
     *
     * @return string|null The language tag, or null if not set.
     */
    public static function getCookieLanguageTag() {
        return CookieManager::getCookie(static::LANGUAGE_COOKIE_NAME);
    }

    /**
     * Set the language tag cookie.
     *
     * @param string|null $langTag The language tag, or null to reset.
     *
     * @throws Exception Throws if the language tag is unknown or invalid.
     */
    public static function setCookieLanguageTag($langTag) {
        // Make sure the tag is valid or null
        if($langTag !== null && !static::isWithTag($langTag))
            throw new Exception('Invalid or unknown language tag.');

        // Set or reset the cookie
        if($langTag !== null)
            CookieManager::setCookie(static::LANGUAGE_COOKIE_NAME, $langTag, static::LANGUAGE_COOKIE_EXPIRE);
        else
            CookieManager::deleteCookie(static::LANGUAGE_COOKIE_NAME);
    }

    /**
     * Get the user language if specified and loaded.
     *
     * @param User|null $user [optional] The user to get the language tag from, or null to use the current logged in user.
     *
     * @return Language|null The user language, or null if not loaded or not specified.
     */
    public static function getUserLanguage($user = null) {
        return static::getByTag(static::getUserLanguageTag($user));
    }

    /**
     * Get the preferred language tag for the specified user if set.
     *
     * @param User|null $user [optional] The user, or null to use the current logged in user.
     *
     * @return string|null Preferred user language tag, or null if not set.
     */
    public static function getUserLanguageTag($user = null) {
        // Parse the user
        if($user === null) {
            // Make sure the user is logged in
            if(!SessionManager::isLoggedIn())
                return null;

            // Get the logged in user
            $user = SessionManager::getLoggedInUser();
        }

        // Make sure the user instance is valid
        if(!($user instanceof User))
            return null;

        // Get the user and return the preferred language for the user if set
        return $user->getMeta(static::LANGUAGE_USER_META_KEY);
    }

    /**
     * Get the preferred language tag for the specified user if set.
     *
     * @param string|null $langTag The preferred language tag or null to reset the preferred language tag.
     * @param User|null $user [optional] The user, or null to use the current logged in user.
     *
     * @return bool True if succeed, false if not.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function setUserLanguageTag($langTag, $user = null) {
        // Parse the language tag
        if($langTag instanceof Language)
            $langTag = $langTag->getTag();

        // Validate the language tag
        if($langTag !== null && !static::isWithTag($langTag))
            throw new Exception('Invalid language tag.');

        // Parse the user
        if($user === null) {
            // Make sure the user is logged in
            if(!SessionManager::isLoggedIn())
                return false;

            // Get the logged in user
            $user = SessionManager::getLoggedInUser();
        }

        // Make sure the user instance is valid
        if(!($user instanceof User))
            return false;

        // Set or reset the language tag, return the result
        if($langTag != null)
            $user->setMeta(static::LANGUAGE_USER_META_KEY, $langTag);
        else
            $user->deleteMeta(static::LANGUAGE_USER_META_KEY);
        return true;
    }

    /**
     * Get a language value.
     *
     * @param string $section Value section.
     * @param string $key Value key.
     * @param string|null $default The default value used if the value doesn't exist, or null.
     * @param Language|string $language The language or language tag to use.
     *
     * @return string The language value, or the default if the language value doesn't exist.
     *
     * @throws Exception Throws an exception on failure.
     */
    public static function getValue($section, $key, $default = null, $language = null) {
        // Parse the language
        if(!($language instanceof Language)) {
            // Use the preferred language if null
            if($language === null)
                $language = static::getPreferredLanguage();
            else
                $language = static::getByTag($language);
        }

        // Make sure the language is valid
        if($language === null)
            throw new Exception('Invalid language.');

        // Get the value of this language
        return $language->get($section, $key, $default);
    }

    /**
     * Set the language tag for various aspects.
     *
     * @param string|null $langTag The language tag, or null to reset.
     * @param bool $setCurrent [optional] True to set the currently used language tag, false if not.
     * @param bool $setCookie [optional] True to set the cookie language tag, false if not.
     * @param bool $setUser [optional] True to set the user's language tag, false if not.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function setLanguageTag($langTag, $setCurrent = true, $setCookie = true, $setUser = true) {
        // Validate the language
        if(!static::isWithTag($langTag))
            throw new Exception('Invalid language tag.');

        // Set the current language
        if($setCurrent)
            static::setCurrentLanguageTag($langTag);

        // Set the language cookie
        if($setCookie)
            static::setCookieLanguageTag($langTag);

        // Make sure the user is logged in
        if($setUser)
            static::setUserLanguageTag($langTag);
    }

    /**
     * Try to parse a language tag.
     *
     * @param Language|string|null|mixed $language [optional] A language instance, a language tag, null to return the preferred language tag or any other value.
     *
     * @return string|null The language tag to parse, or null to return the preferred language tag.
     */
    public static function parseLanguageTag($language = null) {
        // Return the default language if the parameter is null
        if($language === null)
            return static::getPreferredLanguage()->getTag();

        // Parse languages instances
        if($language instanceof Language)
            return $language->getTag();

        // Return a language tag if it's valid
        if(is_string($language) && LanguageManager::isWithTag($language))
            return $language;

        // Unable to parse the language tag
        return null;
    }
}
