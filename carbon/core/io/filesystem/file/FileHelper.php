<?php

namespace carbon\core\io\filesystem\file;

use carbon\core\io\filesystem\file\accessmode\FileAccessModeFactory;
use carbon\core\io\filesystem\FilesystemObject;
use carbon\core\io\filesystem\FilesystemObjectHelper;
use carbon\core\io\filesystem\permissions\SystemGroup;
use carbon\core\io\filesystem\permissions\SystemUser;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class FileHelper extends FilesystemObjectHelper {

    // TODO: Only allow $default to be returned on functions getting data!

    /**
     * Get the File instance from a FileSystemObject instance or the path a string from a file.
     * If the filesystem object is an existing directory or symbolic link, $default will be returned.
     * The file or file path has to be valid.
     *
     * @param FilesystemObject|string $file Filesystem object instance or the path of a file as a string.
     * @param mixed|null $default [optional] Default value returned if the file couldn't be instantiated,
     * possibly because the $file param was invalid.
     *
     * @return File|mixed The file as a File instance.
     * Or the $default param value if the file couldn't be cast to a File instance.
     */
    // TODO: Update this parser method!
    public static function asFile($file, $default = null) {
        // Return the instance if it's already a file
        if($file instanceof File)
            return $file;

        // Create a new file instance when $file is a string, or when $file is a FileSystemObject instance
        // but not a File
        if(is_string($file) || ($file instanceof FilesystemObject && !($file instanceof File)))
            $file = new File($file);

        // The $file must be a file instance, if not, return the default
        if(!($file instanceof File))
            return $default;

        // Make sure the file is valid, if not, return the $default value
        if(!$file->isValid())
            return $default;

        // Return the file
        // TODO: Is this valid?
        return $file;
    }

    /**
     * Create a file if it doesn't exist.
     *
     * @param File|string $file File instance or a file path as a string of the file which should be created.
     *
     * @return bool True if the file was created, false otherwise.
     * False will also be returned if the file already existed.
     */
    public static function createFile($file) {
        // Convert the file into a path string, return the $default value if failed
        if(($file = self::asPath($file, false)) === null)
            return false;

        // Make sure the file doesn't exist
        if(self::exists($file))
            return false;

        // TODO: Create the file recursively, for non existing directories!

        // Create and open the file, return false on error
        $handler = new FileHandler($file);
        if(!$handler->open('w'))
            return false;

        // Close the file handle again, return the result
        return $handler->close();
    }

    /**
     * Get the extension of a file. File names ending with a period, do have an extension.
     *
     * @param File|string $file File instance or file path as a string to get the extension from.
     * @param bool $withPeriod [optional] True to include the period with the returned value, false to exclude the period.
     * @param mixed|null $default [optional] The default value to return on failure.
     *
     * @return string|mixed|null The file extension as a string. The $default value will be returned on failure.
     */
    public static function getExtension($file, $withPeriod = false, $default = null) {
        // Convert the file into a path string, return the $default value if failed
        if(($file = self::asPath($file, false)) === null)
            return $default;

        // Get the path extension info
        $info = pathinfo($file);

        // Return the extension, with or without a prefix period, or $default if the file doesn't have an extension.
        if(isset($info['extension']))
            return ($withPeriod ? '.' : '') . $info['extension'];
        return $default;
    }

    /**
     * Check whether the file has an extension. File names ending with a period, do have an extension.
     *
     * @param File|string $file File instance or file path as a string.
     *
     * @return bool True if the file has an extension, false otherwise. False will also be returned on failure.
     */
    public static function hasExtension($file) {
        return is_string(self::getExtension($file));
    }

    /**
     * Get the file owner.
     *
     * @param File|string $file File instance or file path as a string to get the owner from.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return SystemUser|null Owner of the file, or null on failure.
     */
    public static function getOwner($file, $default = null) {
        // Convert the file into a path string, return the $default value if failed
        if(($file = self::asPath($file, false)) === null)
            return $default;

        // Get the owners user ID, and make sure it's valid
        if(($uid = fileowner($file)) === false)
            return $default;

        // Return the owner
        return new SystemUser($uid);
    }

    /**
     * Get the file group.
     *
     * @param File|string $file File instance or file path as a string to get the group from.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return SystemGroup|null Group of the file, or null on failure.
     */
    public static function getGroup($file, $default = null) {
        // Convert the file into a path string, return the $default value if failed
        if(($file = self::asPath($file, false)) === null)
            return $default;

        // Make sure the group ID is valid, and make sure it's valid
        if(($gid = filegroup($file)) === false)
            return $default;

        // Return the group
        return new SystemGroup($gid);
    }

    /**
     * Get the size in bytes of a file.
     *
     * @param File|string $file File instance or file path as a string to get the size from.
     * @param mixed|null $default [optional] Default value returned on failure.
     *
     * @return int|mixed|null File size in bytes, or the default value on failure.
     */
    public static function getSize($file, $default = -1) {
        // Convert the file into a path string, return the $default value if failed
        if(($file = self::asPath($file, false)) === null)
            return $default;

        // Get and return the file size, if it's valid
        if(($size = filesize($file)) === false)
            return $default;
        return $size;
    }

    /**
     * Get the last access time of the file as a unix timestamp.
     *
     * @param File|string $file File instance or file path as a string to get the last access time from.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|null Last access time of the file as a unix timestamp, or null on failure.
     */
    public static function getLastAccessTime($file, $default = null) {
        // Convert the file into a path string, return the $default value if failed
        if(($file = self::asPath($file, false)) === null)
            return $default;

        // TODO: Return as Time instance?

        // Get and return the last access time, return $default on failure
        if(($time = fileatime($file)) === false)
            return $default;
        return $time;
    }

    /**
     * Get the inode change time of the file as unix timestamp.
     *
     * @param File|string $file File instance or file path as a string to get the change time from.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|null Inode change time of the file as unix timestamp, or null on failure.
     */
    public static function getChangeTime($file, $default = null) {
        // Convert the file into a path string, return the $default value if failed
        if(($file = self::asPath($file, false)) === null)
            return $default;

        // TODO: Return as Time instance?

        // Get and return the inode change time, return $default on failure
        if(($time = filectime($file)) === false)
            return $default;
        return $time;
    }

    /**
     * Get the last modification time of the file as a unix timestamp.
     *
     * @param File|string $file File instance or file path as a string to get the modification time from.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return int|null Last modification time of the file as unix timestamp, or null on failure.
     */
    public static function getModificationTime($file, $default = null) {
        // Convert the file into a path string, return the $default value if failed
        if(($file = self::asPath($file, false)) === null)
            return $default;

        // Get and return the last modification time, return $default on failure
        if(($time = filemtime($file)) === false)
            return $default;
        return $time;
    }

    /**
     * Touch a file and set the modification and access time.
     *
     * @param File|string $file File instance or file path as a string to touch.
     * @param int|null $time [optional] The modification time to set as a timestamp. Null to use the current time.
     * @param int|null $accessTime [optional] The access time to set as a timestamp. Null to use the $time value.
     *
     * @return bool True on success, false on failure.
     */
    public static function touchFile($file, $time = null, $accessTime = null) {
        // Convert the file into a path string, return false on failure
        if(($file = self::asPath($file, false)) === null)
            return false;

        // Touch the file, return the result
        return touch($file, $time, $accessTime);
    }

    /**
     * Get the contents of a file.
     *
     * @param File|string $file File instance or a file path as string to get the contents from.
     * @param resource $context [optional] See PHPs fopen() function for more details.
     * @param int|null $offset [optional] The offset of the file pointer to start reading from measured in bytes from
     * the beginning of the file, or null to ignore this parameter.
     * @param int|null $maxLength [optional] The maximum number of bytes to read from the file, or null to ignore this
     * parameter.
     * @param mixed|null $default [optional] The default value to return on failure.
     *
     * @return string|mixed|null The contents of the file, or $default on failure.
     *
     * @see fopen();
     */
    public static function getContents($file, $context = null, $offset = null, $maxLength = null, $default = null) {
        // Convert the file into a path string, return the $default value if failed
        if(($file = self::asPath($file, false)) === null)
            return $default;

        // Make sure the filesystem object is a file and exists
        if(!self::isFile($file))
            return $default;

        // Open the file with the file reader
        $reader = new FileReader($file);
        if(!$reader->open(null, $context))
            return $default;

        // Seek to the proper offset
        if(!$reader->seek($offset))
            return $default;

        // Read the file and return the result
        if(($contents = $reader->read($maxLength)) === null)
            return $default;
        return $contents;
    }

    /**
     * Put contents into a file. This will truncate the file if it exists already.
     * If the file doesn't exist, it will be created.
     *
     * @param File|string $file File instance or a file path as string to put the contents into.
     * @param string $data The data to put into the file.
     * @param resource $context [optional] See PHPs fopen() function for more details.
     *
     * @return int|null The number of written bytes, or null on failure.
     *
     * @see fopen();
     */
    public static function putContents($file, $data, $context = null) {
        // Convert the file into a path string, return the $default value if failed
        if(($file = self::asPath($file, false)) === null)
            return null;

        // Make sure the path is valid
        if(!self::isValid($file))
            return null;

        // Open a file writer to write to the file
        $writer = new FileWriter($file);
        if(!$writer->open(FileAccessModeFactory::createTruncateMode(false), $context))
            return null;

        // Put the contents into the file, return the result
        return $writer->write($data);
    }

    /**
     * Append to a file.
     *
     * @param File|string $file File instance or a file path as string to append to.
     * @param string $data The data to append to the file.
     * @param resource $context [optional] See PHPs fopen() function for more details.
     * @param bool $create [optional] True to attempt to create the file if it doens't exist.
     *
     * @return int|null The amount of bytes appended to the file.
     */
    public static function append($file, $data, $context = null, $create = true) {
        // Convert the file into a path string, return the $default value if failed
        if(($file = self::asPath($file, false)) === null)
            return null;

        // Make sure the file is valid. If the file shouldn't be created make sure it exists already.
        if($create) {
            if(!self::isValid($file))
                return null;
        } else
            if(!self::isFile($file))
                return null;

        // Open the file with the file writer
        $writer = new FileWriter($file, FileAccessModeFactory::createAppendMode(false));
        if(!$writer->open(null, $context))
            return null;

        // Append to the file, return the result
        return $writer->write($data);
    }

    /*
    /**
     * Prepend to a file.
     *
     * @param File|string $file File instance or a file path as string to prepend to.
     * @param string $data The data to prepend to the file.
     * @param resource $context [optional] See PHPs fopen() function for more details.
     * @param bool $create [optional] True to attempt to create the file if it doens't exist.
     *
     * @return int|null The amount of bytes prepended to the file.
     * /
    // TODO: Method is overwriting current content at the beginning of the file, fix this!
    public static function prepend($file, $data, $context = null, $create = true) {
        // Convert the file into a path string, return the $default value if failed
        if(($file = self::asPath($file)) === null)
            return null;

        // Make sure the filesystem object isn't an existing directory or symbolic link. If the file shouldn't be
        // created if it doesn't exist, make sure the object is a file and exists
        if($create) {
            if(self::isDirectory($file) || self::isSymbolicLink($file))
                return null;
        } else
            if(!self::isFile($file))
                return null;

        // Open the file with the file writer
        $writer = new FileWriter($file, FileAccessModeFactory::createPrependMode(false));
        if(!$writer->open(null, $context))
            return null;

        // Prepend to the file, return the result
        return $writer->write($data);
    }*/

    /**
     * Get the correct new line or end of line character that should be used for the current platform.
     *
     * @return string Correct new line character
     */
    // TODO: Should we keep this method?
    // TODO: Should we move this method?
    public static function getNewLineChar() {
        return PHP_EOL;
    }

    /**
     * Validate a file instance or the path of a file. The file doesn't need to exist.
     * The file may not be an existing directory or symbolic link.
     *
     * @param \carbon\core\io\filesystem\FilesystemObject|string $path Filesystem object instance or the path of a file as a string.
     *
     * @return bool True if the file path seems to be valid, false otherwise.
     */
    public static function isValid($path) {
        // Convert the file into a string, return the false if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Make sure the file is valid as FileSystemObject
        if(!FilesystemObjectHelper::isValid($path))
            return false;

        // Make sure the file isn't a directory or symbolic link, return the result
        return !(FilesystemObjectHelper::isDirectory($path) || FilesystemObjectHelper::isSymbolicLink($path));
    }
}
