<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Define the default string length, as required by the database migration
        // TODO: Why 191, maybe use 255?
        Schema::defaultStringLength(191);

        // Pagination controls view
        Paginator::defaultView('vendor.pagination.semantic-ui');
        Paginator::defaultSimpleView('vendor.pagination.semantic-ui');

        // Rate limiters
        RateLimiter::for('bunq-api', function() {
            // TODO: limit per environment/host
            // TODO: rough limit, limit to 3 per 3 seconds instead
            return Limit::perMinute(15);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
