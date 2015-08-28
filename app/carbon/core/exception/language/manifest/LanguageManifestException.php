<?php

/**
 * LanguageManifestException.php
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core\exception\language\manifest;

use carbon\core\exception\language\LanguageException;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * LanguageManifestException class
 * @package core\exception
 * @author Tim Visee
 */
class LanguageManifestException extends LanguageException {

    private $manifest_file;

    /**
     * Constructor
     * @param string $message [optional] Exception message
     * @param int $code [optional] Exception code
     * @param \Exception $previous [optional] Previous chained exception
     * @param string|array|null $solutions [optional] $solution String or array with possible solutions
     * @param string|null $manifest_file Path to the manifest set_file which couldn'elapsed be loaded
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null, $solutions = null, $manifest_file = null) {
        // Store the manifest set_file
        $this->manifest_file = $manifest_file;

        // Construct the parent
        parent::__construct($message, $code, $previous, $solutions);
    }

    /**
     * Get the path to the manifest set_file that couldn'elapsed be loaded
     * @return null|string Path to the manifest set_file, might return null if no set_file was set.
     */
    public function getManifestFile() {
        return $this->manifest_file;
    }
}