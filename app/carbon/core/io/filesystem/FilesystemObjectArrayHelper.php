<?php

/**
 * FileSystemObjectArrayUtils.php
 * The FileSystemObjectArrayUtils class, which is used to manage arrays of objects in the filesystem.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (c) Tim Visee 2014. All rights reserved.
 */

// TODO: Implement caching
// TODO: Should we rename this to FilesystemObjectSetUtils or something similar?

namespace carbon\core\io\filesystem;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Filesystem Object Array Utils class.
 * This utilities class could be used to manage arrays of objects in the filesystem.
 *
 * @package carbon\core\io\filesystem
 * @author Tim Visee
 */
class FilesystemObjectArrayHelper {

    /**
     * Get the sources of a list of filesystem object as an array of strings.
     *
     * @param Array|FilesystemObject|string $paths An array with filesystem object instances or path strings.
     * Or a single filesystem object instance or path string. The array may contain multiple other arrays.
     * @param bool $ignoreInvalid True to ignore invalid items in the array, this will prevent the default value from
     * being returned.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return Array|mixed|null An array of path strings, or the $default value on failure.
     */
    public static function asPathArray($paths, $ignoreInvalid = false, $default = null) {
        // Create an array to push all the parents in
        $out = Array();

        // Make sure $parents isn't null
        if ($paths === null)
            return null;

        // Create an array of the $parents param
        if (!is_array($paths))
            $paths = Array($paths);

        // Process each filesystem object
        $pathCount = sizeof($paths);
        for ($i = 0; $i < $pathCount; $i++) {
            $path = $paths[$i];

            // Check whether $path is an array
            if (is_array($path)) {
                // Get all parents from the array and make sure the result is valid
                if (($arrayPaths = self::asPathArray($path, $ignoreInvalid)) === null)
                    return $default;

                // Push the parents into the array
                $out = array_merge($out, $arrayPaths);
                continue;
            }

            // Get the path as a string
            $path = FilesystemObjectHelper::asPath($path, false);

            // Check whether the path is valid
            if (is_string($path)) {
                array_push($out, $path);
                continue;
            }

            // Check whether we should return the default value because the conversion of this path failed
            if (!$ignoreInvalid)
                return $default;
        }

        // Return the array of parents
        return $out;
    }

    /**
     * Combine parent and child sources into a sources.
     *
     * If only one parent path is set a path will be created for each child with the same parent path.
     * If only one child path is set a path will be created for each parent with the same child path.
     * A single path will be returned if only a single path is supplied as parent and child.
     * If no child sources is set, the list of parents will be returned interpreted as regular sources.
     *
     * @param FileSystemObject|string|array $parents An array with filesystem object instances or path strings to use as
     * parents. Or a single filesystem object instance or path string. The array may contain multiple other arrays.
     * @param FileSystemObject|string|array|null $children [optional] An array with filesystem object instances or path strings to
     * use as child's. Or a single filesystem object instance or path string. The array may contain multiple other arrays.
     * If this parameter equals null the list of parents will be returned as array.
     * @param bool $includeInvalid [optional] True to include null items in the array being returned for parent and child
     * combinations that couldn't be combined.
     * @param null $default [optional] The default value to return on failure.
     *
     * @return array An array of sources.
     */
    public static function getCombinedPaths($parents, $children = null, $includeInvalid = true, $default = null) {
        // Convert $sources and $children into an array of sources, and make sure $sources is valid
        if(($parents = self::asPathArray($parents, false, null)) === null)
            return $default;
        $children = self::asPathArray($parents, false, null);

        // Count the number of parents and set whether only a single parent is used
        $parentCount = sizeof($parents);
        $singlePath = ($parentCount == 1);

        // Make sure any child is available, if not return the list of sources
        if($children === null)
            return $parents;
        if(($childCount = sizeof($children)) <= 0)
            return $parents;

        // Check whether only a single child is used
        $singleChild = ($childCount == 1);

        // Create a new array to put all sources in
        $paths = Array();

        // Loop through all the parents
        for($parentIndex = 0; $parentIndex < $parentCount; $parentIndex++) {
            // Choose what child's to loop through
            $childFirst = 0;
            $childLast = 0;
            if($singlePath)
                $childLast = $childCount - 1;
            elseif(!$singleChild)
                $childFirst = ($childLast = $parentIndex);

            // Loop through all the child's
            for($childIndex = $childFirst; $childIndex <= $childLast; $childIndex++) {
                // Get the base parent and child
                $parent = $parents[$parentIndex];
                $child = $children[$childIndex];

                // Combine the base path and make sure it's valid if invalid items may not be implemented
                if(($path = FilesystemObjectHelper::getCombinedPath($parent, $child, null)) === null && !$includeInvalid)
                    continue;

                // Add the path to the sources list
                array_push($paths, $path);
            }
        }

        // Return the path list
        return $paths;
    }

    /**
     * Check whether the filesystem objects exist.
     *
     * @param array|FilesystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or as single filesystem object instance or filesystem object path as a string.
     * @param bool $all [optional] True to make sure all sources from the arra exist, false to just check whether at least
     * one exists.
     *
     * @return bool True if the filesystem objects exist. If $all is set to true, all filesystem objects exist when true
     * is returned, if $all is set to false at least one of the filesystem objects exists.
     */
    public static function exists($paths, $all = true) {
        // Convert $sources into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($paths, false, null)) === null)
            return false;

        // Count the number of sources and loop through each path to check whether they exist
        $pathCount = sizeof($paths);
        for($i = 0; $i < $pathCount; $i++) {
            // Check whether the path exists if only one path has to be valid
            if(($exists = FilesystemObjectHelper::exists($paths[$i])) && !$all)
                return true;

            // Make sure the path exists if all sources must exist
            if(!$exists && $all)
                return false;
        }

        // Return the result. True if all sources needed to exist, false if just one had to exist
        return $all;
    }

    /**
     * Check how many file system objects exist.
     *
     * @param array|FileSystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or as a single filesystem object instance or filesystem object path as a string.
     *
     * @return int The number of filesystem objects that exist. Null will be returned if the $sources parameter is invalid.
     */
    public static function existCount($paths) {
        // Convert $sources into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($paths, false, null)) === null)
            return false;

        // Keep track of the number of existing sources
        $existing = 0;

        // Count the number of sources and loop through each path to check whether it exists
        $pathCount = sizeof($paths);
        for($i = 0; $i < $pathCount; $i++)
            // Check whether the path exists if only one path has to be valid
            if(FilesystemObjectHelper::exists($paths[$i]))
                $existing++;

        // Return the number of existing sources
        return $existing;
    }

    /**
     * Check whether an array of filesystem objects exists and are files.
     *
     * @param array|FilesystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or a single filesystem object instance or filesystem object path as a string.
     * @param bool $all [optional] True to make sure all sources form the array are files, false to just check whether
     * at least one is a file.
     *
     * @return bool True if the filesystem objects are files. If $all is set to true, all filesystem object are files
     * when true is returned, if $all is set to false at least one of the filesystem objects is a file.
     */
    public static function areFiles($paths, $all = true) {
        // Convert $sources into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($paths, false, null)) === null)
            return false;

        // Count the number of sources and loop through each path to validate it
        $pathCount = sizeof($paths);
        for($i = 0; $i < $pathCount; $i++) {
            // Check whether the path is a file if only one path has to be a file
            if(($isFile = FilesystemObjectHelper::isFile($paths[$i])) && !$all)
                return true;

            // Make sure the path is file if all sources must be files
            if(!$isFile && $all)
                return false;
        }

        // Return the result. True if all sources needed to be an existing file, false if just one had to be a file
        return $all;
    }

    /**
     * Check whether an array of filesystem objects exists and are directories.
     *
     * @param array|FilesystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or a single filesystem object instance or filesystem object path as a string.
     * @param bool $all [optional] True to make sure all sources form the array are directories, false to just check
     * whether at least one is a directory.
     *
     * @return bool True if the filesystem objects are directories. If $all is set to true, all filesystem object are
     * directories when true is returned, if $all is set to false at least one of the filesystem objects is a directory.
     */
    public static function areDirectories($paths, $all = true) {
        // Convert $sources into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($paths, false, null)) === null)
            return false;

        // Count the number of sources and loop through each path to validate it
        $pathCount = sizeof($paths);
        for($i = 0; $i < $pathCount; $i++) {
            // Check whether the path is a directory if only one path has to be a directory
            if(($isDir = FilesystemObjectHelper::isDirectory($paths[$i])) && !$all)
                return true;

            // Make sure the path is a directory if all sources must be directories
            if(!$isDir && $all)
                return false;
        }

        // Return the result. True if all sources needed to be an existing directory, false if just one had to be a directory
        return $all;
    }

    /**
     * Check whether an array of filesystem objects exists and are symbolic links.
     *
     * @param array|FilesystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or a single filesystem object instance or filesystem object path as a string.
     * @param bool $all [optional] True to make sure all sources form the array are symbolic links, false to just check
     * whether at least one is a symbolic link.
     *
     * @return bool True if the filesystem objects are symbolic links. If $all is set to true, all filesystem object are
     * symbolic links when true is returned, if $all is set to false at least one of the filesystem objects is a
     * symbolic link.
     */
    public static function areSymbolicLinks($paths, $all = true) {
        // Convert $sources into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($paths, false, null)) === null)
            return false;

        // Count the number of sources and loop through each path to validate it
        $pathCount = sizeof($paths);
        for($i = 0; $i < $pathCount; $i++) {
            // Check whether the path is a symbolic link if only one path has to be a symbolic link
            if(($isSymLink = FilesystemObjectHelper::isDirectory($paths[$i])) && !$all)
                return true;

            // Make sure the path is a symbolic link if all sources must be symbolic links
            if(!$isSymLink && $all)
                return false;
        }

        // Return the result. True if all sources needed to be an existing symbolic link, false if just one had to be a symbolic link
        return $all;
    }

    /**
     * Check whether an array of filesystem objects are readable.
     *
     * @param array|FilesystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or a single filesystem object instance or filesystem object path as a string.
     * @param bool $all [optional] True to make sure all sources form the array are readable, false to just check whether
     * at least one is a readable.
     *
     * @return bool True if the filesystem objects are readable. If $all is set to true, all filesystem object are
     * readable when true is returned, if $all is set to false at least one of the filesystem objects is readable.
     */
    public static function areReadable($paths, $all = true) {
        // Convert $sources into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($paths, false, null)) === null)
            return false;

        // Count the number of sources and loop through each path to check whether it's readable
        $pathCount = sizeof($paths);
        for($i = 0; $i < $pathCount; $i++) {
            // Check whether the path is a readable if only one path has to be readable
            if(($isReadable = FilesystemObjectHelper::isReadable($paths[$i])) && !$all)
                return true;

            // Make sure the path is a readable if all sources must be readable
            if(!$isReadable && $all)
                return false;
        }

        // Return the result. True if all sources needed to be readable, false if just one had to be a readable
        return $all;
    }

    /**
     * Check whether an array of filesystem objects are writable.
     *
     * @param array|FilesystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or a single filesystem object instance or filesystem object path as a string.
     * @param bool $all [optional] True to make sure all sources form the array are writable, false to just check whether
     * at least one is a writable.
     *
     * @return bool True if the filesystem objects are writable. If $all is set to true, all filesystem object are
     * writable when true is returned, if $all is set to false at least one of the filesystem objects is writable.
     */
    public static function areWritable($paths, $all = true) {
        // Convert $sources into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($paths, false, null)) === null)
            return false;

        // Count the number of sources and loop through each path to check whether it's writable
        $pathCount = sizeof($paths);
        for($i = 0; $i < $pathCount; $i++) {
            // Check whether the path is a writable if only one path has to be writable
            if(($isWritable = FilesystemObjectHelper::isWritable($paths[$i])) && !$all)
                return true;

            // Make sure the path is a writable if all sources must be writable
            if(!$isWritable && $all)
                return false;
        }

        // Return the result. True if all sources needed to be writable, false if just one had to be a writable
        return $all;
    }

    /**
     * Check whether an array of filesystem objects are writeable.
     *
     * @param array|FilesystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or a single filesystem object instance or filesystem object path as a string.
     * @param bool $all [optional] True to make sure all sources form the array are writeable, false to just check whether
     * at least one is a writeable.
     *
     * @return bool True if the filesystem objects are writeable. If $all is set to true, all filesystem object are
     * writeable when true is returned, if $all is set to false at least one of the filesystem objects is writeable.
     */
    public static function areWriteable($paths, $all = true) {
        return self::areWritable($paths, $all);
    }

    /**
     * Get the basename for each filesystem object.
     *
     * @param array|FilesystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or a single filesystem object instance or filesystem object path as a string.
     * @param string|null $suffix [optional] Suffix to omit from each basename. Null to ignore this feature.
     * @param mixed|null $default [optional] A default value that will be put into the array if the basename of a
     * specific filesystem object couldn't be found.
     *
     * @return array An array of basenames.
     */
    public static function getBasenames($paths, $suffix = null, $default = null) {
        // Convert $sources into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($paths, false, null)) === null)
            return null;

        // Create an array to put all the basenames in
        $names = Array();

        // Count the number of sources and loop through each path to get it's basename
        $pathCount = sizeof($paths);
        for($i = 0; $i < $pathCount; $i++)
            // Get the basename of the path and add it to the basenames array
            array_push($names, FilesystemObjectHelper::getBasename($paths[$i], $suffix, $default));

        // Return the list of basenames
        return $names;
    }

    /**
     * Get the parent for each filesystem object.
     *
     * @param array|FilesystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or a single filesystem object instance or filesystem object sources as a string.
     * @param mixed|null $default [optional] A default value that will be put into the array if the parent of a specific
     * filesystem object couldn't be found.
     *
     * @return array An array of parents.
     */
    public static function getParents($paths, $default = null) {
        // Convert $sources into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($paths, false, null)) === null)
            return null;

        // Create an array to put all the parents in
        $parents = Array();

        // Count the number of sources and loop through each path to get it's parent
        $pathCount = sizeof($paths);
        for($i = 0; $i < $pathCount; $i++)
            // Get the parent of the path and add it to the parents array
            array_push($parents, FilesystemObjectHelper::getParent($paths[$i], $default));

        // Return the list of parents
        return $parents;
    }

    /**
     * Check whether an array of filesystem objects has parents.
     *
     * @param array|FilesystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or a single filesystem object instance or filesystem object path as a string.
     * @param bool $all [optional] True to make sure all sources form the array have parents, false to just make sure at
     * least one of the sources has a parent.
     *
     * @return bool True if the filesystem objects have a parent. If $all is set to true, all filesystem object have a
     * parent when true is returned, if $all is set to false at least one of the filesystem objects has a parent.
     */
    public static function hasParents($paths, $all = true) {
        // Convert $sources into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($paths, false, null)) === null)
            return false;

        // Count the number of sources and loop through each path to check whether each one has a parent or not
        $pathCount = sizeof($paths);
        for($i = 0; $i < $pathCount; $i++) {
            // Check whether the path has a parent or not
            if(($hasParent = FilesystemObjectHelper::hasParent($paths[$i])) && !$all)
                return true;

            // Make sure the path has a parent if all must have a parent
            if(!$hasParent && $all)
                return false;
        }

        // Return the result. True if all sources needed to have a parent, false if just one has a parent
        return $all;
    }

    /**
     * Get the normalized path for each filesystem object.
     *
     * @param array|FilesystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or a single filesystem object instance or filesystem object path as a string.
     * @param mixed|null $default [optional] A default value that will be put into the array if the normalized path of a
     * specific filesystem object couldn't be found.
     *
     * @return array An array of normalized sources.
     */
    public static function getNormalizedPaths($paths, $default = null) {
        // Convert $sources into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($paths, false, null)) === null)
            return null;

        // Create an array to put all the normalized sources in
        $normalized = Array();

        // Count the number of sources and loop through each path to get it's normalized path
        $pathCount = sizeof($paths);
        for($i = 0; $i < $pathCount; $i++)
            // Get the normalized path and put it into the array
            array_push($normalized, FilesystemObjectHelper::getNormalizedPath($paths[$i], $default));

        // Return the list of normalized sources
        return $normalized;
    }

    /**
     * Get the absolute path for each filesystem object.
     *
     * @param array|FilesystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or a single filesystem object instance or filesystem object path as a string.
     * @param mixed|null $default [optional] A default value that will be put into the array if the absolute path of a
     * specific filesystem object couldn't be found.
     *
     * @return array An array of absolute sources.
     */
    public static function getAbsolutePaths($paths, $default = null) {
        // Convert $sources into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($paths, false, null)) === null)
            return null;

        // Create an array to put all the absolute sources in
        $absolute = Array();

        // Count the number of sources and loop through each path to get it's absolute path
        $pathCount = sizeof($paths);
        for($i = 0; $i < $pathCount; $i++)
            // Get the absolute path and put it into the array
            array_push($absolute, FilesystemObjectHelper::getAbsolutePath($paths[$i], $default));

        // Return the list of absolute sources
        return $absolute;
    }

    /**
     * Get the canonical path for each filesystem object.
     *
     * @param array|FilesystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or a single filesystem object instance or filesystem object path as a string.
     * @param mixed|null $default [optional] A default value that will be put into the array if the canonical path of a
     * specific filesystem object couldn't be found.
     *
     * @return array An array of canonical sources.
     */
    public static function getCanonicalPaths($paths, $default = null) {
        // Convert $sources into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($paths, false, null)) === null)
            return null;

        // Create an array to put all the canonical sources in
        $canonical = Array();

        // Count the number of sources and loop through each path to get it's canonical path
        $pathCount = sizeof($paths);
        for($i = 0; $i < $pathCount; $i++)
            // Get the canonical path and put it into the array
            array_push($canonical, FilesystemObjectHelper::getCanonicalPath($paths[$i], $default));

        // Return the list of canonical sources
        return $canonical;
    }

    /**
     * Delete a list of filesystem objects if they exists.
     * Directories will only be deleted if they're empty unless the $recursive param is set to true.
     *
     * @param Array|FilesystemObject|string $paths Filesystem object instance or a path string to delete.
     * Or an array with filesystem object instances or path strings. An array may contain multiple other arrays.
     * @param bool $recursive [optional] True to delete directories recursively.
     * This option should be true if directories with contents should be deleted.
     * @param resource $context [optional] See the unlink() function for documentation.
     *
     * @return int Number of deleted filesystem objects, a negative number will be returned on failure. This will also
     * count the number of deleted recursive filesystem objects.
     *
     * @see unlink()
     */
    public static function delete($paths, $recursive = false, $context = null) {
        // Convert the sources into a string array, return the $default value if failed
        if(($paths = self::asPathArray($paths)) === null)
            return -1;

        // Count the deleted filesystem objects
        $count = 0;

        // Delete each filesystem object
        $size = sizeof($paths);
        for($i = 0; $i < $size; $i++) {
            $path = $paths[$i];

            // Delete the current item
            $count += FilesystemObjectHelper::delete($path, $recursive, $context);
        }

        // Return the number of delete filesystem objects
        return $count;
    }

    /**
     * Rename an array of filesystem objects.
     *
     * @param Array|FileSystemObject|string $oldPaths The filesystem object instance or the path of the filesystem
     * object as a string, or an array of filesystem objects. The filesystem object must be a existing filesystem object.
     * The number of old files must be equal to the number of new files.
     * @param Array|FileSystemObject|string $newPaths The filesystem object instance of the path of the filesystem
     * object to rename the object to, or an array of filesystem objects. This filesystem object or path should include
     * the full path. The object may only exist if $overwrite is set to true or the renaming will fail. The number of
     * new files must be equal to the number of old files.
     * @param bool $overwrite [optional] True to overwrite the existing filesystem object when the target name already
     * exist, false otherwise.
     * @param resource $context [optional] See the rename() function for documentation.
     *
     * @return int The number of filesystem objects that were renamed successfully. A negative number will be returned
     * if an error occurred. Zero will be returned if no filesystem object was moved.
     *
     * @see rename();
     */
    public static function rename($oldPaths, $newPaths, $overwrite = false, $context = null) {
        // Convert $sources and $destinations into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($oldPaths, false, null)) === null)
            return null;
        if(($paths = self::asPathArray($newPaths, false, null)) === null)
            return null;

        // Count the number of old and new sources
        $oldCount = sizeof($oldPaths);
        $newCount = sizeof($newPaths);

        // Make sure there's at least one path available, and make sure count of old and new sources is equal
        if($oldCount <= 0)
            return 0;
        if($oldCount !== $newCount)
            return -1;

        // Validate each old path
        if(!self::exists($oldPaths, true) || self::areSymbolicLinks($oldPaths, false))
            return -1;

        // Make sure all new sources are valid
        if(!self::areValid($newPaths, true))
            return -1;

        // Do an overwrite check
        if(self::exists($newPaths, false) && !$overwrite)
            return -1;

        // Rename each path, and count the number of renamed sources
        $count = 0;
        for($i = 0; $i < $oldCount; $i++)
            $count += (FilesystemObjectHelper::rename($oldPaths[$i], $newPaths[$i], $overwrite, $context) ? 1 : 0);

        // Return the number of renamed sources
        return $count;
    }

    /**
     * Move an array of filesystem objects. The filesystem objects that should be moved must exist.
     *
     * @param FileSystemObject|string $sources The filesystem object must exist. The filesystem object instance or the
     * path of the filesystem object as a string, or an array of filesystem objects. The filesystem object must be a
     * existing filesystem object. The number of sources must be equal or greater than the number of destinations.
     * @param FileSystemObject|string $destinations The filesystem object. The filesystem object instance or the
     * path of the filesystem object as a string, or an array of filesystem objects. This filesystem object or path
     * should be a full/absolute path. The number of destinations should equal the number of sources, or should equal one.
     * If the number of destinations equals one while the number of sources is greater, all sources will be moved into the
     * available target (the target must be a directory, in that case).
     * @param bool $overwrite [optional] True to overwrite the target with the path if the target already exists. If only a single
     * target is set while multiple sources are available, the $overwrite argument will apply to the objects being moved
     * into the target directory.
     * @param resource $context [optional] See the rename() function for documentation.
     *
     * @return int The number of moved files, a negative number on failure.
     *
     * @see rename();
     */
    public static function move($sources, $destinations, $overwrite = false, $context = null) {
        // Convert the old and new sources into an array of sources
        if(($sources = self::asPathArray($sources, false, null)) === null)
            return false;
        if(($destinations = self::asPathArray($destinations, false, null)) === null)
            return false;

        // Count the number of old and new sources
        $oldCount = sizeof($sources);
        $newCount = sizeof($destinations);

        // Make sure there's at least one path available, and make sure the number of new sources equals the number of old sources, or equals one
        if($oldCount <= 0)
            return 0;
        if($oldCount !== $newCount && $newCount != 1)
            return -1;

        // Validate each old path
        if(!self::exists($sources, true) || self::areSymbolicLinks($sources, false))
            return -1;

        // Make sure all new sources are valid
        if(!self::areValid($destinations, true))
            return -1;

        // Do an overwrite check
        if(self::exists($destinations, false) && !$overwrite)
            return -1;

        // Count the number of moved filesystem objects
        $count = 0;

        // Check whether the old sources should all be copied into the new path (directory)
        if($oldCount > 1 && $newCount == 1) {
            // Get the new path
            $newPath = $destinations[0];

            // Make sure the new path is a directory
            if(!FilesystemObjectHelper::isDirectory($newPath))
                return -1;

            // Loop through each path that should be copied
            for($i = 0; $i < $oldCount; $i++) {
                // Get the current entry
                $entry = $sources[$i];

                // Get the target path for this entry
                $entryTarget = new FilesystemObject($newPath, FilesystemObjectHelper::getBasename($entry));

                // Copy the old path
                $count += FilesystemObjectHelper::move($entry, $entryTarget, $overwrite, $context) ? 1 : 0;
            }

        } else {
            // Copy each path, and count the number of renamed sources
            for($i = 0; $i < $oldCount; $i++)
                $count += FilesystemObjectHelper::move($sources[$i], $destinations[$i], $overwrite, $context) ? 1 : 0;
        }

        // Return the number of copied sources
        return $count;
    }

    /**
     * Copy an array of filesystem objects. The filesystem objects that should be copied must exist.
     *
     * @param FileSystemObject|string $sources The filesystem object must exist. The filesystem object instance or the
     * path of the filesystem object as a string, or an array of filesystem objects. The filesystem object must be a
     * existing filesystem object. The number of sources must be equal or greater than the number of destinations.
     * @param FileSystemObject|string $destinations The filesystem object. The filesystem object instance or the
     * path of the filesystem object as a string, or an array of filesystem objects. This filesystem object or path
     * should be a full/absolute path. The number of destinations should equal the number of sources, or should equal one.
     * If the number of destinations equals one while the number of sources is greater, all sources will be copied into the
     * available target (the target must be a directory, in that case).
     * @param bool $overwrite [optional] True to overwrite the target with the path if the target already exists. If only a single
     * target is set while multiple sources are available, the $overwrite argument will apply to the objects being copied
     * into the target directory.
     * @param resource $context [optional] See the rename() function for documentation.
     *
     * @return int The number of copied files, a negative number on failure.
     *
     * @see rename();
     */
    public static function copy($sources, $destinations, $overwrite = false, $context = null) {
        // Convert the old and new sources into an array of sources
        if(($sources = self::asPathArray($sources, false, null)) === null)
            return false;
        if(($destinations = self::asPathArray($destinations, false, null)) === null)
            return false;

        // Count the number of old and new sources
        $oldCount = sizeof($sources);
        $newCount = sizeof($destinations);

        // Make sure there's at least one path available, and make sure the number of new sources equals the number of old sources, or equals one
        if($oldCount <= 0)
            return 0;
        if($oldCount !== $newCount && $newCount != 1)
            return -1;

        // Validate each old path
        if(!self::exists($sources, true) || self::areSymbolicLinks($sources, false))
            return -1;

        // Make sure all new sources are valid
        if(!self::areValid($destinations, true))
            return -1;

        // Do an overwrite check
        if(self::exists($destinations, false) && !$overwrite)
            return -1;

        // Count the number of moved filesystem objects
        $count = 0;

        // Check whether the old sources should all be moved into the new path (directory)
        if($oldCount > 1 && $newCount == 1) {
            // Get the new path
            $newPath = $destinations[0];

            // Make sure the new path is a directory
            if(!FilesystemObjectHelper::isDirectory($newPath))
                return -1;

            // Loop through each path that should be moved
            for($i = 0; $i < $oldCount; $i++) {
                // Get the current entry
                $entry = $sources[$i];

                // Get the target path for this entry
                $entryTarget = new FilesystemObject($newPath, FilesystemObjectHelper::getBasename($entry));

                // Move the old path
                $count += FilesystemObjectHelper::copy($entry, $entryTarget, $overwrite, $context) ? 1 : 0;
            }

        } else {
            // Copy each object, and count the number of renamed sources
            for($i = 0; $i < $oldCount; $i++)
                $count += FilesystemObjectHelper::copy($sources[$i], $destinations[$i], $overwrite, $context) ? 1 : 0;
        }

        // Return the number of copied sources
        return $count;
    }

    /**
     * Validate an array of sources of filesystem objects. The filesystem objects don't need to exist to be valid.
     *
     * @param array|FilesystemObject|string $paths An array containing filesystem objects or filesystem object sources as
     * a string. Or a single filesystem object instance or filesystem object path as a string.
     * @param bool $all [optional] True to make sure all sources form the array are valid, false to just check whether
     * at least one is valid.
     *
     * @return bool True if the filesystem objects are valid. If $all is set to true, all filesystem object are valid
     * when true is returned, if $all is set to false at least one of the filesystem objects is valid.
     */
    public static function areValid($paths, $all = true) {
        // Convert $sources into an array of sources, and make sure it's valid
        if(($paths = self::asPathArray($paths, false, null)) === null)
            return false;

        // Count the number of sources and loop through each path to validate it
        $pathCount = sizeof($paths);
        for($i = 0; $i < $pathCount; $i++) {
            // Check whether the path is valid if only one path has to be valid
            if(($valid = FilesystemObjectHelper::isValid($paths[$i])) && !$all)
                return true;

            // Make sure the path is valid if all sources must be valid
            if(!$valid && $all)
                return false;
        }

        // Return the result. True if all sources needed to be valid, false if just one had to be valid
        return $all;
    }




    // TODO: All methods that return an integer should return -1 OR null on error?
}
