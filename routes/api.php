<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
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

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('tours', [TourController::class, 'index']);
Route::get('tour_search', [TourController::class, 'searchTours']);
Route::get('tours/{tour}', [TourController::class, 'show']);

Route::middleware('auth:sanctum')-> group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout',[AuthController::class, 'logout']);

    Route::group(['middleware' => ['admin']], function () {
        Route::post('tour_create', [TourController::class, 'store']);
        Route::delete('tours/{tour}', [TourController::class, 'delete']);
        Route::put('tours/{tour}', [TourController::class, 'update']);
        Route::get('all_bookings', [BookingController::class, 'all_index']);
        Route::put('bookings/{booking}/status', [BookingController::class, 'updateStatus']);
    });

    Route::get('bookings', [BookingController::class, 'index']);
    Route::post('booking_create', [BookingController::class, 'store']);
    Route::delete('bookings/{booking}', [BookingController::class, 'delete']);
});
