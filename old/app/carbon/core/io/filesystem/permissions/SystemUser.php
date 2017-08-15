<?php

namespace carbon\core\io\filesystem\permissions;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

// TODO: Implement a lot more features in this class!

class SystemUser {

    /** @var int User ID */
    private $uid;

    /**
     * Constructor
     *
     * @param int $uid User ID
     */
    // TODO: Support user names as argument
    public function __construct($uid) {
        $this->uid = $uid;
    }

    /**
     * Get the user ID
     *
     * @return int User ID
     */
    public function getId() {
        return $this->uid;
    }
}
 