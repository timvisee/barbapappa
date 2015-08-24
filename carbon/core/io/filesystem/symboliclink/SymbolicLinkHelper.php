<?php

namespace carbon\core\io\filesystem\symboliclink;

use carbon\core\io\filesystem\directory\Directory;
use carbon\core\io\filesystem\FilesystemObject;
use carbon\core\io\filesystem\FilesystemObjectHelper;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class SymbolicLinkHelper extends FilesystemObjectHelper {

    /**
     * Get the SymbolicLink instance from a FileSystemObject instance or the path a string from a symbolic link.
     * If the filesystem object is an existing file or directory, $default will be returned.
     * The symbolic link or link path has to be valid.
     *
     * @param FilesystemObject|string $link Filesystem object instance or the path of a symbolic link as a string.
     * @param mixed|null $default [optional] Default value returned if the symbolic link couldn't be instantiated,
     * possibly because the $file param was invalid.
     *
     * @return SymbolicLink|mixed The link as a SymbolicLink instance.
     * Or the $default param value if the link couldn't be cast to a SymbolicLink instance.
     */
    public static function asSymbolicLink($link, $default = null) {
        // Create a new symbolic link instance when $link is a string, or when $link is a FileSystemObject instance
        // but not a SymbolicLink
        if(is_string($link) || ($link instanceof FilesystemObject && !$link instanceof SymbolicLink))
            $link = new SymbolicLink($link);

        // The $link must be a file instance, if not, return the default
        if(!$link instanceof SymbolicLink)
            return $default;

        // Make sure the symbolic link is valid, if not, return the $default value
        if(!$link->isValid())
            return $default;

        // Return the symbolic link
        return $link;
    }

    /**
     * Create a symbolic link to the existing target with the specified link.
     *
     * @param \carbon\core\io\filesystem\FilesystemObject|string $target Target of the link.
     * @param \carbon\core\io\filesystem\FilesystemObject|string $link The link name.
     *
     * @return SymbolicLink Symbolic link instance on success, null on failure.
     */
    public static function createSymbolicLink($target, $link) {
        // Convert Path instances to strings
        if($target instanceof FilesystemObject)
            $target = $target->getPath();
        if($link instanceof FilesystemObject)
            $link = $link->getPath();

        // Make sure the $target and $link params are strings
        if(!is_string($target) || !is_string($link))
            return null;

        // Create the symlink, return the result
        if(symlink($target, $link))
            // TODO: Should we prepend __DIR__ to the $link bellow?
            return new self($link);
        return null;
    }

    /**
     * Get the target of the symbolic link.
     *
     * @param SymbolicLink|string $link Symbolic link instance or the symbolic link path as a string.
     * @param mixed|null $default [optional] The default value returned on failure.
     *
     * @return Directory|\carbon\core\io\filesystem\file\File|SymbolicLink|FilesystemObject|null
     * The target will be returned as Directory, File or SymbolicLink instance if the target exist.
     * If the target doesn't exist it will be returned as FileSystemObject instance instead.
     * The $default value will be returned on failure.
     */
    public static function getTarget($link, $default = null) {
        // Convert the link into a path string, return the $default value if failed
        if(($link = self::asPath($link, false)) === null)
            return $default;

        // Read the link, and make sure it's valid
        if(($path = readlink($link)) === false)
            return $default;

        // Return the result
        return FilesystemObject::from($path);
    }

    /**
     * Check whether the symbolic link exists.
     *
     * @param SymbolicLink|string $link Symbolic link instance or the symbolic link path as a string.
     *
     * @return bool True if the symbolic link exists, false otherwise.
     * False will be returned if the path of the symbolic link is invalid.
     */
    public static function exists($link) {
        // Convert the link into a path string, return the $default value if failed
        if(($link = self::asPath($link, false)) === null)
            return false;

        // Get the symbolic link info and check whether the link exists, return the result
        $info = linkinfo($link);
        return $info !== 0 && $info !== false;
    }

    /**
     * Validate the path of the symbolic link object. The link doesn't need to exist.
     * The symbolic link may not be an existing file or directory.
     *
     * @param FilesystemObject|string $path Filesystem object instance or the path as a string.
     *
     * @return bool True if the path of the filesystem object seems to be valid, false otherwise.
     */
    public static function isValid($path) {
        // Convert the path into a string, return false if failed
        if(($path = self::asPath($path, false)) === null)
            return false;

        // Make sure the file is valid as FileSystemObject
        if(!FilesystemObjectHelper::isValid($path))
            return false;

        // TODO: Use better validation!

        // Make sure the symbolic link isn't a an existing file or directory, return the result.
        return !(FilesystemObjectHelper::isFile($path) || FilesystemObjectHelper::isDirectory($path));
    }
}
 