<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthenticationController::class)->prefix('authentication')->name('authentication.')->group(function() {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register')->name('register');
    
    Route::middleware('auth:sanctum')->group(function() {
        Route::post('logout', 'logout')->name('logout');
        Route::get('user', 'user')->name('user');
    });
});
