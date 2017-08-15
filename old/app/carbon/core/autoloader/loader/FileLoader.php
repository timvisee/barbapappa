<?php

/**
 * FileLoader.php
 *
 * An autoloader loader to load files from a path based on their namespace.
 */

namespace carbon\core\autoloader\loader;

use carbon\core\exception\CarbonException;
use carbon\core\io\filesystem\directory\Directory;
use carbon\core\io\filesystem\directory\DirectoryHelper;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class FileLoader extends BaseLoader {

    /** @var string The base namespace, or an empty string for any namespace. */
    private $namespace;
    /** @var Directory The directory of the namespace. */
    private $dir;

    // TODO: Create a variable to set this extension?
    /** @const string The extension of the files, a prefixed period must be included. */
    const FILE_EXTENSION = '.php';

    /**
     * Constructor.
     *
     * @param string $namespace The base namespace, or an empty string for any namespace.
     * @param Directory|string $dir The directory of the namespace.
     *
     * @throws CarbonException Throws if the namespace or directory is invalid.
     */
    public function __construct($namespace, $dir) {
        // Set the namespace and directory
        $this->setNamespace($namespace);
        $this->setDirectory($dir);
    }

    /**
     * Get the base namespace.
     *
     * @return string The base namespace.
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * Set the base namespace. If an empty string is given all namespaces will be handled by this loader.
     * A namespace should be suffixed with a single backslash.
     *
     * @param string $namespace The base namespace, or an empty string for any namespace.
     */
    public function setNamespace($namespace) {
        // Check whether the namespace is an empty string
        if(trim($namespace) == '') {
            $this->namespace = '';
            return;
        }

        // Suffix a single backslash, and set the namespace
        $this->namespace = rtrim($namespace, '\\') . '\\';
    }

    /**
     * Get the namespace directory.
     *
     * @return Directory The namespace directory.
     */
    public function getDirectory() {
        return $this->dir;
    }

    /**
     * Set the namespace directory.
     *
     * @param Directory|string $dir The namespace directory.
     *
     * @throws CarbonException Throws if the directory is invalid.
     */
    public function setDirectory($dir) {
        // Parse the directory
        if(($dir = DirectoryHelper::asDirectory($dir, null)) === null)
            throw new CarbonException("Failed to set the directory of the file loader, the directory is invalid.");

        // Set the directory instance
        $this->dir = $dir;
    }

    /**
     * Check whether a class name or namespace is in the namespace of the file loader.
     * Namespaces must be suffixed with a single backslash.
     *
     * @param string $query The class name or namespace as a string.
     *
     * @return bool True if the class name or namespace is in the file loader namespace.
     */
    public function isInNamespace($query) {
        // Check whether the namespace is empty
        if($this->namespace == '')
            return true;

        // Check whether the class name or namespace is in the namespace of the file loader.
        return substr($query, 0, strlen($this->namespace)) == $this->namespace;
    }

    public function load($className) {
        // Check whether the class is in the current namespace, return false if not
        if(!$this->isInNamespace($className))
            return false;

        // Get the class path, relative to the file loader namespace
        $classNameRelative = substr($className, strlen($this->namespace));

        // Determine the path to load the class file from
        // TODO: Does the absolute path have a suffixed directory separator already?
        $classFile = $this->getDirectory()->getAbsolutePath() . DIRECTORY_SEPARATOR . $classNameRelative . static::FILE_EXTENSION;

        // Replace separators
        $classFile = str_replace('\\', DIRECTORY_SEPARATOR, $classFile);

        // Check whether the file exists, return false if not
        if(!is_file($classFile))
            return false;

        // Load the class file, return the result
        /** @noinspection PhpIncludeInspection */
        require_once($classFile);
        return true;
    }
}
