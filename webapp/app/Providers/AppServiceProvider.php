<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
