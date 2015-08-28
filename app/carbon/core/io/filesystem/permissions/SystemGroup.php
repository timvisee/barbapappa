<?php

namespace carbon\core\io\filesystem\permissions;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

// TODO: Implement a lot more features in this class!

class SystemGroup {

    /** @var int Group ID */
    private $gid;

    /**
     * Constructor
     *
     * @param int|SystemGroup $gid Group ID
     */
    // TODO: Support group names as argument
    public function __construct($gid) {
        $this->gid = $gid;
    }

    /**
     * Get the group ID
     *
     * @return int Group ID
     */
    public function getId() {
        return $this->gid;
    }
}
