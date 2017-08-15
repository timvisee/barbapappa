<?php

namespace carbon\core\io\filesystem\file;

use carbon\core\io\filesystem\file\accessmode\FileAccessMode;
use carbon\core\io\filesystem\file\accessmode\FileAccessModeFactory;
use carbon\core\io\filesystem\FilesystemObject;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * The FileWriter class. This class provides an interface to easily write to files, such as log files.
 *
 * @author Tim Visee
 * @package carbon\core\io\filesystem\file
 */
class FileWriter {

    /** @var File File to write to */
    private $file = null;
    /** @var FileHandler File handler */
    private $handler = null;
    /** @var FileAccessMode File access mode used to access the file. */
    private $fileMode;

    /** @var string The default file access mode used if no mode was specified. */
    const FILE_MODE_DEFAULT = 'w';

    // TODO: Variable to set the new line character!
    // TODO: More and custom file access modes!

    /**
     * Constructor.
     *
     * @param FilesystemObject|string $file File instance or the path of a file as a string of the file to write to.
     * The file or file path must be valid.
     * @param FileAccessMode|string|null $fileMode [optional] The file access mode instance or the file access mode
     * string that should be used to access the file. Null to use the default file access mode.
     *
     * @throws \Exception Throws an exception on error.
     */
    public function __construct($file, $fileMode = self::FILE_MODE_DEFAULT) {
        // Get and store $file as File instance, throw an exception if failed
        if(($file = File::asFile($file)) === null)
            // TODO: Throw a better exception!
            throw new \Exception("Invalid file!");

        // Set the file access mode
        $this->setFileAccessMode($fileMode);

        // Set the file
        $this->setFile($file);
    }

    /**
     * Destructor.
     * Close the file handler properly.
     */
    public function __destruct() {
        $this->close();
    }

    /**
     * Get the file access mode which is used by the file writer to access the file.
     *
     * @return FileAccessMode The file access mode.
     */
    public function getFileAccessMode() {
        return $this->fileMode;
    }

    /**
     * Set the file access mode used by the file handler.
     * This will reopen the file handler if the file access mode changed and it was opened already.
     *
     * @param string $fileMode The file access mode that should be used by the file handler.
     *
     * @return bool True on success, false on failure.
     */
    private function setFileAccessMode($fileMode) {
        // Check whether the default file access mode should be used
        if($fileMode === null)
            $fileMode = FileAccessMode::asFileAccessMode(self::FILE_MODE_DEFAULT);

        // Convert $fileMode into a file access mode instance
        if(($fileMode = FileAccessMode::asFileAccessMode($fileMode)) === null)
            return false;

        // Set the file access mode that should be used
        $this->fileMode = $fileMode;

        // Check whether the file handler is opened and whether the file access mode should be changed.
        // If so, reopen the file handler with the proper file access mode
        if($this->handler !== null)
            if($this->handler->isOpened() && !$fileMode->equals($this->handler->getFileAccessMode(), false))
                return $this->handler->reopen($this->fileMode);

        // File access mode changed successfully, return the result
        return true;
    }

    /**
     * Get the file which is being written to.
     *
     * @return File|null The file to write to, or null if no file was set.
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Set the file to write to. The new file will automatically open if the current file was opened already.
     *
     * @param File|string| $file File instance or the file as a path string to write to.
     *
     * @return bool True if the file was successfully set, false on failure.
     */
    public function setFile($file) {
        // Get $file as File instance, make sure it's not null
        if(($file = File::asFile($file)) === null)
            return false;

        // Make sure the file has changed
        if($this->file === $file)
            return true;

        // Set the file
        $this->file = $file;

        // Open a new file handler if if hasn't been instantiated yet, return the result
        if($this->handler === null) {
            $this->handler = new FileHandler($this->file);
            return true;
        }

        // Set the file being handled, reopen the file handler with the new file if it is opened, return the result
        return $this->handler->setFile($this->file);
    }

    /**
     * Open the file handler to write to.
     *
     * @param FileAccessMode|string|null $fileMode [optional] The optional file access mode that should be used to
     * access the file as a file access mode instance or file access mode string. Null may be supplied to use the
     * default file access mode.
     * @param resource|null $context [optional] See PHPs fopen() function for more details. Null to use the default
     * context.
     *
     * @return bool True on success, false on failure. True will be returned if the file handler was opened already.
     */
    public function open($fileMode = null, $context = null) {
        // TODO: Reopen the file handler if $fileMode or $context changed!

        // Make sure the file handler isn't opened already
        if($this->isOpened())
            return true;

        // Set the file access mode if it isn't null
        if($fileMode !== null)
            $this->setFileAccessMode($fileMode);

        // Construct a new file handler, return the result
        if($this->handler === null)
            $this->handler = new FileHandler($this->file);
        return $this->handler->open($this->fileMode, $context);
    }

    /**
     * Check whether the file handler is opened.
     *
     * @return bool True if the file handler is opened, false if not.
     */
    public function isOpened() {
        // Make sure a file handler is available
        if($this->handler === null)
            return false;

        // Check whether the file handler is opened, return the result
        return $this->handler->isOpened();
    }

    /**
     * Close the file handler to write to.
     *
     * @return bool True on success, false on failure. True will be returned if the file handler isn't opened.
     */
    public function close() {
        return $this->handler->close();
    }

    /**
     * Get the file handler instance.
     *
     * @return FileHandler|null File handler instance. May return null if the file handler isn't opened yet.
     */
    public function getFileHandler() {
        return $this->handler;
    }

    /**
     * Write a string to the file. The file handler will be opened automatically if it hasn't been opened yet.
     * This method is binary safe.
     *
     * @param string $str The string to write to the file.
     *
     * @return int|null Returns the number of written bytes, null on failure.
     */
    public function write($str) {
        // Open the file handler if it isn't opened yet
        if(!$this->open())
            return false;

        // Write the string to the file, return the result
        return $this->handler->write($str);
    }

    /**
     * Write a line to the file. The file handler will be opened automatically if it hasn't been opened yet.
     * This method is binary safe.
     *
     * @param string $str The string to write to the file.
     */
    public function writeLine($str) {
        // Write a line to the file
        $this->write($str . FileHelper::getNewLineChar());
    }

    /**
     * Set the file position indicator of the file handler. The file handler will be opened as it's needed.
     * The new position of the indicator is measured in bytes from the beginning of the file.
     * This method moves the indicator to the $whence position with $offset added.
     *
     * @param int $offset The offset.
     * To move to a position before the end-of-file, you need to pass a negative value in $offset and set
     * $whence to SEEK_END.
     * @param int $whence Positioning origin, choose from:
     * SEEK_SET: Set position equal to offset bytes.
     * SEEK_CUR: Set position to current location plus offset.
     * SEEK_END: Set position to end-of-file plus offset.
     *
     * @return bool True on success, false on failure.
     */
    public function seek($offset, $whence = SEEK_SET) {
        // Open the file handler if it isn't opened yet
        if(!$this->open())
            return false;

        // Read and return
        return $this->handler->seek($offset, $whence);
    }

    /**
     * Check whether the file we're writing to is valid.
     *
     * @return bool True if the file is valid, false otherwise.
     */
    public function isValid() {
        // Make sure $file isn't null
        if($this->file === null)
            return false;

        // Make sure $file is an instance of File, return the result
        if(!$this->file instanceof File)
            return false;

        // Make sure the file is valid
        return $this->file->isValid();
    }

    /**
     * Check whether the file we're writing to exists.
     *
     * @return bool True if the target file exists, false otherwise.
     */
    public function exists() {
        // Make sure the file is valid
        if(!$this->isValid())
            return false;

        // Check whether the file exists, return the result
        return $this->file->exists();
    }
}
 