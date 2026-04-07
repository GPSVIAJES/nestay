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
     * START the async booking process (step 1 of 2).
     * Saves to DB with status = 'pending' and returns the order_id.
     * The frontend must poll /api/booking-status/{id} to get the confirmation.
     *
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

        // ETG returns either 'ok' (with order_id in processing state) or an error
        if (empty($result['data']['order_id'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se pudo iniciar la reserva. Por favor, inténtalo de nuevo.',
            ], 422);
        }

        // Return order_id immediately — frontend will poll for status
        return response()->json([
            'status'   => 'ok',
            'order_id' => $result['data']['order_id'],
            'message'  => 'Reserva iniciada. Verificando disponibilidad...',
        ]);
    }

    /**
     * POLL booking status (step 2 of 2).
     * Frontend calls this every few seconds until status is 'confirmed' or 'failed'.
     * Also syncs local DB when confirmed.
     *
     * GET /api/booking-status/{id}
     */
    public function status(Request $request, string $id): JsonResponse
    {
        $booking = Booking::where('ratehawk_order_id', $id)
                          ->where('user_id', Auth::id())
                          ->first();

        if (!$booking) {
            return response()->json(['status' => 'error', 'message' => 'Reserva no encontrada'], 404);
        }

        // If already confirmed or cancelled, return immediately from DB
        if (in_array($booking->status, ['confirmed', 'cancelled', 'failed'])) {
            return response()->json([
                'status'         => 'ok',
                'booking_status' => $booking->status,
                'order_id'       => $booking->ratehawk_order_id,
            ]);
        }

        // Still pending — poll ETG API for the real status
        $apiResult = $this->bookingService->pollBookingStatus($id);
        $apiStatus = $apiResult['data']['status'] ?? 'processing';

        if ($apiStatus === 'confirmed') {
            $booking->update(['status' => 'confirmed']);
        } elseif ($apiStatus === 'failed' || $apiStatus === 'cancelled') {
            $booking->update(['status' => 'failed']);
        }
        // else still processing — keep 'pending' in DB, frontend polls again

        return response()->json([
            'status'         => 'ok',
            'booking_status' => $booking->fresh()->status,
            'order_id'       => $booking->ratehawk_order_id,
        ]);
    }

    /**
     * Get authenticated user's bookings.
     * GET /api/my-bookings
     */
    public function myBookings(Request $request): JsonResponse
    {
        $tab     = $request->input('tab', 'upcoming'); // upcoming | past | all
        $perPage = min($request->input('per_page', 10), 50);

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
     * Cancel a booking — calls ETG API first, then updates local DB.
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

        if ($booking->status === 'pending') {
            return response()->json(['status' => 'error', 'message' => 'No se puede cancelar una reserva que aún está siendo procesada'], 422);
        }

        // Call ETG API to cancel on their side first
        $apiResult = $this->bookingService->cancelBookingViaApi($booking);

        if (($apiResult['status'] ?? '') !== 'ok') {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se pudo cancelar la reserva en el sistema. Contacta soporte.',
            ], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'status'  => 'ok',
            'message' => 'Reserva cancelada correctamente.',
        ]);
    }
}
