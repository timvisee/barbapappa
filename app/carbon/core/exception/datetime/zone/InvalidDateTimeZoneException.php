<?php

/**
 * InvalidDateTimeZoneException.php
 *
 * Carbon Core DateTimeZone exception.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core\exception\datetime\zone;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * InvalidDateTimeZoneException class.
 *
 * @package carbon\core\exception\datetime\zone
 *
 * @author Tim Visee
 */
class InvalidDateTimeZoneException extends DateTimeZoneException { }