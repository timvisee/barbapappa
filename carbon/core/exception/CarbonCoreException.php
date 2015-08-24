<?php

/**
 * CarbonCoreException.php
 *
 * Main Carbon CMS Core Exception.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core\exception;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * CarbonCoreException class
 * @package core\exception
 * @author Tim Visee
 */
class CarbonCoreException extends CarbonException {

    // TODO: Should we remove this class to directly extend the CarbonException class (Which should then be renamed)?
}