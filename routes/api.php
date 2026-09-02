<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EggProductionController;
use App\Http\Controllers\Api\EggSaleController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\FarmSettingController;
use App\Http\Controllers\Api\FlockRecordController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\FarmUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Test
|--------------------------------------------------------------------------
*/

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'Dorofarm API is working',
    ]);
});

Route::get('/auth-debug', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'authorization' => $request->header('Authorization'),
        'bearer_token' => $request->bearerToken(),
        'auth_check' => auth()->check(),
        'user_id' => auth()->id(),
    ]);
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::post('/login', [
    AuthController::class,
    'login',
]);

/*
|--------------------------------------------------------------------------
| Authenticated API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [
        AuthController::class,
        'logout',
    ]);

    Route::get('/user', [
        AuthController::class,
        'user',
    ]);

    Route::put('/user/profile', [
        AuthController::class,
        'updateProfile',
    ]);

    Route::put('/user/password', [
        AuthController::class,
        'changePassword',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Egg Production
    |--------------------------------------------------------------------------
    */

    Route::get('/egg-production', [
        EggProductionController::class,
        'index',
    ]);

    Route::post('/egg-production', [
        EggProductionController::class,
        'store',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Egg Sales
    |--------------------------------------------------------------------------
    */

    Route::get('/egg-sales', [
        EggSaleController::class,
        'index',
    ]);

    Route::post('/egg-sales', [
        EggSaleController::class,
        'store',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Expenses
    |--------------------------------------------------------------------------
    */

    Route::get('/expenses', [
        ExpenseController::class,
        'index',
    ]);

    Route::post('/expenses', [
        ExpenseController::class,
        'store',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Flock Records
    |--------------------------------------------------------------------------
    */

    Route::get('/flock-records', [
        FlockRecordController::class,
        'index',
    ]);

    Route::post('/flock-records', [
        FlockRecordController::class,
        'store',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Farm Users
    |--------------------------------------------------------------------------
    */

    Route::get('/farm/users', [
        FarmUserController::class,
        'index',
    ]);

    Route::post('/farm/users', [
        FarmUserController::class,
        'store',
    ]);

    Route::put('/farm/users/{id}', [
        FarmUserController::class,
        'update',
    ]);

    Route::put('/farm/users/{id}/toggle-status', [
        FarmUserController::class,
        'toggleStatus',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Farm Settings
    |--------------------------------------------------------------------------
    */

    Route::get('/farm-settings', [
        FarmSettingController::class,
        'show',
    ]);

    Route::put('/farm-settings', [
        FarmSettingController::class,
        'update',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    Route::get('/reports', [
        ReportController::class,
        'index',
    ]);
});
