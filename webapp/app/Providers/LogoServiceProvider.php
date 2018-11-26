<?php

namespace App\Providers;

use App\Services\LogoService;
use Illuminate\Support\ServiceProvider;

class LogoServiceProvider extends ServiceProvider {

    /**
     * Register the singleton.
     */
    public function register() {
        $this->app->singleton('logo', function($app) {
            return new LogoService($app);
        });
    }
}