<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EggProductionController;
use App\Http\Controllers\EggSaleController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FlockRecordController;
use App\Http\Controllers\FarmSettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FarmUserController;


/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});


/*
|--------------------------------------------------------------------------
| Authenticated Farm Application
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/egg-production', [EggProductionController::class, 'create']);

    Route::post('/egg-production', [EggProductionController::class, 'store']);

    Route::get('/egg-sales', [EggSaleController::class, 'create']);

    Route::post('/egg-sales', [EggSaleController::class, 'store']);

    Route::get('/expenses', [ExpenseController::class, 'create']);

    Route::post('/expenses', [ExpenseController::class, 'store']);

    Route::get('/reports', [ReportController::class, 'index']);

    Route::get('/flock-records', [FlockRecordController::class, 'create']);

    Route::post('/flock-records', [FlockRecordController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | Farm Settings
    |--------------------------------------------------------------------------
    */

    Route::get('/farm-settings', [FarmSettingController::class, 'edit']);

    Route::put('/farm-settings', [FarmSettingController::class, 'update']);


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | Farm Users
    |--------------------------------------------------------------------------
    */

    Route::get('/farm-settings/users', [
        FarmUserController::class,
        'index',
    ])->name('farm-settings.users.index');

    Route::get('/farm-settings/users/create', [
        FarmUserController::class,
        'create',
    ])->name('farm-settings.users.create');

    Route::post('/farm-settings/users', [
        FarmUserController::class,
        'store',
    ])->name('farm-settings.users.store');

    Route::get('/farm-settings/users/{id}/edit', [
        FarmUserController::class,
        'edit',
    ])->name('farm-settings.users.edit');

    Route::put('/farm-settings/users/{id}', [
        FarmUserController::class,
        'update',
    ])->name('farm-settings.users.update');

    Route::put('/farm-settings/users/{id}/toggle-status', [
        FarmUserController::class,
        'toggleStatus',
    ])->name('farm-settings.users.toggle-status');

});


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
