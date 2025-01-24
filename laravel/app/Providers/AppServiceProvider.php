<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // This is where you register any services or bindings, if necessary
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // This ensures that a connection to the database is successfully established
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            // Handle connection error (optional)
            echo "Database connection error: " . $e->getMessage();
        }

        // Set the default string length for database columns to prevent issues with MySQL's utf8mb4 character set
        Schema::defaultStringLength(191);
    }
}
