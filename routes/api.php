<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth.api')->group(function() {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/calendars', [UserController::class, 'calendars']);
    });

    Route::prefix('calendar')->group(function () {
        Route::get('/{id}/share', [CalendarController::class, 'share']);
        Route::middleware('auth')->post('/{id}/event', [CalendarController::class, 'createEvent']);
        Route::middleware('auth')->get('/{id}/events', [CalendarController::class, 'getEvents']);
    });
});
