<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Room\RoomController;
use App\Http\Controllers\Tamagotchi\TamagotchiController;
use Illuminate\Http\Request;
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

Route::prefix('/auth')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/signup', [SignupController::class, 'signup']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('/tamagotchi')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [TamagotchiController::class, 'getTamagotchi']);
        Route::post('/', [TamagotchiController::class, 'createTamagotchi']);
        Route::delete('/', [TamagotchiController::class, 'deleteTamagotchi']);
    });

    Route::prefix('/room')->group(function () {
        Route::middleware(['isAdmin'])->group(function () {
            Route::post('/', [RoomController::class, 'createRoom']);
            Route::put('/', [RoomController::class, 'updateRoom']);
            Route::delete('/', [RoomController::class, 'deleteRoom']);
        });
        Route::get('/', [RoomController::class, 'getRoom']);
    });
    Route::prefix('/book')->group(function () {
        Route::post('/', [BookingController::class, 'createBooking']);
    });
});
