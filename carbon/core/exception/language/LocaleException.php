<?php

/**
 * LocaleException.php
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core\exception\language;

use carbon\core\exception\CarbonCoreException;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * LocaleException class
 * @package core\exception
 * @author Tim Visee
 */
class LocaleException extends CarbonCoreException {

    // TODO: Should this extend the language exception?
}