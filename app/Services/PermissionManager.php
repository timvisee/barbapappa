<?php

namespace App\Services;

use Illuminate\Foundation\Application;

class PermissionManager {

    /**
     * Application instance.
     * @var Application
     */
    private $app;

    /**
     * BarAuthManager constructor.
     *
     * @param Application $app Application instance.
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }

    public function init() {
        // TODO: automatically figure out what community and bar we are working with
    }

    // TODO: Method to determine the applicable groups for a user, based on his scope.
    // TODO: Method to get all normalized permissions for a user in it's scope or context.
    // TODO: Method to check whether the user has permission in a given scope or context.

}
