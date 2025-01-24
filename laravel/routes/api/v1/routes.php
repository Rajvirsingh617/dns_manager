<?php

use App\Http\Controllers\ZoneController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('auth:api')->group(function () {
        // Get all zones
        Route::get('/auth-key', [ProfileController::class, 'showAuthKey'])->name('auth.key');
        Route::post('/auth-key/regenerate', [ProfileController::class, 'regenerateAuthKey'])->name('auth.key.regenerate');
        Route::get('/zones', [ZoneController::class, 'indexApi']);
        Route::get('/zones/{id}', [ZoneController::class, 'showApi']);
        // Create a new zone
        Route::post('/zones', [ZoneController::class, 'storeApi']);
        // Update zone records
        Route::put('/zones/{id}', [ZoneController::class, 'updateApi']);
        // Delete a zone
        Route::delete('/zones/{id}', [ZoneController::class, 'destroyApi']);
        //dns records
        Route::post('/zones/records/{uuid}', [ZoneController::class, 'storeRecordApi']);
        Route::get('/zones/records/{uuid}', [ZoneController::class, 'indexRecordsApi']);
        Route::put('/zones/{uuid}/records/{recordId}', [ZoneController::class, 'updateRecordApi']);
        Route::delete('/zones/{uuid}/records/{recordId}', [ZoneController::class, 'deleteRecordApi']);  // Delete a specific record from a zone


        
    });
});
