<?php

namespace carbon\core\io\filesystem\directory;

use carbon\core\io\filesystem\FilesystemObject;
use carbon\core\io\filesystem\file\File;
use carbon\core\io\filesystem\FilesystemObjectFlags;
use carbon\core\io\filesystem\symboliclink\SymbolicLink;
use Exception;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class DirectoryScanner implements FilesystemObjectFlags {

    /** @var Directory Directory to scan. */
    private $dir = null;
    /** @var DirectoryHandler Directory handler instance. */
    private $handler = null;
    /**
     * @var File The last successfully scanned directory entry.
     * A successfully readAll() scan will cause the last entry from the returned list to be remembered.
     */
    private $lastRead = null;
    /**
     * @var bool True if period directories such as /. and /.. are ignored and skipped while scanning the directory,
     * false if they're included.
     */
    private $ignorePeriodDirs = true;

    // TODO: Automatically close the handler when this object is destroyed

    /**
     * Constructor.
     *
     * @param Directory|string $dir Directory instance or a directory path as string to scan.
     *
     * @throws Exception Throws an exception $dir isn't a directory or doesn't exist.
     */
    public function __construct($dir) {
        // Set the directory
        if(!$this->setDirectory($dir))
            // TODO: Throw an exception!
            throw new Exception();
    }

    /**
     * Destructor.
     * Close the directory handler properly before deconstructing.
     */
    public function __destruct() {
        // Close the directory handler if it's opened
        $this->close();
    }

    /**
     * Get the directory.
     *
     * @return Directory|null Directory instance. Null will be returned if no directory was set.
     */
    public function getDirectory() {
        return $this->dir;
    }

    /**
     * Set the directory to scan
     *
     * @param Directory|string $dir Directory instance or a directory path as string.
     *
     * @return bool True on success, false on failure.
     */
    public function setDirectory($dir) {
        // Convert $dir into a Directory instance, return false on failure
        if(($dir = DirectoryHelper::asDirectory($dir)) === null)
            return false;

        // Set the directory
        $this->dir = $dir;

        // Set the directory in the directory handler if it's initialized, return the result
        if($this->handler !== null)
            return $this->handler->setDirectory($dir);
        return true;
    }

    /**
     * Check whether period directory entries such as /. and /.. are ignored and skipped while scanning the directory.
     *
     * @return bool True if period directories are ignored and skipped, false if they're included.
     */
    public function isIgnorePeriodDirectories() {
        return $this->ignorePeriodDirs;
    }


    /**
     * Set whether period directory entries such as /. and /.. should be ignored and skipped while scanning the
     * directory.
     *
     * @param bool $ignorePeriodDirs True to ignore and skip the period directories, false to include them.
     */
    public function setIgnorePeriodDirectories($ignorePeriodDirs) {
        $this->ignorePeriodDirs = $ignorePeriodDirs;
    }

    /**
     * Open the directory handle. The directory handler is opened automatically as it's required.
     *
     * @return bool True on success, false on failure. Returns true if the directory handle was opened already.
     */
    public function open() {
        // Make sure the handler isn't opened already
        if($this->isOpened())
            return true;

        // Open the director handler or instantiate a new one if required, return the result
        if($this->handler === null)
            $this->handler = new DirectoryHandler($this->dir);
        return $this->handler->open();
    }

    /**
     * Check whether the directory handle is opened.
     *
     * @return bool True if the directory is opened, false otherwise.
     */
    public function isOpened() {
        // Make sure the handler is instantiated
        if($this->handler === null)
            return false;

        // Check whether the handler is opened, return the result
        return $this->handler->isOpened();
    }

    /**
     * Close the currently opened directory handle.
     *
     * @return bool True on success, false on failure. True will also be returned if no directory handle was opened.
     */
    public function close() {
        // Make sure the directory handler is opened
        if($this->isOpened())
            return false;

        // CLose the directory handler, return the result
        return $this->handler->close();
    }

    /**
     * Rewind the directory handler. The directory handler is opened automatically as it's required.
     *
     * @return bool True if succeed, false if failed because no directory handle was opened.
     */
    public function rewind() {
        // Open the directory handler if it isn't opened yet
        $this->open();

        // Rewind the directory handler, return the result
        return $this->handler->rewind();
    }

    /**
     * Get the next entry from the directory handle. The directory handler is opened automatically as it's required.
     *
     * @return File|Directory|SymbolicLink|FilesystemObject|null The next filesystem object from the directory.
     */
    // TODO: Improve the quality of this method
    public function read() {
        // Open the directory handler if it isn't opened yet
        $this->open();

        // Read from the directory handler, and store the last read directory
        if(($read = $this->handler->read($this->ignorePeriodDirs)) === null)
            return null;

        // Set the last read entry and return the result
        $this->lastRead = $read;
        return $read;
    }

    /**
     * Get the successfully read directory entry from this directory scanner. Returns null if no entry was read yet.
     *
     * @return File|Directory|SymbolicLink|FilesystemObject|null The last successfully read directory entry.
     */
    public function getLastRead() {
        return $this->lastRead;
    }

    /**
     * Read all next entries from the directory handle, starting after the last entry that was read.
     * The directory handler is opened automatically as it's required. The directory handle will be rewind automatically
     * after calling this method.
     *
     * @param bool $recursive True to scan the directory recursively.
     * @param int $types [optional] The type of filesystem objects to read. Defaults to FLAG_TYPE_ALL which allows all
     * filesystem object types. Choose from:
     * - FLAG_TYPE_OBJECT
     * - FLAG_TYPE_FILE
     * - FLAG_TYPE_DIRECTORY
     * - FLAG_TYPE_SYMBOLIC_LINK
     * - FLAG_TYPE_ALL
     * Use null to default to FLAG_TYPE_ALL.
     *
     * @return Array|null An array of all directory entries, or null on failure.
     *
     * @see FilesystemObjectFlags
     */
    public function readAll($recursive = false, $types = self::FLAG_TYPE_ALL) {
        // Default to FLAG_TYPE_ALL if $types is set to null
        if($types === null)
            $types = self::FLAG_TYPE_ALL;

        // Open the directory handler if it isn't opened yet
        $this->open();

        // Create an array to store all filesystem objects in
        $entries = Array();

        // Read all entries form the directory handle
        while(($entry = $this->read($this->ignorePeriodDirs)) !== null) {
            // Check this filesystem object type should be included
            if($entry instanceof File) {
                if(!($types & self::FLAG_TYPE_FILE))
                    continue;

            } elseif($entry instanceof Directory) {
                if(!($types & self::FLAG_TYPE_DIRECTORY))
                    continue;

            } elseif($entry instanceof Directory) {
                if(!($types & self::FLAG_TYPE_SYMBOLIC_LINK))
                    continue;

            } elseif(!($types & self::FLAG_TYPE_OBJECT))
                continue;

            // Add the entry to the list
            $entries[] = $entry;

            // Check whether the scan is recursive and check whether the current object is a directory
            if($recursive && $entry->isDirectory()) {
                // Create a new scanner to scan the current object recursively
                $scanner = new self($entry);

                // Scan all directory objects, make sure the result is valid
                if(($items = $scanner->readAll($recursive, $types, $this->ignorePeriodDirs)) === null)
                    return null;

                // Add the objects to the list
                $entries = array_merge($entries, $items);
            }
        }

        // Rewind the directory handler
        $this->rewind();

        // Return the list of filesystem objects
        return $entries;
    }

    /**
     * Count the number of directory entries in this directory.
     * Period directories such as /. and /.. are ignored if $ignorePeriodDirs is set to true.
     * Warning: This method might be resource expensive.
     *
     * @param resource $context [optional] The directory context. See the DirectoryHandler::scan() method for more information.
     *
     * @return int The number of entries in this directory, or -1 on failure.
     *
     * @see DirectoryHandler::scan();
     */
    public function count($context = null) {
        // Open the directory handler if it isn't opened yet
        $this->open();

        // Scan the directory, return -1 on failure
        if(($entries = $this->handler->scan(SCANDIR_SORT_NONE, $context, $this->ignorePeriodDirs)) === null)
            return -1;

        // Count and return the directory entries
        return count($entries);
    }

    /**
     * Check whether the directory is valid
     *
     * @return bool True if the directory is valid, false otherwise
     */
    public function isValid() {
        // Make sure the directory is set
        if($this->dir === null)
            return false;

        // Check whether the directory is valid, return the result
        return $this->dir->isValid();
    }
}
