<?php

use App\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {
    Route::middleware('auth:api')->group(function () {
        // Get all zones
        Route::get('/zones', [ZoneController::class, 'indexApi']);
        // Get a single zone
        Route::get('/zones/{id}', [ZoneController::class, 'showApi']);
        // Create a new zone
        Route::post('/zones', [ZoneController::class, 'storeApi']);
        // Update zone records
        Route::put('/zones/{id}', [ZoneController::class, 'updateApi']);
        // Delete a zone
        Route::delete('/zones/{id}', [ZoneController::class, 'destroyApi']);
    });
});
