<?php

/**
 * FileSystemObject.php
 * The FileSystemObject class, which is used to manage objects in the filesystem.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Tim Visee 2014. All rights reserved.
 */

namespace carbon\core\io\filesystem;

use carbon\core\cache\simplecache\SimpleCache;
use carbon\core\io\filesystem\directory\Directory;
use carbon\core\io\filesystem\file\File;
use carbon\core\io\filesystem\symboliclink\SymbolicLink;
use carbon\core\util\StringUtils;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Filesystem Object class.
 * This class references to an object in the filesystem based on it's file path.
 * This class could be used to manage objects in the filesystem.
 *
 * @package carbon\core\io\filesystem
 * @author Tim Visee
 */
class FilesystemObject {

    // TODO: ::join($a, $b) method to join two paths!
    // TODO: Add support for disks, external servers and so on, see: http://laravel.com/docs/5.0/filesystem

    /** @var string Defines the path. */
    protected $path = '';
    /** @var SimpleCache Instance used for basic caching. */
    protected $cache;

    // TODO: Should we keep these cache methods in this updated class?
    /** Defines the cache key used for the normalized path cache */
    const CACHE_NORMALIZED_PATH = 1;
    /** Defines the cache key used for the absolute path cache */
    const CACHE_ABSOLUTE_PATH = 2;
    /** Defines the cache key used for the canonical path cache */
    const CACHE_CANONICAL_PATH = 3;

    /**
     * FileSystemObject constructor.<br>
     * The path of a filesystem object must be entered as argument when constructing the the Filesystem Object.
     * The specified object doesn't need to exist. An optional child path relative to the $path param may be supplied.
     *
     * @param string|FilesystemObject $path The filesystem object as path or as filesystem object.
     * @param string|null $child [optional] An optional child path relative to the main path parameter.
     * Null to just use the $path param as path.
     *
     * @throws \Exception Throws an exception when the $path or $child param is invalid.
     */
    public function __construct($path = '', $child = null) {
        // Initialize the simple cache
        $this->cache = new SimpleCache();

        // Set the path, throw an exception on error
        if(!$this->setPath($path, $child, false))
            // TODO: Invalid path, throw a custom exception!
            throw new \Exception();
    }

    /**
     * Get the path of the filesystem object as a string. This will return the path in the same format without any
     * processing since the path was set.
     *
     * @return string The filesystem object path as a string.
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set the path of the filesystem object. An optional child path relative to the $path param may be supplied.
     *
     * @param FilesystemObject|string $path Path to the filesystem object or the instance of another filesystem object
     * to use it's path.
     * @param string|null $child [optional] An optional child path relative to the $path param.
     * Null to just use the $path param as path.
     * @param bool $flushCache [optional] True to flush the cache, false otherwise.
     *
     * @return bool True on success, false on failure.
     */
    protected function setPath($path, $child = null, $flushCache = true) {
        // Combine the two paths and make sure it's valid
        // TODO: Should we get the path though this function, or does this normalize symbolic links?
        // TODO: Throw an exception on failure
        if(($path = FilesystemObjectHelper::getCombinedPath($path, $child)) === null)
            return false;

        // Set the path
        $this->path = $path;

        // Flush the cache, and return true
        if($flushCache)
            $this->flushCache();
        return true;
    }

    /**
     * Check whether the filesystem object exists.
     *
     * @return bool True if the filesystem object exists, false otherwise.
     */
    public function exists() {
        return FilesystemObjectHelper::exists($this);
    }

    /**
     * Check whether the filesystem object exists and is a file.
     *
     * @return bool True if the filesystem object exists and is a file, false otherwise.
     */
    public function isFile() {
        return FilesystemObjectHelper::isFile($this);
    }

    /**
     * Check whether the filesystem object exists and is a directory.
     *
     * @return bool True if the filesystem object exists and is a directory, false otherwise.
     */
    public function isDirectory() {
        return FilesystemObjectHelper::isDirectory($this);
    }

    /**
     * Check whether the filesystem object exists and is a symbolic link.
     *
     * @return bool True if the filesystem object exists and is a symbolic link, false otherwise.
     */
    public function isSymbolicLink() {
        return FilesystemObjectHelper::isSymbolicLink($this);
    }

    /**
     * Check whether the file or directory exists and is readable.
     *
     * @return bool True if the file or directory exists and is readable, false otherwise.
     * False will also be returned if the path was invalid.
     */
    public function isReadable() {
        return FilesystemObjectHelper::isReadable($this);
    }

    /**
     * Check whether the file or directory exists and is writable.
     *
     * @return bool True if the file or directory exists and is writable, false otherwise.
     * False will also be returned if the path was invalid.
     */
    public function isWritable() {
        return FilesystemObjectHelper::isWritable($this);
    }

    /**
     * Alias of {@link FileSystemObject::isWritable()}.
     * Check whether the file or directory exists and is writable.
     *
     * @return bool True if the file or directory exists and is writable, false otherwise.
     * False will also be returned if the path was invalid.
     */
    public function isWriteable() {
        return FilesystemObjectHelper::isWriteable($this);
    }

    /**
     * Get the basename of the filesystem object.
     * For files, this will return the file name with it's extension.
     * For directories and symbolic links, this will return the name of the directory or symbolic link.
     *
     * @param string|null $suffix [optional] Suffix to omit from the basename. Null to ignore this feature.
     *
     * @return string|null Basename of the filesystem object or null on failure.
     */
    public function getBasename($suffix = null) {
        return FilesystemObjectHelper::getBasename($this, $suffix);
    }

    /**
     * Get the parent directory of the filesystem object.
     * This will return the directory the filesystem object is located in.
     * Calling this method on the root directory will fail because the root doesn't have a parent directory,
     * and will return the $default value.
     *
     * @return Directory|null The parent directory as Directory instance, or null if there's no parent directory and on
     * failure.
     */
    public function getParent() {
        return FilesystemObjectHelper::getParent($this);
    }

    /**
     * Check whether the filesystem object has a parent directory.
     *
     * @return bool True if the filesystem object has a parent directory, false otherwise.
     */
    public function hasParent() {
        return FilesystemObjectHelper::hasParent($this);
    }

    /**
     * Get the normalized path of the filesystem object.
     * This will remove unicode whitespaces and any kind of self referring or parent referring paths.
     * The filesystem object doesn't need to exist.
     *
     * @return string|null A normalized path of the filesystem object, or null on failure.
     */
    public function getNormalizedPath() {
        // Return the normalized path if it's cached
        if($this->cache->has(self::CACHE_NORMALIZED_PATH))
            return $this->cache->get(self::CACHE_NORMALIZED_PATH, $this->path);

        // Get the normalized path
        $path = FilesystemObjectHelper::getNormalizedPath($this);

        // Cache and return the normalized path
        $this->cache->set(self::CACHE_NORMALIZED_PATH, $path);
        return $path;
    }

    /**
     * Get the absolute path of the filesystem object.
     * A canonicalized version of the absolute path will be returned if the filesystem object exists.
     *
     * @return string|null Absolute path of the filesystem object or null on failure.
     */
    public function getAbsolutePath() {
        // Return the absolute path if it's cached
        if($this->cache->has(self::CACHE_ABSOLUTE_PATH))
            return $this->cache->get(self::CACHE_ABSOLUTE_PATH, $this->path);

        // Get the absolute path
        $path = FilesystemObjectHelper::getAbsolutePath($this);

        // Cache and return the absolute path
        $this->cache->set(self::CACHE_ABSOLUTE_PATH, $path);
        return $path;
    }

    /**
     * Get the canonicalized path of the filesystem object. The canonicalized path will be absolute.
     * A path which is invalid or doesn't exist will be canonicalized as far as that's possible.
     *
     * @return string|null Canonicalized path of the filesystem object, or null if failed to canonicalize the path.
     */
    // TODO: Improve this method!
    public function getCanonicalPath() {
        // Return the canonical path if it's cached
        if($this->cache->has(self::CACHE_CANONICAL_PATH))
            return $this->cache->get(self::CACHE_CANONICAL_PATH, null);

        // Get the canonicalized path
        $path = FilesystemObjectHelper::getCanonicalPath($this);

        // Cache and return the canonical path
        $this->cache->set(self::CACHE_CANONICAL_PATH, $path);
        return $path;
    }

    /**
     * Canonicalize the path.
     */
    public function canonicalize() {
        $this->setPath($this->getCanonicalPath());
    }

    /**
     * Delete the filesystem object if it exists.
     * Directories will only be deleted if they're empty or if the $recursive param is set to true.
     *
     * @param resource $context [optional] See the unlink() function for documentation.
     * @param bool $recursive [optional] True to delete directories recursively.
     * This option should be true if directories with contents should be deleted.
     *
     * @return int Number of deleted filesystem objects, a negative number will be returned if the $path param was
     * invalid.
     *
     * @see unlink()
     */
    public function delete($context = null, $recursive = false) {
        return FilesystemObjectHelper::delete($this, $recursive, $context);
    }

    /**
     * Rename a file or directory. The filesystem object must exist.
     *
     * @param FileSystemObject|string $newPath The filesystem object instance of the path of the filesystem object to
     * rename the object to. This filesystem object or path should include the full path. The object may only exist if
     * $overwrite is set to true or the renaming will fail.
     * @param bool $overwrite [optional] True to overwrite the existing filesystem object when the target name already
     * exist, false otherwise.
     * @param resource $context [optional] See the rename() function for documentation.
     *
     * @return bool True if the filesystem object was successfully renamed, false on failure.
     *
     * @see rename();
     */
    public function rename($newPath, $overwrite = false, $context = null) {
        return FilesystemObjectHelper::rename($this, $newPath, $overwrite, $context);
    }

    /**
     * Move a file system object. The filesystem object that should be moved must exist.
     *
     * @param FileSystemObject|string $target The filesystem object instance or the path of a the filesystem object to
     * move the object to. This filesystem object or path should be a full/absolute path. If the target is an existing
     * directory, the path won't be moved to the target, instead the path is moved inside the target directory. This is
     * still the case if $overwrite is set to true.
     * @param bool $overwrite True to overwrite the target with the path if the target already exists. Please note that
     * the path is moved into the target directory if the target is an existing directory, thus the target directory
     * won't be overwritten. If the path being moved does exist inside the target directory, the object will be
     * overwritten if $overwrite is set to true.
     * @param resource $context [optional] See the rename() function for documentation.
     *
     * @return bool True on success, false on failure.
     *
     * @see rename();
     */
    public function move($target, $overwrite = false, $context = null) {
        return FilesystemObjectHelper::move($target, $overwrite, $context);
    }

    /**
     * Flush the cache
     */
    public function flushCache() {
        $this->cache->flush();
    }

    /**
     * Get a File, Directory, SymbolicLink or FileSystemObject instance.
     * A File instance will be returned if the filesystem object is a file.
     * A Directory instance will be returned if the filesystem object is a directory.
     * A SymbolicLink instance will be returned if the filesystem object is a symbolic link.
     * A FileSystemObject instance will be returned if it couldn't be determined whether the filesystem object is a
     * file, directory or symbolic link. This is usually the case when the filesystem object doesn't exist.
     * The supplied filesystem object doesn't need to exist.
     * An optional child path relative to the $path param may be supplied.
     *
     * @param FilesystemObject|string $path Path to the filesystem object or the instance of another filesystem object
     * to use it's path.
     * @param string|null $child [optional] An optional child path relative to the $path param.
     * Null to just use the $path param as path.
     *
     * @return File|Directory|SymbolicLink|FilesystemObject|null File, Directory, SymbolicLink or FileSystemObject
     * instance, or null if the $path was invalid.
     */
    // TODO: Should we rename this method to instance(), instantiate() or parse(). At least use better naming than from().
    // TODO: Should we keep this method available, even though the constructor is available with similar functionality
    public static function from($path, $child = null) {
        // Create a filesystem object instance and make sure it's valid
        if(($path = FilesystemObjectHelper::getCombinedPath($path, $child)) === null)
            return null;

        // Return a File instance if the filesystem object is a file
        if(FilesystemObjectHelper::isFile($path))
            return new File($path);

        // Return a Directory instance if the filesystem object is a directory
        if(FilesystemObjectHelper::isDirectory($path))
            return new Directory($path);

        // Return a SymbolicLink instance if the filesystem object is a symbolic link
        if(FilesystemObjectHelper::isSymbolicLink($path))
            return new SymbolicLink($path);

        // Return as filesystem object instance
        return new FilesystemObject($path);
    }

    /**
     * Check whether the filesystem object path is valid. The filesystem object doesn't need to exist.
     *
     * @return bool True if the path of the filesystem object seems to be valid, false otherwise.
     */
    // TODO: Create static function of this, and check the path on construction.
    public function isValid() {
        return FilesystemObjectHelper::isValid($this);
    }

    /**
     * Compare this filesystem object with an other filesystem object.
     *
     * @param FilesystemObject|string $other The other filesystem object instance.
     * The path of a filesystem object may be supplied if $sameType is set to false to just compare the paths.
     * @param bool $sameType [optional] True to make sure both instances are from the same type,
     * false to just compare the paths.
     *
     * @return bool True if this filesystem object is equal with $other, false otherwise.
     * False will also be returned on failure.
     */
    // TODO: Improve the quality of this method!
    public function equals($other, $sameType = true) {
        // Make sure the types are equal
        if($sameType && !(get_class() === get_class($other)))
            return false;

        // Convert $other into a string, return false if failed
        if(($other = FilesystemObjectHelper::asPath($other, false)) === null)
            return false;

        // Compare the paths, return the result
        return StringUtils::equals($this->getPath(), $other, false, true);
    }

    /**
     * Convert the path to a string. The output of {@link getPath()} will be returned.
     *
     * @return string Path as a string.
     */
    public function __toString() {
        return $this->path;
    }

    /**
     * Clone this instance.
     *
     * @return FilesystemObject Cloned instance
     */
    public function __clone() {
        // Get the class type
        $class = get_class($this);

        // Clone and return the instance
        return new $class($this->path);
    }

    // TODO: Take a look at the getCanonicalPath, getAbsolutePath and canonicalize methods!
    // TODO: Method to convert anything possible into a path
}
