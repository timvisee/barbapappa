<?php

namespace app\autoloader\loader;

use carbon\core\autoloader\loader\FileLoader;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class AppLoader extends FileLoader {

    /**
     * Constructor.
     *
     * Set up a file loader for the app classes and files.
     */
    public function __construct() {
        // TODO: Use the defined constant path for the app
        parent::__construct('app\\', CARBON_SITE_ROOT . '/app');
    }
}
