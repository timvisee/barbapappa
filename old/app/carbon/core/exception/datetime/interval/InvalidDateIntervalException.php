<?php

/**
 * InvalidDateIntervalException.php
 *
 * Carbon Core DateTime exception.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core\exception\datetime\interval;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * InvalidDateIntervalException class.
 *
 * @package carbon\core\exception\datetime
 *
 * @author Tim Visee
 */
class InvalidDateIntervalException extends DateIntervalException { }