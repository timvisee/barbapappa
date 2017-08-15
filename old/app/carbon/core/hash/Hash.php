<?php

/**
 * Hash.php
 *
 * The Hash class is used to hash data.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright (C) Tim Visee 2012-2013, All rights reserved.
 */

namespace carbon\core\hash;

use app\config\Config;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

/**
 * Hash class.
 *
 * @package core
 * @author Tim Visee
 */
class Hash {

    /**
     * Hash data.
     *
     * @param string $data Data to hash.
     * @param string $algorithm [Optional] The algorithm to use, null to use the default algorithm specified in the config.
     * @param string $salt [Optional] The salt to use, null to use the default salt specified in the config.
     *
     * @return string The hashed data.
     */
    public static function hash($data, $algorithm = null, $salt = null) {
        // If the $algo param is not set, get the default value from the config set_file
        if($algorithm == null)
            $algorithm = Config::getValue('hash', 'algorithm');

        // If the $salt param was not set, get the default value from the config set_file
        if($salt == null)
            $salt = Config::getValue('hash', 'salt');

        // Hash the data
        $context = hash_init($algorithm, HASH_HMAC, $salt);
        hash_update($context, $data);

        // Return the hashed data
        return hash_final($context);
    }

    /**
     * Generate a random salt string.
     *
     * @return string Random salt string.
     */
    public static function generateSalt() {
        return md5(mt_rand(0, mt_getrandmax()));
    }
}
