<?php

namespace App\Providers;

use App\Services\LanguageManagerService;
use Illuminate\Support\ServiceProvider;

class LanguageManagerServiceProvider extends ServiceProvider {

    /**
     * Register the singleton.
     */
    public function register() {
        $this->app->singleton('langManager', function($app) {
            return new LanguageManagerService($app);
        });
    }
}