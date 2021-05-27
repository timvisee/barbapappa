<?php

namespace App\Providers;

use App\Services\KioskAuthManager;
use \Illuminate\Support\ServiceProvider;

class KioskAuthServiceProvider extends ServiceProvider {

    /**
     * Register the singleton.
     */
    public function register() {
        $this->app->singleton('kioskauth', function($app) {
            return new KioskAuthManager($app);
        });
    }
}
