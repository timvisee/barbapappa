<?php

/**
 * CarbonException.php
 *
 * Main Carbon CMS Exception.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core\exception;

use Exception;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * CarbonException class
 * @package core\exception
 * @author Tim Visee
 */
class CarbonException extends \Exception {

    // TODO: Should we remove the solutions property?

    /** @var array|null $solutions Array with possible solutions for this exception, or null */
    private $solutions = null;

    /**
     * Constructor.
     *
     * @param string $message [optional] Exception message.
     * @param int $code [optional] Exception code
     * @param \Exception $previous [optional] Previous chained exception
     * @param string|array|null $solutions [optional] $solution String or array of possible solutions
     */
    public function __construct($message = '', $code = 0, Exception $previous = null, $solutions = null) {
        // Store the possible solution
        $this->setSolutions($solutions);

        // Construct the parent
        parent::__construct($message, $code, $previous);
    }

    /**
     * Check whether this exception has any possible solutions included
     * @return bool True if this exception has any possible solutions included
     */
    public function hasSolutions() {
        // Make sure the solutions doesn't equal to null
        if($this->solutions == null)
            return false;

        // The solutions must be an array
        return (is_array($this->solutions));
    }

    /**
     * Get an array containing all possible solutions for this exception
     * @return array Array containing all possible solutions for this exception. May be an empty array.
     */
    public function getSolutions() {
        if(is_array($this->solutions))
            return $this->solutions;
        return Array();
    }

    /**
     * Set the possible solutions for this exception
     * @param string|array|null $solutions String or array with possible solutions,
     * null to clear the list of solutions
     */
    public function setSolutions($solutions = null) {
        if($solutions != null) {
            if(is_array($solutions))
                $this->solutions = $solutions;
            else if(is_string($solutions))
                $this->solutions = Array($solutions);

        } else
            $solutions = null;
    }
}