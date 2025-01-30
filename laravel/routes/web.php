<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NameServerController;

/* Route::get('/', function () {
    return view('welcome');
}); */

Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/register', [RegisterController::class, 'store']);
/* Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); */
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');


// Route to handle the password reset request
Route::post('/reset-password update', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/commit-changes', function () {
    return view('layouts.commit-changes');
})->name('commit.changes');

Route::middleware('auth')->group(function () {
    // Dashboard

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Change Password
    Route::get('/auth-key', [ProfileController::class, 'showAuthKey'])->name('auth.key');
    Route::post('/auth-key/regenerate', [ProfileController::class, 'regenerateAuthKey'])->name('auth.key.regenerate');
    Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [ProfileController::class, 'updatePassword'])->name('password.update');

    // Zones routes
    Route::resource('zones', ZoneController::class)->except(['index', 'show']);
    Route::get('/zones', [ZoneController::class, 'index'])->name('zones.index');
    Route::get('/zones/{id}', [ZoneController::class, 'show'])->name('zones.show');
    Route::get('/zones/{id}/edit', [ZoneController::class, 'edit'])->name('zones.editzone');
    Route::put('/zones/{id}/update-records', [ZoneController::class, 'updateRecords'])->name('zones.updateRecords');
    Route::put('/zones/{id}', [ZoneController::class, 'update'])->name('zones.update');
    Route::delete('/records/{id}', [ZoneController::class, 'destroy'])->name('zones.destroy');
    //name server
     Route::resource('name-servers', NameServerController::class);
    /* Route::get('name-servers', [NameServerController::class, 'index'])->name('name-servers.index');
    Route::get('name-servers/create', [NameServerController::class, 'create'])->name('name-servers.create');
    Route::post('name-servers', [NameServerController::class, 'store'])->name('name-servers.store');
    Route::get('name-servers/{name_server}', [NameServerController::class, 'show'])->name('name-servers.show');
    Route::get('name-servers/{name_server}/edit', [NameServerController::class, 'edit'])->name('name-servers.edit');
    Route::patch('name-servers/{name_server}', [NameServerController::class, 'update'])->name('name-servers.update');
    Route::post('name-servers/delete/{name_server}', [NameServerController::class, 'destroy'])->name('name-servers.destroy');

 */
    Route::delete('name-servers/{name_server}', [NameServerController::class, 'destroy'])->name('name-servers.destroy');
    Route::post('name-servers', [NameServerController::class, 'store'])->name('name-servers.store');



    });










