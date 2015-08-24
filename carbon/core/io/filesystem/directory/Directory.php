<?php

namespace carbon\core\io\filesystem\directory;

use carbon\core\io\filesystem\FilesystemObject;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class Directory extends FilesystemObject {

    /**
     * Get the Directory instance from a FileSystemObject instance or the path a string from a directory.
     * If the filesystem object is an existing file or symbolic link, null will be returned.
     * The directory or directory path has to be valid.
     *
     * @param FilesystemObject|string $dir Filesystem object instance or the path of a directory as a string.
     *
     * @return Directory|null The directory as a Directory instance.
     * Or null if the directory couldn't be cast to a Directory instance.
     */
    public static function asDirectory($dir) {
        return DirectoryHelper::asDirectory($dir);
    }

    /**
     * Get the name of the directory without any path information.
     * Alias of {@link FileSystemObject::getBasename()}.
     *
     * @return string Name of the directory.
     *
     * @see FileSystemObject::getBasename();
     */
    public function getDirectoryName() {
        return $this->getBasename();
    }

    /**
     * Attempt to create a directory at the path.
     *
     * @param int $mode [optional] See the mkdir() function for documentation.
     * @param bool $recursive [optional] See the mkdir() function for documentation.
     * @param null $context [optional] See the mkdir() function for documentation.
     *
     * @return Directory|null Directory instance on success, null on failure.
     *
     * @see mkdir()
     */
    public function createDirectory($mode = 0777, $recursive = false, $context = null) {
        // Make sure the mode is a valid number, if not, use the default value
        if(!is_numeric($mode))
            $mode = 0777;

        // Get the directory path
        $path = $this->getCanonicalPath();

        // TODO: Fix the context error in the main Carbon project!
        // Attempt to create the directory, return the result
        if($context === null)
            if(mkdir($path, $mode, $recursive))
                return new self($path);
        else
            if(mkdir($path, $mode, $recursive, $context))
                return new self($path);
        return null;
    }

    /**
     * Delete the contents of the directory.
     *
     * @param resource $context [optional] See the unlink() function for documentation
     *
     * @return int Number of deleted files, symbolic links and directories, a negative number will be returned if the
     * directory doesn't exist or on failure.
     *
     * @see unlink()
     */
    public function deleteContents($context = null) {
        // The directory must exist
        if(!$this->isDirectory())
            return -1;

        // Count the deleted files, symbolic links and directories
        $count = 0;

        // TODO: Use list methods from the File class if possible!
        // Get the directory path
        $dirPath = $this->getPath();
        if(substr($dirPath, strlen($dirPath) - 1, 1) != '/')
            $dirPath .= '/';

        // List and delete the files and sub directories
        // TODO: List all files using the list() method from the Directory class, instead of glob!
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach($files as $filePath) {
            $file = new FilesystemObject($filePath);
            $count += $file->delete($context, true);
        }

        // Return the number of removed files and directories
        return $count;
    }

    /**
     * Get all contents of the directory.
     *
     * @param bool $recursive [optional] True to read all directory contents recursively.
     * @param int $types [optional] The type of filesystem objects to include. Defaults to FLAG_TYPE_ALL which allows all
     * filesystem object types. Choose from:
     * - FLAG_TYPE_OBJECT
     * - FLAG_TYPE_FILE
     * - FLAG_TYPE_DIRECTORY
     * - FLAG_TYPE_SYMBOLIC_LINK
     * - FLAG_TYPE_ALL
     * Use null to default to FLAG_TYPE_ALL.
     *
     * @return array|mixed|null An array with all directory contents, or null on failure.
     *
     * @see FilesystemObjectFlags
     */
    // TODO: Add types parameter!
    public function getContents($recursive = false, $types = self::FLAG_TYPE_ALL) {
        return DirectoryHelper::getContents($this, $recursive, $types, null);
    }

    /**
     * Check whether the directory is valid. The directory doesn't need to exist.
     * The directory may not be an existing file or symbolic link.
     *
     * @return bool True if the directory seems to be valid, false otherwise.
     */
    public function isValid() {
        return DirectoryHelper::isValid($this);
    }
}
