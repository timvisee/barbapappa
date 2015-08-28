<?php

/**
 * ConfigLoadingException.php
 *
 * Carbon CORE Config Load Exception class
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core\exception\config;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * ConfigLoadException class
 * @package core\exception
 * @author Tim Visee
 */
class ConfigLoadException extends ConfigException { }