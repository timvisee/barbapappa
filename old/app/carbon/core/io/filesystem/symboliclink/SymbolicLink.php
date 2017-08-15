<?php

namespace carbon\core\io\filesystem\symboliclink;

use carbon\core\io\filesystem\FilesystemObject;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class SymbolicLink extends FilesystemObject {

    // TODO: Ability to create a symbolic link, with a static function.

    /**
     * Get the SymbolicLink instance from a FileSystemObject instance or the path a string from a symbolic link.
     * If the filesystem object is an existing file or directory, null will be returned.
     * The symbolic link or link path has to be valid.
     *
     * @param \carbon\core\io\filesystem\FilesystemObject|string $link Filesystem object instance or the path of a symbolic link as a string.
     *
     * @return SymbolicLink|null The link as a SymbolicLink instance.
     * Or null if the link couldn't be cast to a SymbolicLink instance.
     */
    public static function asSymbolicLink($link) {
        return SymbolicLinkHelper::asSymbolicLink($link, null);
    }

    /**
     * Get the name of the symbolic link without any path information.
     * Alias of {@link FileSystemObject::getBasename()}
     *
     * @return string Name of the symbolic link.
     *
     * @see FileSystemObject::getBasename();
     */
    public function getLinkName() {
        return $this->getBasename();
    }

    /**
     * Get the target of the symbolic link.
     *
     * @return \carbon\core\io\filesystem\directory\Directory|\carbon\core\io\filesystem\file\File|SymbolicLink|\carbon\core\io\filesystem\FilesystemObject|null
     * The target will be returned as Directory, File or SymbolicLink instance if the target exist.
     * If the target doesn't exist it will be returned as Path instance instead.
     * Null will be returned if the symbolic link was invalid.
     */
    public function getTarget() {
        return SymbolicLinkHelper::getTarget($this);
    }

    /**
     * Check whether the symbolic link exists.
     *
     * @return bool True if the symbolic link exists, false otherwise.
     * False will be returned if the path of the symbolic link is invalid.
     */
    public function exists() {
        return SymbolicLink::exists($this);
    }

    /**
     * Check whether the symbolic link is valid. The link doesn't need to exist.
     * The file may not be an existing file or directory.
     *
     * @return bool True if the symbolic link seems to be valid, false otherwise.
     */
    public function isValid() {
        return SymbolicLinkHelper::isValid($this);
    }
}
