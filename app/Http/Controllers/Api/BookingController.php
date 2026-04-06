<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\RateHawk\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService)
    {}

    /**
     * Validate rate before booking (prebook step).
     * POST /api/prebook
     */
    public function prebook(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_hash' => 'required|string',
        ]);

        $result = $this->bookingService->prebook($validated['book_hash']);

        return response()->json($result);
    }

    /**
     * Start booking process.
     * POST /api/book
     */
    public function book(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_hash'           => 'required|string',
            'hotel_id'            => 'required|string',
            'hotel_name'          => 'required|string',
            'hotel_city'          => 'nullable|string',
            'hotel_country'       => 'nullable|string',
            'hotel_image'         => 'nullable|url',
            'check_in'            => 'required|date',
            'check_out'           => 'required|date|after:check_in',
            'guests'              => 'required|integer|min:1',
            'total_price'         => 'required|numeric|min:0',
            'cancellation_policy' => 'nullable|string',
            'guest.first_name'    => 'required|string|max:100',
            'guest.last_name'     => 'required|string|max:100',
            'guest.email'         => 'required|email',
            'guest.phone'         => 'nullable|string|max:30',
        ]);

        $result = $this->bookingService->startBooking($validated, Auth::id());

        if (($result['status'] ?? '') !== 'ok') {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se pudo completar la reserva. Por favor, intenta de nuevo.',
            ], 422);
        }

        return response()->json($result);
    }

    /**
     * Check booking status by RateHawk order ID.
     * GET /api/booking-status/{id}
     */
    public function status(Request $request, string $id): JsonResponse
    {
        // Also check local DB first
        $booking = Booking::where('ratehawk_order_id', $id)
                          ->where('user_id', Auth::id())
                          ->first();

        if (!$booking) {
            return response()->json(['status' => 'error', 'message' => 'Reserva no encontrada'], 404);
        }

        // Optionally sync with RateHawk if pending
        if ($booking->status === 'pending') {
            $apiStatus = $this->bookingService->getBookingStatus($id);
            if (($apiStatus['data']['status'] ?? '') === 'confirmed') {
                $booking->update(['status' => 'confirmed']);
            }
        }

        return response()->json([
            'status' => 'ok',
            'data'   => $booking->fresh(),
        ]);
    }

    /**
     * Get authenticated user's bookings.
     * GET /api/my-bookings
     */
    public function myBookings(Request $request): JsonResponse
    {
        $tab      = $request->input('tab', 'upcoming'); // upcoming | past | all
        $perPage  = min($request->input('per_page', 10), 50);

        $query = Booking::where('user_id', Auth::id());

        $bookings = match($tab) {
            'upcoming' => $query->upcoming()->paginate($perPage),
            'past'     => $query->past()->paginate($perPage),
            default    => $query->orderByDesc('created_at')->paginate($perPage),
        };

        return response()->json([
            'status' => 'ok',
            'data'   => $bookings,
        ]);
    }

    /**
     * Cancel a booking.
     * DELETE /api/bookings/{id}/cancel
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $booking = Booking::where('id', $id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        if ($booking->status === 'cancelled') {
            return response()->json(['status' => 'error', 'message' => 'La reserva ya está cancelada'], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'status'  => 'ok',
            'message' => 'Reserva cancelada correctamente.',
        ]);
    }
}
