<?php

/**
 * RegistryException.php
 *
 * Carbon CMS Registry Exception class set_file
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core\exception\registry;

use carbon\core\exception\CarbonCoreException;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * RegistryException class
 * @package core\exception
 * @author Tim Visee
 */
class RegistryException extends CarbonCoreException { }