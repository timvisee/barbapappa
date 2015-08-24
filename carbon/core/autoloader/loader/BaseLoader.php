<?php

/**
 * BaseLoader.php
 *
 * This base loader provides a basic interface for autoloader loaders.
 */

namespace carbon\core\autoloader\loader;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

abstract class BaseLoader {

    /**
     * Load a class through this loader, if this loader is applicable.
     *
     * @param string $className The full name of the class with it's namespace to load.
     *
     * @return bool True if the class was loaded, false otherwise.
     */
    public abstract function load($className);
}
