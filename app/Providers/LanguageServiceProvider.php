<?php

namespace App\Providers;

use App\Services\LanguageService;
use Illuminate\Support\ServiceProvider;

class LanguageServiceProvider extends ServiceProvider {

    /**
     * Register the singleton.
     */
    public function register() {
        $this->app->singleton('lang', function($app) {
            return new LanguageService($app);
        });
    }
}