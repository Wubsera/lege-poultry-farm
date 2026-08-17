<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
|
| Routes available before login.
|
*/

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    */

    Route::get('register', [
        RegisteredUserController::class,
        'create',
    ])->name('register');

    Route::post('register', [
        RegisteredUserController::class,
        'store',
    ]);


    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    Route::get('login', [
        AuthenticatedSessionController::class,
        'create',
    ])->name('login');

    Route::post('login', [
        AuthenticatedSessionController::class,
        'store',
    ]);

});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
|
| Routes available after login.
|
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Change PIN
    |--------------------------------------------------------------------------
    */

    Route::put(
        'password',
        [PasswordController::class, 'update']
    )->name('password.update');


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    Route::post(
        'logout',
        [AuthenticatedSessionController::class, 'destroy']
    )->name('logout');

});
