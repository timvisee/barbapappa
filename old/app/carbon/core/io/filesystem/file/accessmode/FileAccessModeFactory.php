<?php

namespace carbon\core\io\filesystem\file\accessmode;

use carbon\core\util\StringUtils;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class FileAccessModeFactory {

    /**
     * Create a new file access mode instance.
     *
     * @param bool $read [optional] True if the file should be readable, false otherwise.
     * @param bool $write [optional] True if the file should be writeable, false otherwise.
     * @param bool $create [optional] True if the file should be created if it doesn't exist.
     * @param bool $forceCreate [optional] True if the file is forced to be created.
     * @param bool $truncate [optional] True if the file should be truncated once it's opened.
     * @param bool $end [optional] True if the file pointer should be placed at the end of the file.
     * @param bool $textModeTranslation [optional] True to enable text-mode translations to translate \n to \r\n on
     * supported platforms.
     * @param bool $binary True to enable binary mode.
     *
     * @return FileAccessMode File access mode instance, for method chaining.
     */
    public static function create($read = false, $write = false, $create = false, $forceCreate = false,
                                  $truncate = false,  $end = false, $textModeTranslation = false, $binary = false) {
        return new self($read, $write, $create, $forceCreate, $truncate, $end, $textModeTranslation, $binary);
    }

    /**
     * Create a file access mode instance based on a PHP file access modes string.
     * This method won't verify the validity of the file access mode string.
     *
     * @param string $mode PHPs file access mode string.
     *
     * @return FileAccessMode|null The file access mode instance, or null on failure.
     */
    public static function createFromMode($mode) {
        // Return $mode if it's an FileAccessMode instance
        if($mode instanceof FileAccessMode)
            return $mode;

        // Create and return a new file access mode instance
        return new FileAccessMode(
            // Check whether the file access mode should be readable
            StringUtils::contains($mode, '+'),

            // Check whether the file access mode should create the file
            !StringUtils::contains($mode, 'r') || StringUtils::contains($mode, '+'),

            // Check whether the file access mode should create the file
            !StringUtils::contains($mode, 'r'),

            // Check whether the file access mode should force create the file
            StringUtils::contains($mode, 'x'),

            // Check whether the file access mode truncates the file
            StringUtils::contains($mode, 'w'),

            // Check whether the file access mode should start at the end of the file
            StringUtils::contains($mode, 'a'),

            // Check whether the file access mode should use text-mode translations on supported platforms
            StringUtils::contains($mode, 't'),

            // Check whether the file access mode should use binary mode
            StringUtils::contains($mode, 'b')
        );
    }

    /**
     * Create the file access mode which could be used for reading only.
     *
     * @param bool $textModeTranslation [optional] True to enable text-mode translations to translate \n to \r\n on
     * supported platforms.
     * @param bool $binary True to enable binary mode.
     *
     * @return FileAccessMode File access mode instance.
     */
    public static function createReadOnlyMode($textModeTranslation = false, $binary = false) {
        return new FileAccessMode(true, false, false, false, false, false, $textModeTranslation, $binary);
    }

    /**
     * Create the file access mode which forces a new file to be created.
     *
     * @param bool $read [optional] True if the file should be readable, false otherwise.
     * @param bool $textModeTranslation [optional] True to enable text-mode translations to translate \n to \r\n on
     * supported platforms.
     * @param bool $binary True to enable binary mode.
     *
     * @return FileAccessMode File access mode instance.
     */
    public static function createForceCreateMode($read = true, $textModeTranslation = false, $binary = false) {
        return new FileAccessMode($read, true, false, true, false, false, $textModeTranslation, $binary);
    }

    /**
     * Create the file access mode which truncates the file.
     *
     * @param bool $read [optional] True if the file should be readable, false otherwise.
     * @param bool $textModeTranslation [optional] True to enable text-mode translations to translate \n to \r\n on
     * supported platforms.
     * @param bool $binary True to enable binary mode.
     *
     * @return FileAccessMode File access mode instance.
     */
    public static function createTruncateMode($read = true, $textModeTranslation = false, $binary = false) {
        return new FileAccessMode($read, true, false, false, true, false, $textModeTranslation, $binary);
    }

    /**
     * Create the file access mode which places the file pointer at the end of the file, and tries to create the file if it
     * doesn't exist.
     *
     * @param bool $read [optional] True if the file should be readable, false otherwise.
     * @param bool $textModeTranslation [optional] True to enable text-mode translations to translate \n to \r\n on
     * supported platforms.
     * @param bool $binary True to enable binary mode.
     *
     * @return FileAccessMode File access mode instance.
     */
    public static function createAppendMode($read = true, $textModeTranslation = false, $binary = false) {
        return new FileAccessMode($read, true, true, false, false, true, $textModeTranslation, $binary);
    }

    /**
     * Create the file access mode which places the file pointer at the beginning of the file, and tries to create the
     * file if it doesn't exist.
     *
     * @param bool $read [optional] True if the file should be readable, false otherwise.
     * @param bool $textModeTranslation [optional] True to enable text-mode translations to translate \n to \r\n on
     * supported platforms.
     * @param bool $binary True to enable binary mode.
     *
     * @return FileAccessMode File access mode instance.
     */
    public static function createPrependMode($read = true, $textModeTranslation = false, $binary = false) {
        return new FileAccessMode($read, true, true, false, false, false, $textModeTranslation, $binary);
    }
}
 