<?php

namespace carbon\core\io\filesystem\file\accessmode;

use carbon\core\util\StringUtils;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class FileAccessMode {

    /** @var bool True when the file should be readable, false otherwise. */
    private $read = false;
    /** @var bool True if the file should be writable, false otherwise. */
    private $write = false;
    /** @var bool True if the file should be created if it doesn't exist, false otherwise. */
    private $create = false;
    /** @var bool True if the file is forced to be created or an error will be returned, false otherwise. */
    private $forceCreate = false;
    /** @var bool True to truncate the file once it's opened, false otherwise. */
    private $truncate = false;
    /** @var bool True to put the file pointer at the ending of the file. */
    private $end = false;
    /**
     * @var bool True if text-mode translation should be enabled to translate \n to \r\n when working with files, false
     * to use the regular mode. This feature is only available on Windows based platforms, and is ignored on other
     * unsupported platforms.
     */
    private $textModeTranslation = false;
    /**
     * @var bool True to enable the binary mode, false otherwise. It's strongly recommended to enable the binary flag
     * when working with binary files to prevent strange problems from occurring with new line characters and images.
     */
    private $binary = false;

    /**
     * Constructor.
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
     */
    public function __construct($read = false, $write = false, $create = false, $forceCreate = false, $truncate = false,
                                $end = false, $textModeTranslation = false, $binary = false) {
        // Set the variables
        $this->read = $read;
        $this->write = $write;
        $this->create = $create;
        $this->forceCreate = $forceCreate;
        $this->truncate = $truncate;
        $this->end = $end;
        $this->textModeTranslation = $textModeTranslation;
        $this->binary = $binary;
    }

    /**
     * Get a file access mode instance. This method will try to instantiate a file access mode instance based on the
     * input. This method won't verify the validity of the file access mode string.
     *
     * @param FileAccessMode|string $mode File access mode instance, or a PHPs file access mode string.
     *
     * @return FileAccessMode|null The file access mode instance, or null on failure.
     */
    public static function asFileAccessMode($mode) {
        // Return $mode if it's an FileAccessMode instance
        if($mode instanceof FileAccessMode)
            return $mode;

        // The $mode must be a string
        if(!is_string($mode))
            return null;

        // Trim $mode from unwanted whitespaces
        $mode = trim($mode);

        // Make sure $mode isn't empty and has a maximum of 4 characters.
        if(empty($mode) || strlen($mode) > 4)
            return null;

        // Try to convert the file access mode string into an instance, return the result
        return FileAccessModeFactory::createFromMode($mode);
    }

    /**
     * Check whether the file should be readable.
     *
     * @return bool True if the file should be readable, false otherwise.
     */
    public function isRead() {
        return $this->read;
    }

    /**
     * Set whether the file should be readable.
     *
     * @param bool $readable True if the file should be readable, false otherwise.
     *
     * @return FileAccessMode This instance, for method chaining.
     */
    public function setRead($readable) {
        // Set whether the file should be readable
        $this->read = $readable;

        // Return this instance
        return $this;
    }

    /**
     * Check whether the file should be writable.
     *
     * @return bool True if the file should be writable, false otherwise.
     */
    public function isWrite() {
        return $this->write;
    }

    /**
     * Set whether the file should be writable.
     *
     * @param bool $writable True if the file should be writable, false otherwise.
     *
     * @return FileAccessMode This instance, for method chaining.
     */
    public function setWrite($writable) {
        // Set whether the file should be writable
        $this->write = $writable;

        // Return this instance
        return $this;
    }

    /**
     * Check whether the file should be created if it doesn't exist.
     *
     * @return bool True if the file should be created if it doesn't exist.
     */
    public function isCreating() {
        return $this->create;
    }

    /**
     * Set whether the file should be craeted if it doesn't exist.
     *
     * @param bool $creating True if the file should be created if it doesn't exist.
     *
     * @return FileAccessMode This instance, for method chaining.
     */
    public function setCreate($creating) {
        // Set whether the file should be created
        $this->create = $creating;

        // Return this instance
        return $this;
    }

    /**
     * Check if the file is forced to be created, or else an error will occur.
     *
     * @return bool True if the file is forced to be created.
     */
    public function isForceCreate() {
        return $this->forceCreate;
    }

    /**
     * Set whether the file is forced to be created, or else an error will occur.
     *
     * @param bool $forceCreating True if the file is forced to be created.
     *
     * @return FileAccessMode This instance, for method chaining.
     */
    public function setForceCreate($forceCreating) {
        // Set whether the file is force to be created
        $this->forceCreate = $forceCreating;

        // Return this instance
        return $this;
    }

    /**
     * Check whether the file should be truncated once it's opened.
     *
     * @return bool True if the file should be truncated once it's opened.
     */
    public function isTruncate() {
        return $this->truncate;
    }

    /**
     * Set whether the file should be truncated once it's opened.
     *
     * @param bool $truncating True if the file should be truncated once it's opened.
     *
     * @return FileAccessMode This instance, for method chaining.
     */
    public function setTruncate($truncating) {
        // Set whether the file should be truncated
        $this->truncate = $truncating;

        // Return this instance
        return $this;
    }

    /**
     * Check whether the file pointer should be placed at the end of the file.
     *
     * @return bool True if the file pointer should be placed at the end of the file.
     */
    public function isEnd() {
        return $this->end;
    }

    /**
     * Set whether the file pointer should be placed at the end of the file.
     *
     * @param bool $end True if the file pointer should be placed at the end of the file.
     *
     * @return FileAccessMode This instance, for method chaining.
     */
    public function setEnd($end) {
        // Set whether the file pointer should be put at the end of the file
        $this->end = $end;

        // Return this instance
        return $this;
    }

    /**
     * Check whether text-mode translations are enabled to translate \n to \r\n when working with files, false to use
     * the regular mode. This feature is only available on Windows based platforms, and is ignored on other unsupported
     * platforms.
     *
     * @return bool True if text-mode translations should be enabled, false to use the regular mode.
     */
    public function isTextModeTranslation() {
        return $this->textModeTranslation;
    }

    /**
     * Set whether text-mode translations are enabled to translate \n to \r\n when working with files, false to use the
     * regular mode. This feature is only available on Windows based platforms, and is ignored on other unsupported
     * platforms.
     *
     * @param bool $textModeTranslation True if text-mode translations should be enabled, false to use the regular mode.
     *
     * @return FileAccessMode This instance, for method chaining.
     */
    public function setTextModeTranslation($textModeTranslation) {
        // Set whether text-mode translations should be enabled
        $this->textModeTranslation = $textModeTranslation;

        // Return this instance
        return $this;
    }

    /**
     * Check whether the binary mode is used. It's strongly recommended to enable the binary flag when working with
     * binary files to prevent strange problems from occurring with new line characters and images.
     *
     * @return bool True to enable the binary mode, false otherwise.
     */
    public function isBinary() {
        return $this->binary;
    }

    /**
     * Set whether the binary mode is used. It's strongly recommended to enable the binary flag when working with
     * binary files to prevent strange problems from occurring with new line characters and images.
     *
     * @param bool $binary True to enable the binary mode, false otherwise.
     *
     * @return FileAccessMode This instance, for method chaining.
     */
    public function setBinary($binary) {
        // Set the binary mode
        $this->binary = $binary;

        // Return this instance
        return $this;
    }

    /**
     * Get the file access mode for PHPs file methods as a string based on the file access mode properties.
     * Some file access mode combinations aren't possible, in that case the most similar mode will be returned.
     *
     * @return string The file access mode.
     */
    public function getMode() {
        // TODO: Cache!

        // Get the file access mode suffix
        $suffix = '';
        if($this->textModeTranslation)
            $suffix .= 't';
        if($this->binary)
            $suffix .= 'b';

        // Check whether the file should be opened for reading only
        if($this->read && !$this->write)
            return 'r' . $suffix;

        // Check whether the file should be opened for writing only
        if(!$this->read && $this->write) {
            // Check whether the file should be force created
            if($this->forceCreate)
                return 'x' . $suffix;

            // Check whether the file should be truncated
            if($this->truncate)
                return 'w' . $suffix;

            // Check whether the file should be opened on the end
            if($this->end)
                return 'a' . $suffix;

            // Return the default file access mode for write-only operations
            return 'c' . $suffix;
        }

        // The file should be opened for reading and writing
        // Check whether the file should be force created
        if($this->forceCreate)
            return 'x+' . $suffix;

        // Check whether the file should be truncated
        if($this->truncate)
            return 'w+' . $suffix;

        // Check whether the file should be opened on the end
        if($this->end)
            return 'a+' . $suffix;

        // Check whether the file should be created
        if($this->create)
            return 'c+' . $suffix;

        // Return the default file access mode for read and write operations
        return 'r+' . $suffix;
    }

    /**
     * Compare the file access mode with an other file access mode instance.
     *
     * @param FileAccessMode|string $other The other file access mode instance to compare this instance to. A file
     * access mode string may be supplied which causes this method to compare the two file access modes as strings.
     * @param bool $exact [optional] True to ensure the two instances exactly equal each other with all of their
     * properties, false to just compare the file access mode strings of both instances since different instances may
     * share the same file access mode string. This option is only available if $other was a FileAccessMode instance.
     *
     * @return bool True if the two instances equal, false otherwise. False will also be returned if the other instance
     * is invalid.
     */
    public function equals($other, $exact = false) {
        // Make sure $other isn't null
        if($other === null)
            return false;

        // Directly compare the two instances
        if($this === $other)
            return true;

        // Compare the two instances as a string, if $other is a string.
        if(is_string($other))
            return StringUtils::equals($this->getMode(), $other, false, true);

        // Compare the PHPs file access mode strings if the comparison doesn't have to be exact.
        if(!$exact)
            return StringUtils::equals($this->getMode(), $other->getMode(), false);

        // The two instances doesn't seem to equal, return false
        return false;
    }

    /**
     * Convert the file access mode to a string. This will return the getMode() value.
     *
     * @return string The file access mode as a string.
     *
     * @see FileAccessMode::getMode();
     */
    public function __toString() {
        return $this->getMode();
    }
}
