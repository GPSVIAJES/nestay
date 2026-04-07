<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingPageController extends Controller
{
    /**
     * Booking form page.
     * Data is passed via query params from the hotel detail page.
     */
    public function form(Request $request)
    {
        $bookingData = $request->only([
            'book_hash', 'hotel_id', 'hotel_name', 'hotel_address',
            'hotel_stars', 'hotel_image', 'check_in', 'check_out',
            'guests', 'room_name', 'total_price', 'currency',
            'meal_label', 'refundable', 'cancellation_policy',
        ]);

        if (empty($bookingData['book_hash'])) {
            return redirect()->route('home')->with('error', 'Sesión de booking expirada. Busca de nuevo.');
        }

        return view('pages.booking', compact('bookingData'));
    }

    /**
     * Booking confirmation page.
     * Shown after the async polling confirms the booking is 'confirmed'.
     */
    public function confirm(Request $request, string $id)
    {
        $booking = Booking::where('ratehawk_order_id', $id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        return view('pages.booking-confirm', compact('booking'));
    }

    // voucher() removed — will be re-added once the ETG Retrieve Voucher API endpoint
    // (api/b2b/v3/hotel/order/voucher/) is integrated in a future release.
}
