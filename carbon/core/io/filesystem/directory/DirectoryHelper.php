<?php

namespace carbon\core\io\filesystem\directory;

use carbon\core\io\filesystem\FilesystemObject;
use carbon\core\io\filesystem\FilesystemObjectHelper;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class DirectoryHelper extends FilesystemObjectHelper {

    /**
     * Get the Directory instance from a FileSystemObject instance or the path a string from a directory.
     * If the filesystem object is an existing file or symbolic link, $default will be returned.
     * The directory or directory path has to be valid.
     *
     * @param FilesystemObject|string $dir Filesystem object instance or the path of a directory as a string.
     * @param mixed|null $default [optional] Default value returned if the directory couldn't be instantiated,
     * possibly because the $dir param was invalid.
     *
     * @return Directory|mixed The directory as a Directory instance.
     * Or the $default param value if the directory couldn't be cast to a Directory instance.
     */
    public static function asDirectory($dir, $default = null) {
        // Create a new directory instance when $dir is a string, or when $dir is a FileSystemObject instance
        // but not a Directory
        if(is_string($dir) || ($dir instanceof FilesystemObject && !$dir instanceof Directory))
            $dir = new Directory($dir);

        // The $dir must be a Directory instance, if not, return the default
        if(!$dir instanceof Directory)
            return $default;

        // Make sure the directory is valid, if not, return the $default value
        if(!$dir->isValid())
            return $default;

        // Return the directory
        return $dir;
    }

    /**
     * Delete all the contents of a directory.
     *
     * @param Directory|string $dir Directory instance or directory path as a string to delete the contents from.
     * @param resource $context [optional] See the unlink() function for documentation.
     *
     * @return int
     *
     * @see unlink()
     */
    public static function deleteContents($dir, $context = null) {
        // Convert the directory into a path string, return the $default value if failed
        if(($dir = self::asPath($dir, false)) === null)
            return -1;

        // The directory must exist
        if(!self::isDirectory($dir))
            return -1;

        // Count the deleted files, symbolic links and directories
        $count = 0;

        // Create a directory scanner, then list and delete all directory contents
        $scanner = new DirectoryScanner($dir);
        while(($item = $scanner->read()) !== null)
            $count += self::delete($item, $context, true);

        // Return the number of removed files and directories
        return $count;
    }

    /**
     * Get all contents of a directory.
     *
     * @param Directory|string $dir Directory instance or the directory as a path string to get the contents from.
     * @param bool $recursive [optional] True to read all directory contents recursively.
     * @param int $types [optional] The type of filesystem objects to include. Defaults to FLAG_TYPE_ALL which allows all
     * filesystem object types. Choose from:
     * - FLAG_TYPE_OBJECT
     * - FLAG_TYPE_FILE
     * - FLAG_TYPE_DIRECTORY
     * - FLAG_TYPE_SYMBOLIC_LINK
     * - FLAG_TYPE_ALL
     * Use null to default to FLAG_TYPE_ALL.
     * @param mixed|null $default [optional] The default value to return on failure.
     *
     * @return array|mixed|null An array with all directory contents, or the $default value on failure.
     *
     * @see FilesystemObjectFlags
     */
    // TODO: Better $types description!
    public static function getContents($dir, $recursive = false, $types = self::FLAG_TYPE_ALL, $default = null) {
        // Convert the directory into a path string, return the $default value if failed
        if(($dir = self::asPath($dir, false)) === null)
            return $default;

        // Make sure the filesystem object is a directory, and exists
        if(!self::isDirectory($dir))
            return null;

        // Create a directory scanner
        $scanner = new DirectoryScanner($dir, true);

        // Scan the directory and return the result if it's valid
        if(($out = $scanner->readAll($recursive, $types)) === null)
            return $default;
        return $out;
    }

    /**
     * Validate a directory or the path of a directory. The directory doesn't need to exist.
     * The directory may not be an existing file or symbolic link.
     *
     * @param \carbon\core\io\filesystem\FilesystemObject|string $dir Filesystem object instance or the path of a directory as a string.
     *
     * @return bool True if the directory path seems to be valid, false otherwise.
     */
    public static function isValid($dir) {
        // Convert the directory into a string, return the false if failed
        if(($dir = self::asPath($dir, false)) === null)
            return false;

        // Make sure the directory is valid as FileSystemObject
        if(!FilesystemObjectHelper::isValid($dir))
            return false;

        // Make sure the directory isn't a file or symbolic link, return the result
        return !(FilesystemObjectHelper::isFile($dir) || FilesystemObjectHelper::isSymbolicLink($dir));
    }
}