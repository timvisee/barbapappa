<?php

// Prevent direct requests to this file due to security reasons
defined('CARBON_SITE_ROOT') or die('Access denied!');

// Make sure Carbon Core is only initialized once
if(defined('CARBON_CORE_INIT_DONE') && CARBON_CORE_INIT_DONE === true)
    return;

// Define various Carbon Core constants
/** The Carbon Core namespace. */
define('CARBON_CORE_NAMESPACE', 'carbon\\core\\');
/** The required PHP version to run Carbon Core. */
define('CARBON_CORE_PHP_VERSION_REQUIRED', '5.3.1');
/** The root directory of Carbon Core. */
define('CARBON_CORE_ROOT', __DIR__);
/** The version name of the currently installed Carbon Core instance. */
define('CARBON_CORE_VERSION_NAME', '0.1');
/** The version code of the currently installed Carbon Core instance. */
define('CARBON_CORE_VERSION_CODE', 1);

// Make sure the current PHP version is supported
if(version_compare(phpversion(), CARBON_CORE_PHP_VERSION_REQUIRED, '<'))
    // PHP version the server is running is not supported, show an error message
    // TODO: Show proper error message
    die('This server is running PHP ' . phpversion() . ', the required PHP version to start Carbon Core is PHP 5.3.1 or higher,
            please install PHP 5.3.1 or higher on your server!');

/** Defines whether Carbon Core is initializing or initialized. */
define('CARBON_CORE_INIT', true);

// Load and initialize the autoloader
require_once(CARBON_CORE_ROOT . '/autoloader/Autoloader.php');

use carbon\core\autoloader\Autoloader;
use carbon\core\autoloader\loader\CarbonCoreLoader;

// Set up the autoloader
Autoloader::init();
Autoloader::addLoader(new CarbonCoreLoader());

// Carbon Core initialized successfully, define the CARBON_CORE_INIT_DONE constant to store the initialization state
/** Defines whether Carbon Core is initialized successfully. */
define('CARBON_CORE_INIT_DONE', true);
