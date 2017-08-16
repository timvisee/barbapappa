<?php

namespace App\Providers;

use App\Services\BarAuth;
use \Illuminate\Support\ServiceProvider;

class BarAuthServiceProvider extends ServiceProvider {

    /**
     * Register the singleton.
     */
    public function register() {
        $this->app->singleton('barauth', function($app) {
            return new BarAuth($app);
        });
    }
}