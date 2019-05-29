<?php

namespace BarPay;

use Illuminate\Support\ServiceProvider;

class BarPayServiceProvider extends ServiceProvider {
    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        // Load provided
        $this->loadViewsFrom(__DIR__ . '/../views', 'barpay');
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'barpay');
    }
}
