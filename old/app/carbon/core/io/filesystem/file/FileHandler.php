<?php

namespace carbon\core\io\filesystem\file;

use carbon\core\io\filesystem\file\accessmode\FileAccessMode;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class FileHandler {

    // TODO: Shared file handle feature!

    /** @var File The handled file. */
    private $file;
    /** @var resource Filesystem object handle of the file */
    protected $handle = null;
    /** @var FileAccessMode The file access mode used by the handler. */
    private $fileMode = null;

    /**
     * Constructor.
     *
     * @param File|string $file The file or the file path as a string of the file that needs to be handled.
     *
     * @throws \Exception Throws an exception when the file isn't valid.
     */
    public function __construct($file) {
        // Set the handled file, throw an exception on failure
        if(!$this->setFile($file))
            throw new \Exception();
            // TODO: Throw a custom exception on error!
    }

    /**
     * Destructor, to ensure the file handle is closed safely before destroying the object.
     */
    public function __destruct() {
        // Make sure to close the file handle before destroying the object.
        $this->close();
    }

    /**
     * Get the handled file.
     *
     * @return File Handled file.
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Set the handled file. Automatically reopens the file handle if the handle was opened with a different file.
     *
     * @param File|\carbon\core\io\filesystem\FilesystemObject|string $file Handled file, a file or filesystem object
     * or the path of a file as a string.
     * @param FileAccessMode|string $mode [optional] The file access mode used by the handler as file access mode
     * instance or as PHPs file access mode string. See PHPs fopen() function for more details. This file access mode
     * will be used if the handle needs to be reopened. If set to null, the current file access mode will be used.
     * @param resource $context [optional] See PHPs fopen() function for more details.
     * This file context will be used if the handle needs to be reopened.
     * If set to null, the current file context will be used.
     *
     * @return bool True if succeed, false on failure. Also returns true if the file wasn't changed.
     *
     * @see fopen();
     */
    public function setFile($file, $mode = null, $context = null) {
        // Get $file as File instance and $mode as FileAccessMode instance, return false on failure
        if(($file = File::asFile($file)) === null)
            return false;
        $mode = FileAccessMode::asFileAccessMode($mode);

        // Make sure the file changed
        if($this->file === $file)
            return true;

        // Set the file instance, return the result
        $this->file = $file;

        // Reopen the file handler if it's opened already, return the result
        if($this->isOpened())
            return $this->reopen($mode->getMode(), $context);
        return true;
    }

    /**
     * Open the file handle.
     *
     * @param FileAccessMode|string $mode [optional] The file access mode used by the handler as file access mode
     * instance or as PHPs file access mode string. See PHPs fopen() function for more details.
     * @param resource $context [optional] See PHPs fopen() function for more details.
     *
     * @return bool True on success, false on failure. True will be returned if the handle was opened already.
     *
     * @see fopen();
     */
    public function open($mode, $context = null) {
        // Make sure the file handle isn't opened already
        if($this->isOpened())
            return true;

        // Convert $mode into a FileAccessMode instance, return false on failure
        if(($mode = FileAccessMode::asFileAccessMode($mode)) === null)
            return false;

        // Open the file handle
        // TODO: Improve the code bellow!
        if($context === null) {
            if(($this->handle = @fopen($this->file->getPath(), $mode->getMode(), false)) === false) {
                // Close the handle again, return false
                $this->close();
                return false;
            }
        } else {
            if(($this->handle = @fopen($this->file->getPath(), $mode->getMode(), false, $context)) === false) {
                // Close the handle again, return false
                $this->close();
                return false;
            }
        }

        // Set the file mode used
        $this->fileMode = $mode;

        // Return the result
        return true;
    }

    /**
     * Reopen the currently opened file handle. The file handle must be opened in order to reopen it.
     *
     * @param FileAccessMode|string $mode [optional] The file access mode used by the handler as file access mode
     * instance or as PHPs file access mode string. See PHPs fopen() function for more details. This file access mode
     * will be used if the handle needs to be reopened. If set to null, the current file access mode will be used.
     * @param resource|null $context [optional] See PHPs fopen() function for more details.
     * The context of the current opened file handle will be used if not specified.
     *
     * @return bool True on success, false on failure. False will also be returned if the file handle wasn't opened.
     *
     * @see fopen();
     */
    public function reopen($mode = null, $context = null) {
        // Make sure the file handle is opened
        if(!$this->isOpened())
            return false;

        // Try to convert $mode into a FileAccessMode instance if set
        $mode = FileAccessMode::asFileAccessMode($mode);

        // TODO: Store the current context!
        // Store the current file mode and context if not set
        if($mode === null)
            $mode = $this->fileMode;
        if($context === null)
            $context = null;

        // Close the file handle, return false on error
        if(!$this->close())
            return false;

        // Reopen the file handle, return the result
        return $this->open($mode, $context);
    }

    /**
     * Check whether the file handle is opened.
     *
     * @return bool True if the file handle is opened, false otherwise.
     */
    public function isOpened() {
        // Check whether $handle is null or false
        if($this->handle === null || $this->handle === false)
            return false;

        // Check whether the handle is a valid resource, return the result
        return is_resource($this->handle);
    }

    /**
     * Close the file handle if it's opened.
     *
     * @return bool True on success, false on failure. True will also be returned if no handle was opened.
     */
    public function close() {
        // Make sure the file handle is opened
        if(!$this->isOpened())
            return true;

        // Close the file handle, return false on failure
        if(@fclose($this->handle) === false) {
            // Set $handle to null, return the result
            $this->handle = null;
            return false;
        }

        // Set the file access mode
        $this->fileMode = null;

        // Set $handle to null, return the result
        $this->handle = null;
        return true;
    }

    /**
     * Get the file access mode which is used by the currently opened file handle.
     *
     * @return FileAccessMode|null The file access mode instance which is used by the currently opened file handle.
     * Null will be returned if no file handle was opened.
     */
    public function getFileAccessMode() {
        // Check whether the file handle is closed, if so return null
        if(!$this->isOpened())
            return null;

        // Return the used file access mode
        return $this->fileMode;
    }

    /**
     * Rewind the file handle.
     *
     * @return bool True if succeed, false if failed because no file handle was opened.
     */
    public function rewind() {
        // Make sure a file handle is opened
        if(!$this->isOpened())
            return false;

        // Rewind the file handle, return the result
        return rewind($this->handle);
    }

    /**
     * Read a line from the file starting at the file pointer. The file handle must be opened.
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
        // Read the line, return null if an error occurs
        if(($out = fgets($this->handle, $length)) === false)
            return null;

        // Return the read data
        return $out;
    }

    /**
     * Read a line from the file starting at the file pointer. The file handle must be opened.
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
        // Read the line with stripped tags, return null if an error occurs
        if(($out = fgetss($this->handle, $length, $allowTags)) === false)
            return null;

        // Return the read data
        return $out;
    }

    /**
     * Read the next character from the file starting at the file pointer. The file handle must be opened.
     * This method is binary safe.
     *
     * @return string|null The read character, or null if the end of the file was reached or if an error occurred.
     */
    public function getChar() {
        // Read the character, return null if an error occurs
        if(($out = fgetc($this->handle)) === false)
            return null;

        // Return the read character
        return $out;
    }

    /**
     * Read the contents of the file starting at the file pointer. The file handle must be opened.
     * Reading ends when the maximum number of bytes is read, specified by $length.
     * Reading ends when the end of the file is reached.
     * Reading ends when the stream is read buffered and it does not represent a plain file,
     * at most one read of up to a number of bytes equal to the chunk size (usually 8192) is made;
     * depending on the previously buffered data, the size of the returned data may be larger than the chunk size.
     * This method is binary safe.
     *
     * @param int $length [optional] Specifies the maximum number of bytes to read. This value must be an integer which
     * equals or is greater than zero.
     *
     * @return string|null The read data, or null if the end of the file was reached or if an error occurred.
     */
    public function read($length) {
        // Make sure the length is valid
        if(!is_int($length) || $length < 0)
            return null;

        // Read the data, return null if an error occurs
        if(($out = fread($this->handle, $length)) === false)
            return null;

        // Return the read data
        return $out;
    }

    /**
     * Write a string to a file starting at the file pointer. The file handle must be opened.
     * Writing ends when the end of the string is reached, or when the number of written bytes equals $length.
     * This method is binary safe.
     *
     * @param string $str The string to write to the file.
     * @param int|null $length [optional] The maximum number of bytes to write, or null to ignore a maximum length.
     *
     * @return int|null The number of written bytes, or null on failure.
     */
    public function write($str, $length = null) {
        // Check whether $length is specified
        if($length !== null) {
            // Write the string to the file, return null if an error occurs
            if(($out = fwrite($this->handle, $str, $length)) === false)
                return null;
        } else
            // Write the string to the file, return null if an error occurs
            if(($out = fwrite($this->handle, $str)) === false)
                return null;

        // Return the number of written bytes
        return $out;
    }

    /**
     * Allows file locking and unlocking. Files are automatically unlocked when {@see close()} is called.
     *
     * @param int $operation Operation type. You can use:
     * - LOCK_SH to acquire a shared lock (reader).
     * - LOCK_EX to acquire an exclusive lock (writer).
     * - LOCK_UN to release a lock (shared or exclusive).
     * It is also possible to add LOCK_NB as a bitmask to any of the above operations if you don't want to block while
     * locking, this is not supported on Windows.
     * @param int|null $wouldBlock [optional] This argument is set to 1 if the lock would block
     * (EWOULDBLOCK errno condition), this is not supported on Windows.
     *
     * @return bool True on success, false on failure.
     */
    public function lock($operation, $wouldBlock = null) {
        return flock($this->handle, $operation, $wouldBlock);
    }

    /**
     * Set the file position indicator. The file handle must be opened.
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
        return fseek($this->handle, $offset, $whence) === 0;
    }

    /**
     * Passes through the file starting at the file pointer until the end of the file is reached.
     * The file handle must be opened. This method is binary safe.
     *
     * @return int|null The number of passed characters, or null if the end of the file was reached or if an error occurred.
     */
    public function passthru() {
        // Passthru the data, return null if an error occurs
        if(($count = fpassthru($this->handle)) === false)
            return null;

        // Return the number of passed bytes
        return $count;
    }

    /**
     * Truncate the file to a given length.
     *
     * @param int $size [optional] The size to truncate to measured in bytes.<br>
     * If $size is larger than the file then the file is extended with null bytes.<br>
     * If $size is smaller than the file then the file is truncated to that size.
     *
     * @return bool True on success, false on failure.
     */
    public function truncate($size = 0) {
        // Truncate the file, return the result
        return ftruncate($this->handle, $size);
    }

    /**
     * Get the file handle.
     *
     * @return resource File handle as resource, null will be returned if the file handle isn't opened.
     */
    public function getHandle() {
        // Make sure the file handler is opened, if not return null
        if(!$this->isOpened())
            return null;

        // Return the file handle
        return $this->handle;
    }

    /**
     * Get the ID of the PHP resource which is used by the opened file handle. The file handler must be opened.
     *
     * @return int|null The ID of the file handle, null on failure.
     */
    public function getHandleId() {
        // Make sure the file handler is opened
        if(!$this->isOpened())
            return null;

        // Return the ID of the handle
        return intval($this->handle);
    }

    /**
     * Check whether the file handler is valid. The file handler is valid if a valid file is set.
     *
     * @return bool True if the file handler is valid, false otherwise.
     */
    public function isValid() {
        // Make sure a file is set
        if($this->file === null)
            return false;

        // Check whether the file is valid, return the result
        return $this->file->isValid();
    }
}
