<?php

namespace App\Providers;

use App\Services\HistoryManager;
use \Illuminate\Support\ServiceProvider;

class HistoryManagerServiceProvider extends ServiceProvider {

    /**
     * Register the singleton.
     */
    public function register() {
        $this->app->singleton('history', function($app) {
            return new HistoryManager($app);
        });
    }
}
