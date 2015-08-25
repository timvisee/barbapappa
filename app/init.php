<?php

// Make sure the app is only initialized once
if(defined('APP_INIT_DONE') && APP_INIT_DONE === true)
    return;

// Define the site root for Carbon
define('CARBON_SITE_ROOT', dirname(__DIR__));

// Define various app constants
/** The app namespace. */
define('APP_NAMESPACE', 'app\\');
/** The required PHP version to run the app. */
define('APP_PHP_VERSION_REQUIRED', '5.3.1');
/** The root directory of the app. */
define('APP_ROOT', __DIR__);
/** The application name. */
define('APP_NAME', 'BarApp');
/** The version name of the currently installed app instance. */
define('APP_VERSION_NAME', '0.1');
/** The version code of the currently installed app instance. */
define('APP_VERSION_CODE', 1);

// Make sure the current PHP version is supported
if(version_compare(phpversion(), APP_PHP_VERSION_REQUIRED, '<'))
    // PHP version the server is running is not supported, show an error message
    // TODO: Show proper error message
    die('This server is running PHP ' . phpversion() . ', the required PHP version to start the application is PHP ' . APP_PHP_VERSION_REQUIRED . ' or higher,
            please install PHP ' . APP_PHP_VERSION_REQUIRED . ' or higher on your server!');

/** Defines whether app is initializing or initialized. */
define('APP_INIT', true);

// Initialize, load and set up Carbon Core
require_once(CARBON_SITE_ROOT . '/carbon/core/init.php');

// Make sure Carbon Core is initialized successfully
if(!defined('CARBON_CORE_INIT_DONE') || CARBON_CORE_INIT_DONE != true)
    die('Failed to load the application because Carbon Core couldn\'t be initialized');

// Include the loader for the app and set it up
require_once(APP_ROOT . '/autoloader/loader/AppLoader.php');
use app\autoloader\loader\AppLoader;
use carbon\core\autoloader\Autoloader;
Autoloader::addLoader(new AppLoader());

// Load the configuration
use app\config\Config;
Config::load();

// Set up the error handler
use carbon\core\ErrorHandler;
ErrorHandler::init(true, true, Config::getValue('app', 'debug'));

// Set up the language manager
use app\language\LanguageManager;
LanguageManager::init(true, Config::get('app', 'language.default'));

// Connect to the database
use app\database\Database;
Database::connect();

// Validate the user session
use app\session\SessionManager;
SessionManager::validateSession();

// The app initialized successfully, define the APP_INIT_DONE constant to store the initialization state
/** Defines whether the app is initialized successfully. */
define('APP_INIT_DONE', true);
