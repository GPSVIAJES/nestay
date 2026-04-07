<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\BookingPageController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// ─── Public pages ─────────────────────────────────────────────────────────────
Route::get('/',             [HotelController::class, 'home'])->name('home');
Route::get('/search',       [HotelController::class, 'results'])->name('search');
Route::get('/hotel/{id}',   [HotelController::class, 'show'])->name('hotel.show');

// ─── Booking flow (auth required) ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/booking',              [BookingPageController::class, 'form'])->name('booking.form');
    Route::get('/booking/{id}/confirm', [BookingPageController::class, 'confirm'])->name('booking.confirm');
    // /booking/{id}/voucher — will be added once ETG voucher API is integrated
});

// ─── Dashboard & Profile (auth required) ──────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard',             [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/bookings',    [DashboardController::class, 'bookings'])->name('dashboard.bookings');

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
