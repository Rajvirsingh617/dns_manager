<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->mapApiRoutes();
    }

    public function map()
{
        $this->mapApiV1Routes();

        $this->mapApiV2Routes();

        // Default Laravel API and web routes
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api/v1/routes.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
}

    protected function mapApiRoutes()
{
    Route::prefix('api')
    ->middleware('api')
    ->group(base_path('routes/api/v1/routes.php'));
    
    Route::prefix('api')
         ->middleware('api')
         ->group(base_path('routes/api/v1/routes.php'));  // for v1

    Route::prefix('api')
         ->middleware('api')
         ->group(base_path('routes/api/v2/routes.php'));  // for v2
}

protected function mapApiV2Routes()
{
    Route::middleware('api')
        ->prefix('api/v2')
        ->group(base_path('routes/api/v2/routes.php'));
}
}
