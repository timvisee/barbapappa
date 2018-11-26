<?php

namespace App\Providers;

use App\Services\PermissionManager;
use Illuminate\Support\ServiceProvider;

class PermissionManagerServiceProvider extends ServiceProvider {

    /**
     * Register the singleton.
     */
    public function register() {
        $this->app->singleton('perms', function($app) {
            return new PermissionManager($app);
        });
    }
}