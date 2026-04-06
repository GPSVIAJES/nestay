<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Main dashboard — overview with upcoming bookings.
     */
    public function index()
    {
        $user            = Auth::user();
        $upcomingCount   = Booking::where('user_id', $user->id)->upcoming()->count();
        $totalBookings   = Booking::where('user_id', $user->id)->count();
        $upcomingBookings = Booking::where('user_id', $user->id)->upcoming()->take(3)->get();

        return view('dashboard.index', compact('user', 'upcomingCount', 'totalBookings', 'upcomingBookings'));
    }

    /**
     * All bookings page.
     */
    public function bookings()
    {
        $user     = Auth::user();
        $upcoming = Booking::where('user_id', $user->id)->upcoming()->paginate(5, ['*'], 'upcoming_page');
        $past     = Booking::where('user_id', $user->id)->past()->paginate(5, ['*'], 'past_page');

        return view('dashboard.bookings', compact('upcoming', 'past'));
    }
}
