<?php

/**
 * CarbonCoreLoader.php
 *
 * An autoloader loader to load Carbon Core classes and files.
 */

namespace carbon\core\autoloader\loader;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class CarbonCoreLoader extends FileLoader {

    /**
     * Constructor.
     *
     * Set up a file loader for Carbon Core classes and files.
     */
    public function __construct() {
        parent::__construct(CARBON_CORE_NAMESPACE, CARBON_CORE_ROOT);
    }
}
