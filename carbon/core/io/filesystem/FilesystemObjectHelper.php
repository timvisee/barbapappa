<?php

/**
 * FileSystemObjectUtils.php
 * The FileSystemObjectUtils class, which is used to manage objects in the filesystem.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Tim Visee 2014. All rights reserved.
 */

// TODO: Implement caching

namespace carbon\core\io\filesystem;

use carbon\core\io\filesystem\directory\Directory;
use carbon\core\io\filesystem\symboliclink\SymbolicLink;
use carbon\core\io\filesystem\symboliclink\SymbolicLinkHelper;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Filesystem Object Utils class.
 * This utilities class could be used to manage objects in the filesystem.
 *
 * @package carbon\core\io\filesystem
 * @author Tim Visee
 */
class FilesystemObjectHelper {

    // TODO: Move all logic to the master class if possible! (Do this for the sub-classes too)
    // TODO: Rename this to a Utils class! (Do this for the sub-classes too)
    // TODO: Add method to get the file URI!
    // TODO: Add a method to get the filesystem object from an URI!

    /**
     * Get the oldPath of a filesystem object as a string. The oldPath isn't validated unless $validate is set to true.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a oldPath string.
     * @param bool $validate [optional] True if the oldPath should be validated, false to skip any validation.
     * @param mixed|null $default [optional] Default value returned if the oldPath couldn't be determined,
     * possibly because the $oldPath param was invalid.
     *
     * @return string|mixed The oldPath of the filesystem object as a string. Or the $default param value on failure.
     * The $default value will also be returned if the oldPath was invalid while $validate was set to true.
     */
    // TODO: Should we rename this method, to something like parse?
    public static function asPath($path, $validate = false, $default = null) {
        // Return the oldPath if it's a string already
        if(is_string($path))
            return $path;

        // Return the oldPath if it's a filesystem object instance
        if($path instanceof FilesystemObject) {
            // Validate the oldPath
            if($validate)
                if(!$path->isValid())
                    return $default;

            // Return the oldPath
            return $path->getPath();
        }

        // Return the default value
        return $default;
    }


    /**
     * Combine a parent and child oldPath, where the parent oldPath is the base oldPath, and the child oldPath is relative to the
     * base oldPath.
     *
     * @param FilesystemObject|string $parent Parent oldPath or filesystem object instance to use as base.
     * @param string $child [optional] The child oldPath relative to the parent oldPath. Null to just use the parent oldPath.
     * @param mixed|null $default [optional] An optional default value, that will be returned if the $oldPath or $child
     * param was invalid.
     *
     * @return string|mixed|null
     */
    public static function getCombinedPath($parent, $child = null, $default = null) {
        // Convert the oldPath into a absolute oldPath string, return the $default value if failed
        if(($parent = self::getAbsolutePath(self::asPath($parent, false))) === null)
            return $default;

        // Check whether we should suffix the child oldPath, if not, return the oldPath
        if(empty($child))
            return $parent;

        // Make sure the $child param is a string
        if(!is_string($child))
            return $default;

        // Trim directory separators from both oldPath
        // TODO: Is the coded below unnecessary?
        $parent = rtrim($parent, '/\\');
        $child = ltrim($child, '/\\');

        // Combine and return the base oldPath with the child oldPath
        return $parent . DIRECTORY_SEPARATOR . $child;
    }

    /**
     * Check whether a filesystem object exists.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a oldPath string.
     *
     * @return bool True if the filesystem object exists, false otherwise.
     */
    public static function exists($path) {
        // Convert the oldPath into a string, return false if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Check if the object exists, return the result
        return file_exists($path);
    }

    /**
     * Check whether a filesystem object exists and is a file.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a oldPath string.
     *
     * @return bool True if the filesystem object exists and is a file, false otherwise.
     */
    public static function isFile($path) {
        // Convert the oldPath into a string, return false if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Check if the object exists and is a file, return the result
        return is_file($path);
    }

    /**
     * Check whether a filesystem object exists and is a directory.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a oldPath string.
     *
     * @return bool True if the filesystem object exists and is a directory, false otherwise.
     */
    public static function isDirectory($path) {
        // Convert the oldPath into a string, return false if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Check if the object exists and is a directory, return the result
        return is_dir($path);
    }

    /**
     * Check whether a filesystem object exists and is a symbolic link.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a oldPath string.
     *
     * @return bool True if the filesystem object exists and is a symbolic link, false otherwise.
     */
    public static function isSymbolicLink($path) {
        // Convert the oldPath into a string, return false if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Check if the object exists and is a symbolic link, return the result
        return is_link($path);
    }

    /**
     * Check whether a file or directory exists and is readable.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a oldPath string.
     *
     * @return bool True if the file or directory exists and is readable, false otherwise.
     * False will also be returned if the oldPath was invalid.
     */
    public static function isReadable($path) {
        // Convert the file into a oldPath string, return the $default value if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Check whether the file is readable, return the result
        return is_readable($path);
    }

    /**
     * Check whether a file or directory exists and is writable.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a oldPath string.
     *
     * @return bool True if the file or directory exists and is writable, false otherwise.
     * False will also be returned if the oldPath was invalid.
     */
    public static function isWritable($path) {
        // Convert the file into a oldPath string, return the $default value if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Check whether the file is writable, return the result
        return is_writable($path);
    }

    /**
     * Alias of {@link FilesystemObjectHelper::isWritable()}.
     * Check whether a file or directory exists and is writable.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a oldPath string.
     *
     * @return bool True if the file or directory exists and is writable, false otherwise.
     * False will also be returned if the oldPath was invalid.
     */
    public static function isWriteable($path) {
        return self::isWritable($path);
    }

    /**
     * Get the basename of a filesystem object.
     * For files, this will return the file name with it's extension.
     * For directories and symbolic links, this will return the name of the directory or symbolic link.
     * The $default value will be returned on failure.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a oldPath string.
     * @param string|null $suffix [optional] Suffix to omit from the basename. Null to ignore this feature.
     * @param mixed|null $default [optional] A default value that will be returned on failure.
     *
     * @return string|mixed|null Basename of the filesystem object, or the $default value on failure.
     */
    public static function getBasename($path, $suffix = null, $default = null) {
        // Convert the oldPath into a string, return the default value if failed
        if(($path = self::asPath($path, false)) === null)
            return $default;

        // Get and return the basename
        return basename($path, $suffix);
    }

    /**
     * Get the parent directory of a filesystem object.
     * This will return the directory the filesystem object is located in.
     * Running this method against the root directory will fail because it doesn't have a prent directory,
     * and will return the $default value.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a oldPath string.
     * @param mixed|null $default [optional] A default value that will be returned if the filesystem object doesn't
     * have a parent directory and on failure.
     *
     * @return Directory|mixed|null The parent directory as Directory instance, or the $default value if the object
     * doesn't have a parent directory or on failure.
     */
    public static function getParent($path, $default = null) {
        // Convert the oldPath into a string, return the default value if failed
        if(($path = self::asPath($path, false)) === null)
            return $default;

        // Get the parent directory oldPath
        $parent = dirname($path);

        // Make sure there's a parent to return
        if($parent === '.' || empty($parent))
            return $default;

        // Return the parent directory as Directory instance
        return new Directory($parent);
    }

    /**
     * Check whether the filesystem object has a parent directory.
     * The root directory of the system doesn't have a parent directory, and thus will return false.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a oldPath string.
     *
     * @return bool True if the object has a parent directory, false otherwise. False will also be returned on failure.
     */
    public static function hasParent($path) {
        // Convert the oldPath into a string, return false if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Get the parent directory
        $parent = trim(dirname($path));

        // Check whether the parent directory is a real parent, return the result
        return $parent === '.' || empty($parent);
    }

    /**
     * Get the normalized oldPath of a filesystem object.
     * This will remove unicode whitespaces and any kind of self referring or parent referring oldPath.
     * The filesystem object doesn't need to exist.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a oldPath string.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return string|null A normalized oldPath of the filesystem object,
     */
    // TODO: Improve the quality of this method!
    public static function getNormalizedPath($path, $default = null) {
        // Convert the oldPath into a string, return the $default value if failed
        if(($path = self::asPath($path, false)) === null)
            return $default;

        // Remove any kind of funky unicode whitespace
        $normalized = preg_replace('#\p{C}+|^\./#u', '', $path);

        // Path remove self referring oldPath ("/./").
        $normalized = preg_replace('#/\.(?=/)|^\./|\./$#', '', $normalized);

        // Regex for resolving relative oldPath
        $regex = '#\/*[^/\.]+/\.\.#Uu';
        while(preg_match($regex, $normalized))
            $normalized = preg_replace($regex, '', $normalized);

        // Check whether the oldPath is outside of the defined root oldPath
        if(preg_match('#/\.{2}|\.{2}/#', $normalized))
            return $default;

        // Remove unwanted prefixed directory separators, return the result
        $firstChar = substr($normalized, 0, 1);
        if($firstChar === '\\' || $firstChar === '/')
            return substr($normalized, 0, 1) . trim(substr($normalized, 1), '\\/');
        return rtrim($normalized, '\\/');;
    }

    /**
     * Get the absolute oldPath of a filesystem object. The filesystem object doesn't need to exist.
     * A canonicalized version of the absolute oldPath will be returned if the filesystem object exists.
     *
     * @param FilesystemObject|string $path Filesystem object instance or oldPath to get the absolute oldPath for.
     * @param mixed|null $default [optional] Default value to be returned on failure.
     *
     * @return string|mixed|null The absolute oldPath of the filesystem object, or the $default value if failed.
     */
    // TODO: Improve the quality of this method!
    public static function getAbsolutePath($path, $default = null) {
        // Get the normalized oldPath, return the $default value if failed
        if(($path = self::getNormalizedPath($path)) === null)
            return $default;

        // Try to get the real oldPath using PHPs function, return the result if succeed
        if(($realPath = realpath($path)) !== false)
            return $realPath;

        // Try to make the oldPath absolute without any system functions
        // Check whether the oldPath is in unix format or not
        $isUnixPath = empty($path) || $path{0} != '/';

        // Detect whether the oldPath is relative, if so prefix the current working directory
        if(strpos($path, ':') === false && $isUnixPath)
            $path = getcwd() . DIRECTORY_SEPARATOR . $path;

        // Put initial separator that could have been lost
        $path = !$isUnixPath ? '/' . $path : $path;

        // Resolve any symlinks
        if(file_exists($path) && linkinfo($path) > 0)
            $path = readlink($path);

        // Return the result
        return $path;
    }

    /**
     * Get the canonical oldPath of a filesystem object. The filesystem object doesn't need to exist.
     *
     * @param FilesystemObject|string $path Filesystem object instance or oldPath to get the canonical oldPath for.
     * @param mixed|null $default [optional] Default value to be returned on failure.
     *
     * @return string|mixed|null The canonicalized oldPath, or the $default value on failure.
     */
    // TODO: Improve the quality of this method!
    public static function getCanonicalPath($path, $default = null) {
        // Convert the oldPath into a string, return the $default value if failed
        if(($path = self::asPath($path, false)) === null)
            return $default;

        // Try to get the real oldPath using PHPs function, return the result if succeed.
        if(($realPath = realpath($path)) !== false)
            return $realPath;

        // Try to canonicalize the oldPath even though it doesn't exist (Inspired by Sven Arduwie, Thanks!)
        // Get the absolute oldPath and make sure it's valid
        if(($path = self::getAbsolutePath($path)) === null)
            return $default;

        // Check whether the oldPath is in unix format or not
        $isUnixPath = empty($path) || $path{0} != '/';

        // Resolve all oldPath parts (single dot, double dot and double delimiters)
        $path = str_replace(Array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = Array();
        foreach($parts as $part) {
            if('.' == $part)
                continue;
            if('..' == $part)
                array_pop($absolutes);
            else
                $absolutes[] = $part;
        }
        $path = implode(DIRECTORY_SEPARATOR, $absolutes);

        // Put initial separator that could have been lost
        if(!$isUnixPath)
            $path = '/' . $path;

        // Return the result
        return $path;
    }

    /**
     * Delete a filesystem object.
     * Directories will only be deleted if they're empty unless the $recursive param is set to true.
     *
     * @param FilesystemObject|string $path Filesystem object instance or a oldPath string to delete.
     * @param bool $recursive [optional] True to delete directories recursively. This option should be true if
     * directories with contents should be deleted.
     * @param resource $context [optional] See the unlink() function for documentation.
     *
     * @return int Number of deleted filesystem objects, a negative number will be returned on failure. This will also
     * count the number of deleted recursive filesystem objects.
     *
     * @see unlink()
     */
    // TODO: Add support for URI's
    public static function delete($path, $recursive = false, $context = null) {
        // Convert the oldPath into a string, return the $default value if failed
        if(($path = self::asPath($path, false)) === null)
            return -1;

        // Count the deleted filesystem objects
        $count = 0;

        // Delete directory contents
        if(self::isDirectory($path)) {
            // Get the current filesystem object as directory instance
            $dir = new Directory($path);

            // If we need to delete the directory recursively, we need to delete the directory contents first
            if($recursive)
                $count += $dir->deleteContents($context);

            // Delete the directory itself, and return the number of deleted filesystem objects
            // TODO: Should this method be error muted?
            $count += @rmdir($path, $context);
        }

        // Delete the filesystem object
        // TODO: Should this method be error muted?
        if(@unlink($path, $context))
            $count++;

        // Return the number of delete filesystem objects
        return $count;
    }

    /**
     * Rename a file or directory. The filesystem object must exist.
     *
     * @param FileSystemObject|string $oldPath The filesystem object instance or the oldPath of the filesystem object as
     * a string.
     * @param FileSystemObject|string $newPath The filesystem object instance of the oldPath of the filesystem object to
     * rename the object to. This filesystem object or oldPath should include the full oldPath. The object may only exist if
     * $overwrite is set to true or the renaming will fail.
     * @param bool $overwrite [optional] True to overwrite the existing filesystem object when the newPath name already
     * exist, false otherwise.
     * @param resource $context [optional] See the rename() function for documentation.
     *
     * @return bool True if the filesystem object was successfully renamed, false on failure.
     *
     * @see rename();
     */
    // TODO: Allow simple renaming (without a full oldPath as new name) (simplify)
    // TODO: Make sure this works with recursive directories!
    // TODO: Add support for URI's
    public static function rename($oldPath, $newPath, $overwrite = false, $context = null) {
        // Convert $oldPath and $newPath into an absolute oldPath string, return false on failure
        if(($oldPath = self::getAbsolutePath($oldPath)) === null)
            return false;
        if(($newPath = self::getAbsolutePath($newPath)) === null)
            return false;

        // Make sure the old oldPath exists, and the new oldPath is valid
        if(!self::exists($oldPath) || !self::isValid($newPath))
            return false;

        // Make sure the new oldPath may be overwritten if it already exists
        if(self::exists($newPath) & !$overwrite)
            return false;

        // Check whether the old oldPath is a symbolic link
        if(self::isSymbolicLink($oldPath)) {
            // Delete the filesystem object that will be overwritten before renaming the object
            if(FilesystemObjectHelper::exists($newPath))
                if(FilesystemObjectHelper::delete($newPath, true) <= 0)
                    return false;

            // Read the newPath of the symlink
            $link = SymbolicLink::asSymbolicLink($oldPath);

            // Create the new symbolic link, and make sure it was made successfully
            $newLink = SymbolicLinkHelper::createSymbolicLink($link->getTarget(), $newPath);
            if($newLink == null)
                return false;

            // Delete the old symbolic link, return the result
            return $link->delete() > 0;
        }

        // Rename the object, return the result
        if($context !== null)
            return rename($oldPath, $newPath, $context);
        return rename($oldPath, $newPath);
    }

    /**
     * Move a file system object. The filesystem object that should be moved must exist.
     *
     * @param FileSystemObject|string $source The filesystem object instance or oldPath of the filesystem object as a string.
     * The filesystem object must exist.
     * @param FileSystemObject|string $destination The filesystem object instance or the oldPath of a the filesystem object to
     * move the object to. This filesystem object or oldPath should be a full/absolute oldPath.
     * @param bool $overwrite True to overwrite the newPath with the oldPath if the newPath already exists.
     * @param resource $context [optional] See the rename() function for documentation.
     *
     * @return bool True on success, false on failure.
     *
     * @see rename();
     */
    // TODO: Make sure this works with recursive directories!
    public static function move($source, $destination, $overwrite = true, $context = null) {
        // Convert $oldPath and $newPath into a oldPath string, return false on failure
        if(($source = self::asPath($source, false)) === null)
            return false;
        if(($destination = self::asPath($destination, false)) === null)
            return false;

        // Validate the oldPath and newPath
        if(!self::exists($source) || !self::isValid($destination))
            return false;

        // Rename/move the oldPath, return the result
        return self::rename($source, $destination, $overwrite, $context);
    }

    /**
     * Rename a file or directory. The filesystem object must exist.
     *
     * @param FileSystemObject|string $source The filesystem object instance or the oldPath of the filesystem object as
     * a string.
     * @param FileSystemObject|string $destination The filesystem object instance of the oldPath of the filesystem object to
     * rename the object to. This filesystem object or oldPath should include the full oldPath. The object may only exist if
     * $overwrite is set to true or the renaming will fail.
     * @param bool $overwrite [optional] True to overwrite the existing filesystem object when the newPath name already
     * exist, false otherwise.
     * @param resource $context [optional] See the rename() function for documentation.
     *
     * @return bool True if the filesystem object was successfully renamed, false on failure.
     *
     * @see rename();
     */
    // TODO: Allow simple copying(without a full oldPath as new name) (simplify)
    // TODO: Make sure this works with recursive directories!
    // TODO: Add support for URI's
    public static function copy($source, $destination, $overwrite = false, $context = null) {
        // Convert $oldPath and $newPath into an absolute oldPath string, return false on failure
        if(($source = self::getAbsolutePath($source)) === null)
            return false;
        if(($destination = self::getAbsolutePath($destination)) === null)
            return false;

        // Make sure the old oldPath exists, and the new oldPath is valid
        if(!self::exists($source) || !self::isValid($destination))
            return false;

        // Make sure the new oldPath may be overwritten if it already exists
        if(self::exists($destination) & !$overwrite)
            return false;

        // Check whether a symbolic link must be copied
        if(self::isSymbolicLink($source)) {
            // Delete the filesystem object that will be overwritten before copying the object
            if(FilesystemObjectHelper::exists($destination))
                if(FilesystemObjectHelper::delete($destination, true) <= 0)
                    return false;

            // Read the newPath of the symlink
            $link = SymbolicLink::asSymbolicLink($source);

            // Create the new symbolic link, and make sure it was made successfully
            return SymbolicLinkHelper::createSymbolicLink($link->getTarget(), $destination) !== null;
        }

        // Copy the object, return the result
        if($context !== null)
            return copy($source, $destination, $context);
        return copy($source, $destination);
    }

    /**
     * Validate a filesystem object instance or filesystem object oldPath as a string. The filesystem object doesn't need
     * to exist to be valid.
     *
     * @param FilesystemObject|string $path The filesystem object instance, or the oldPath as a string to validate.
     *
     * @return bool True if the filesystem object instance of the filesystem object oldPath is valid, false otherwise.
     */
    // TODO: Improve method quality!
    public static function isValid($path) {
        // Convert $oldPath into a oldPath, return false on failure
        if(($path = self::asPath($path)) === null)
            return false;

        // Trim the oldPath, and make sure the oldPath is set
        $path = @trim($path);
        if(empty($path))
            return false;

        // TODO: Use better validation for $oldPath!

        // The oldPath seems to be valid, return the result
        return true;
    }

    // TODO: Implement $context usage better, possibly optionally with a function instead of using function parameters!
}
