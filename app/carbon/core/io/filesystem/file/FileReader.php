<?php

namespace carbon\core\io\filesystem\file;

use carbon\core\io\filesystem\file\accessmode\FileAccessMode;
use carbon\core\io\filesystem\file\accessmode\FileAccessModeFactory;
use carbon\core\io\filesystem\FilesystemObject;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class FileReader {

    /** @var File File to write to */
    private $file = null;
    /** @var FileHandler File handler */
    private $handler = null;
    /** @var FileAccessMode File access mode used to access the file. */
    private $fileMode;

    // TODO: Ability to set context, instead of just the context in the open() method.

    /**
     * Constructor.
     *
     * @param FilesystemObject|string $file File instance or the path of a file as a string of the file to read from.
     * The file or file path must be an existing file.
     *
     * @throws \Exception Throws an exception on error.
     */
    public function __construct($file) {
        // Set the default file access mode
        $this->fileMode = FileAccessModeFactory::createReadOnlyMode();

        // Get and store $file as File instance, throw an exception if failed
        if(($file = File::asFile($file)) === null)
            // TODO: Throw a better exception!
            throw new \Exception("Invalid file!");

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
     * Get the file access mode which is used by the file handler to open the file.
     *
     * @return FileAccessMode The used file access mode.
     */
    public function getFileAccessMode() {
        return $this->fileMode;
    }

    /**
     * Set the file access mode used by the file handler to open the file.
     * This will reopen the file handler if the file access mode changed and it was opened already.
     *
     * @param FileAccessMode|string $fileMode The file access mode instance or the file access mode as a string that
     * should be used by the file handler to open the file.
     *
     * @return bool True on success, false on failure.
     */
    public function setFileAccessMode($fileMode) {
        // Convert $fileMode into a FileAccessMode instance
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
     * Get the file which is being read from.
     *
     * @return File|null The file to read from, or null if no file was set.
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Set the file to read from. The file must be an existing file.
     *
     * @param File|string $file File instance or the file as a path string to read from.
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

        // Make sure the file is an existing file
        if(!$file->isFile())
            return false;

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
     * Open the file handler to read from.
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
     * Close the file handler to read from.
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
     * Read a line from the file starting at the file pointer of the file handler.
     * The file handler will be opened automatically as it's needed.
     * Reading ends when the maximum number of bytes is read, if specified by $length.
     * Reading ends when a new line is reached (the new line character is included in the output).
     * Reading ends when the end of the file is reached.
     * This method is binary safe.
     *
     * @param int|null $length [optional] Specifies the maximum number of bytes to read,
     * null to disable a maximum length.
     *
     * @return string|null The read data, or null if an error occurred.
     */
    public function getLine($length = null) {
        // Open the file handler if it isn't opened yet
        if(!$this->open())
            return null;

        // Get and return the line
        return $this->handler->getLine($length);
    }

    /**
     * Read a line from the file starting at the file pointer of the file handler.
     * The file handler will be opened automatically as it's needed.
     * This method attempts to strip HTML, PHP tags and NUL bytes.
     * Reading ends when the maximum number of bytes is read, if specified by $length.
     * Reading ends when a new line is reached (the new line character is included in the output).
     * Reading ends when the end of the file is reached.
     * This method is binary safe.
     *
     * @param int|null $length [optional] Specifies the maximum number of bytes to read,
     * null to disable a maximum length.
     * @param string $allowTags [optional] A list of allowed tags, which shouldn't be stripped by this method.
     * Whitespaces aren't allowed, and tags are case-insensitive.
     *
     * @return string|null The read data, or null if the end of the file was reached or if an error occurred.
     */
    public function getLineStripped($length = null, $allowTags = '') {
        // Open the file handler if it isn't opened yet
        if(!$this->open())
            return null;

        // Get and return the stripped line
        return $this->handler->getLineStripped($length, $allowTags);
    }

    /**
     * Read the next character from the file starting at the file pointer of the file handler.
     * The file handler will be opened automatically as it's needed.
     * This method is binary safe.
     *
     * @return string|null The read character, or null if the end of the file was reached or if an error occurred.
     */
    public function getChar() {
        // Open the file handler if it isn't opened yet
        if(!$this->open())
            return null;

        // Get and return the next character
        return $this->handler->getChar();
    }

    /**
     * Read the contents of the file starting at the file pointer of the file handler.
     * The file handler will be opened automatically as it's needed.
     * Reading ends when the maximum number of bytes is read, specified by $length.
     * Reading ends when the end of the file is reached.
     * Reading ends when the stream is read buffered and it does not represent a plain file,
     * at most one read of up to a number of bytes equal to the chunk size (usually 8192) is made;
     * depending on the previously buffered data, the size of the returned data may be larger than the chunk size.
     * This method is binary safe.
     *
     * @param int|null $length [optional] Specifies the maximum number of bytes to read, null to ignore this parameter
     * and to use the length of the file.
     *
     * @return string|null The read data, or null if the end of the file was reached or if an error occurred.
     */
    public function read($length = null) {
        // Open the file handler if it isn't opened yet
        if(!$this->open())
            return null;

        // Use the length of the file if no length was set
        if($length === null)
            $length = $this->file->getSize();

        // Read and return
        return $this->handler->read($length);
    }

    /**
     * Set the file position indicator of the file handler. The file handler will be opened as it's needed.
     * The new position of the indicator is measured in bytes from the beginning of the file.
     * This method moves the indicator to the $whence position with $offset added.
     *
     * @param int $offset The offset. To move to a position before the end-of-file, you need to pass a negative value in
     * $offset and set $whence to SEEK_END.
     * @param int $whence Positioning origin, choose from:<br>
     * SEEK_SET: Set position equal to offset bytes.<br>
     * SEEK_CUR: Set position to current location plus offset.<br>
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
     * Check whether the file we're reading from is valid. The file must be an existing file.
     *
     * @return bool True if the file is valid and exists, false otherwise.
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
}
