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
        // $this->loadViewsFrom(__DIR__ . '/../views', 'pay');
        // $this->publishes([
        //     __DIR__ . '/../views' => base_path('resources/views/timvisee/pay'),
        // ]);

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
    }
}
