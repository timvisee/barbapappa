<?php

namespace carbon\core\io\filesystem\directory;

use carbon\core\io\filesystem\file\File;
use carbon\core\io\filesystem\FilesystemObject;
use carbon\core\io\filesystem\symboliclink\SymbolicLink;
use carbon\core\util\StringUtils;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class DirectoryHandler {

    // TODO: Shared file handle feature!

    /** @var Directory The handled directory. */
    private $dir;
    /** @var resource Filesystem object handle for the directory */
    protected $handle = null;

    /**
     * Constructor.
     *
     * @param Directory|string $dir The directory or the directory path as a string of the directory that needs to be
     * handled. The directory must exist before the handle can be opened.
     *
     * @throws \Exception Throws an exception when the directory isn't valid.
     */
    public function __construct($dir) {
        // Set the handled directory, throw an exception on failure
        if(!$this->setDirectory($dir))
            throw new \Exception();
        // TODO: Throw a custom exception on error!
    }

    /**
     * Destructor, to ensure the directory handle is closed safely before destroying the object.
     */
    public function __destruct() {
        // Make sure to close the directory handle before destroying the object.
        $this->close();
    }

    /**
     * Get the handled directory.
     *
     * @return Directory Handled directory.
     */
    public function getDirectory() {
        return $this->dir;
    }

    /**
     * Set the handled directory. Automatically reopens the directory handle if the handle was opened with a different
     * directory. The directory must exist before the handle can be opened.
     *
     * @param Directory|FilesystemObject|string $dir Handled directory, a directory or filesystem object
     * or the path of a directory as a string.
     *
     * @param resource $context [optional] See PHPs opendir() function for more details.
     * This directory context will be used if the handle needs to be reopened.
     * If set to null, the current directory context will be used.
     *
     * @return bool True if succeed, false on failure. Also returns true if the directory wasn't changed.
     *
     * @see opendir();
     */
    public function setDirectory($dir, $context = null) {
        // Get $dir as Directory instance, return false on failure
        if(($dir = Directory::asDirectory($dir)) === null)
            return false;

        // Make sure the directory changed
        if($this->dir === $dir)
            return true;

        // Set the directory instance, return the result
        $this->dir = $dir;

        // Reopen the directory handler if it's opened already, return the result
        if($this->isOpened())
            return $this->reopen($context);
        return true;
    }

    /**
     * Open the directory handle. The directory must exist before a handle can be opened.
     *
     * @param resource $context [optional] See PHPs opendir() function for more details.
     *
     * @return bool True on success, false on failure. True will be returned if the handle was opened already.
     *
     * @see opendir();
     */
    public function open($context = null) {
        // Make sure the directory handle isn't opened already
        if($this->isOpened())
            return true;

        // Make sure the directory is valid and existing
        if(!$this->dir->isDirectory())
            return false;

        // Open the directory handle
        // TODO: Improve the code bellow!
        if($context === null) {
            if(($this->handle = @opendir($this->dir->getPath())) === false) {
                // Close the handle again, return false
                $this->close();
                return false;
            }
        } else {
            if(($this->handle = @opendir($this->dir->getPath(), $context)) === false) {
                // Close the handle again, return false
                $this->close();
                return false;
            }
        }

        // Return the result
        return true;
    }

    /**
     * Reopen the currently opened directory handle. The directory handle must be opened in order to reopen it.
     *
     * @param resource|null $context [optional] See PHPs opendir() function for more details.
     * The context of the current opened directory handle will be used if not specified.
     *
     * @return bool True on success, false on failure. False will also be returned if the directory handle wasn't
     * opened.
     *
     * @see opendir();
     */
    public function reopen($context = null) {
        // Make sure the directory handle is opened
        if(!$this->isOpened())
            return false;

        // TODO: Store the current context!
        // Store the current directory context if not set
        if($context === null)
            $context = null;

        // Close the directory handle, return false on error
        if(!$this->close())
            return false;

        // Reopen the directory handle, return the result
        return $this->open($context);
    }

    /**
     * Check whether the directory handle is opened.
     *
     * @return bool True if the directory handle is opened, false otherwise.
     */
    public function isOpened() {
        // Check whether $handle is null or false
        if($this->handle === null || $this->handle === false)
            return false;

        // Check whether the handle is a valid resource, return the result
        return is_resource($this->handle);
    }

    /**
     * Close the directory handle if it's opened.
     *
     * @return bool True on success, false on failure. True will also be returned if no handle was opened.
     */
    public function close() {
        // Make sure the directory handle is opened
        if(!$this->isOpened())
            return true;

        // Close the directory handle
        @closedir($this->handle);

        // Set $handle to null, return the result
        $this->handle = null;
        return true;
    }

    /**
     * Get the ID of the PHP resource which is used by the opened directory handle.
     * The directory handler must be opened.
     *
     * @return int|null The ID of the directory handle, null on failure.
     */
    public function getHandleId() {
        // Make sure the directory handler is opened
        if(!$this->isOpened())
            return null;

        // Return the ID of the handle
        return intval($this->handle);
    }

    /**
     * Rewind the directory handle. This will set the directory pointer to the beginning of the directory which causes
     * the following directory scans to start from the beginning.
     *
     * @return bool True if succeed, false if failed because no directory handle was opened.
     */
    public function rewind() {
        // Make sure a file handle is opened
        if(!$this->isOpened())
            return false;

        // Rewind the file handle, return the result
        @rewinddir($this->handle);
        return true;
    }

    /**
     * Get the next entry from the directory handle.
     *
     * @param bool $ignorePeriodDirs [optional] True to ignore period directories such as /. and /.. .
     *
     * @return File|Directory|SymbolicLink|FilesystemObject|null The next filesystem object from the directory.
     */
    public function read($ignorePeriodDirs = true) {
        // Make sure a directory handle is opened
        if(!$this->isOpened())
            return null;

        // Read the next entry from the directory handle, and make sure it's valid
        // TODO: Should we close the handle when null is returned?
        if(($entry = readdir($this->handle)) === false)
            return null;

        // Process single and double period entries
        if(($entry === '.' || $entry == '..') && $ignorePeriodDirs)
            return $this->read($ignorePeriodDirs);

        // Return period directories
        if($entry === '.')
            return $this->dir;
        else if($entry === '..')
            return $this->dir->getParent();

        // Create a proper filesystem object instance of the entry, return the result
        return FilesystemObject::from($this->dir, $entry);
    }

    /**
     * List files and directories in the directory.
     *
     * @param int $sortOrder [optional] The order of the entries being returned. Using alphabetical order by default.
     * Order types:
     * - SCANDIR_SORT_ASCENDING
     * - SCANDIR_SORT_DESCENDING
     * - SCANDIR_SORT_NONE
     * @param resource $context [optional] The directory context. See PHPs scandir() function for more information.
     * @param bool $ignorePeriodDirs [optional] True to ignore period dirs such as /. and /.., false otherwise.
     *
     * @return Array|null A list of filesystem objects as an array or null on failure.
     *
     * @see scandir();
     */
    public function scan($sortOrder = SCANDIR_SORT_ASCENDING, $context, $ignorePeriodDirs = false) {
        // Make sure the directory handle was opened
        if(!$this->isOpened())
            return null;

        // Scan the directory
        if($context !== null)
            $scan = scandir($this->handle, $sortOrder, $context);
        else
            $scan = scandir($this->handle, $sortOrder);

        // Make sure the result was valid
        if($scan === false)
            return null;

        // Create an array of filesystem objects
        $entries = Array();
        foreach($scan as $entry) {
            // Check whether period directories should be ignored
            if($ignorePeriodDirs && (StringUtils::equals($entry, Array('.', '..'), false, true)))
                continue;

            $entries[] = FileSystemObject::from($this->dir, $entry);
        }

        // Return the result
        return $entries;
    }

    /**
     * Get the directory handle.
     *
     * @return resource Directory handle as resource, null will be returned if the directory handle isn't opened.
     */
    public function getHandle() {
        // Make sure the directory handle is opened, if not return null
        if(!$this->isOpened())
            return null;

        // Return the directory handle
        return $this->handle;
    }

    /**
     * Check whether the directory handler is valid. The directory handler is valid when a valid directory is set.
     *
     * @return bool True if the directory handler is valid, false otherwise.
     */
    public function isValid() {
        // Make sure a directory is set
        if($this->dir === null)
            return false;

        // Make sure the directory is valid, return the result
        return $this->dir->isValid();
    }
}
 