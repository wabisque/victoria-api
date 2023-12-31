<?php

use App\Http\Controllers\AspirantController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ConstituencyController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RoleController;
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

Route::controller(AuthenticationController::class)->prefix('authentication')->name('authentication.')->group(
    function() {
        Route::post('login', 'login')->name('login');
        Route::post('register', 'register')->name('register');
        
        Route::middleware('auth:sanctum')->group(
            function() {
                Route::get('user', 'user')->name('user');
                Route::put('update-details', 'updateDetails')->name('updateDetails');
                Route::put('update-password', 'updatePassword')->name('updatePassword');
                Route::post('logout', 'logout')->name('logout');
            }
        );
    }
);

Route::controller(RoleController::class)->middleware('auth:sanctum', 'role:Administrator')->prefix('roles')->name('roles.')->group(
    function() {
        Route::get('', 'index')->name('index');
        Route::get('{role}', 'get')->name('get');
    }
);

Route::controller(RegionController::class)->prefix('regions')->name('regions.')->group(
    function() {
        Route::get('', 'index')->name('index');
        Route::get('{region}', 'get')->name('get');

        Route::middleware('auth:sanctum', 'role:Administrator')->group(
            function() {
                Route::post('', 'create')->name('create');
                Route::put('{region}', 'update')->name('update');
                Route::delete('{region}', 'delete')->name('delete');
            }
        );
    }
);

Route::controller(ConstituencyController::class)->prefix('constituencies')->name('constituencies.')->group(
    function() {
        Route::get('', 'index')->name('index');
        Route::get('{constituency}', 'get')->name('get');

        Route::middleware('auth:sanctum', 'role:Administrator')->group(
            function() {
                Route::post('', 'create')->name('create');
                Route::put('{constituency}', 'update')->name('update');
                Route::delete('{constituency}', 'delete')->name('delete');
            }
        );
    }
);

Route::controller(PositionController::class)->prefix('positions')->name('positions.')->group(
    function() {
        Route::get('', 'index')->name('index');
        Route::get('{position}', 'get')->name('get');

        Route::middleware('auth:sanctum', 'role:Administrator')->group(
            function() {
                Route::post('', 'create')->name('create');
                Route::put('{position}', 'update')->name('update');
                Route::delete('{position}', 'delete')->name('delete');
            }
        );
    }
);

Route::controller(PartyController::class)->prefix('parties')->name('parties.')->group(
    function() {
        Route::get('', 'index')->name('index');
        Route::get('{party}', 'get')->name('get');

        Route::middleware('auth:sanctum', 'role:Administrator')->group(
            function() {
                Route::post('', 'create')->name('create');
                Route::put('{party}', 'update')->name('update');
                Route::delete('{party}', 'delete')->name('delete');
            }
        );
    }
);

Route::controller(AspirantController::class)->middleware('auth:sanctum')->prefix('aspirants')->name('aspirants.')->group(
    function() {
        Route::middleware('role:Administrator')->group(
            function() {
                Route::prefix('creation-requests')->group(
                    function() {
                        Route::get('', 'indexCreationRequest')->name('indexCreationRequest');
                        Route::get('{aspirant_creation_request}', 'getCreationRequest')->name('getCreationRequest');
                        Route::put('{aspirant_creation_request}', 'confirmCreation')->name('confirmCreation');
                    }
                );

                Route::prefix('update-requests')->group(
                    function() {
                        Route::get('', 'indexUpdateRequest')->name('indexUpdateRequest');
                        Route::get('{aspirant_update_request}', 'getUpdateRequest')->name('getUpdateRequest');
                        Route::put('{aspirant_update_request}', 'confirmUpdate')->name('confirmUpdate');
                    }
                );

                Route::delete('{aspirant}', 'delete')->name('delete');
            }
        );

        Route::middleware('role:Aspirant,Follower')->group(
            function() {
                Route::post('{aspirant}/follow', 'follow')->name('follow');
                Route::post('{aspirant}/unfollow', 'unfollow')->name('unfollow');
            }
        );

        Route::put('', 'update')->middleware('role:Aspirant')->name('update');
        
        Route::post('', 'create')->middleware('role:Follower')->name('create');

        Route::get('', 'index')->name('index');
        Route::get('{aspirant}', 'get')->name('get');
    }
);

Route::controller(PostController::class)->middleware('auth:sanctum')->prefix('posts')->name('posts.')->group(
    function() {
        Route::middleware('role:Aspirant')->group(
            function() {
                Route::post('', 'create')->name('create');
                Route::put('{post}', 'update')->name('update');
                Route::delete('{post}', 'delete')->name('delete');
            }
        );

        Route::get('', 'index')->name('index');
        Route::get('{post}', 'get')->name('get');
    }
);
