<?php

namespace App\Services;

use Illuminate\Foundation\Application;

class LanguageManagerService {

    /**
     * Application instance.
     * @var Application
     */
    private $app;

    /**
     * Language service constructor.
     *
     * @param Application $app Application instance.
     */
    public function __construct(Application $app) {
        $this->app = $app;

        // TODO: Figure out the current language of the user
    }
}