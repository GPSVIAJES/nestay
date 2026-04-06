<?php

use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\BookingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| RateHawk API Routes
|--------------------------------------------------------------------------
| These routes act as a secure proxy between the frontend and RateHawk API.
| Never expose RateHawk credentials to the browser — all calls go through here.
*/

// ─── Public endpoints (no auth required) ─────────────────────────────────────
Route::post('/suggest',       [SearchController::class, 'suggest']);
Route::post('/search-hotels', [SearchController::class, 'search']);
Route::post('/hotel-details', [SearchController::class, 'details']);

// ─── Protected endpoints (auth required) ─────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/prebook',                  [BookingController::class, 'prebook']);
    Route::post('/book',                     [BookingController::class, 'book']);
    Route::get('/booking-status/{id}',       [BookingController::class, 'status']);
    Route::get('/my-bookings',               [BookingController::class, 'myBookings']);
    Route::delete('/bookings/{id}/cancel',   [BookingController::class, 'cancel']);
});
