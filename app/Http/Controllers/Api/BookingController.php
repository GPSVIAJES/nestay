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

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 3 — Prebook
    // POST /api/prebook
    //
    // Input:  book_hash (h-... from hotelpage), optional price_increase_percent
    // Output: new book_hash (p-...) + rate details + price_changed flag
    //
    // If price_changed = true, the frontend MUST inform the user about the
    // new price before proceeding to booking.
    // ─────────────────────────────────────────────────────────────────────────

    public function prebook(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_hash'              => 'required|string',
            'price_increase_percent' => 'nullable|integer|min:0|max:100',
        ]);

        $result = $this->bookingService->prebook(
            $validated['book_hash'],
            $validated['price_increase_percent'] ?? 0
        );

        return response()->json($result);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 4.1 — Create Booking Form
    // POST /api/booking-form
    //
    // Input:  book_hash (p-... from prebook)
    // Output: confirmation that ETG linked the order to our partner_order_id
    //
    // This step generates a partner_order_id and links it to the ETG booking.
    // Returns the partner_order_id so the frontend can use it in the next step.
    //
    // Retried automatically up to 10 times inside BookingService.
    // ─────────────────────────────────────────────────────────────────────────

    public function createBookingForm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_hash' => 'required|string',
        ]);

        $partnerOrderId = $this->bookingService->generatePartnerOrderId(Auth::id());

        $result = $this->bookingService->createBookingForm(
            $validated['book_hash'],
            $partnerOrderId
        );

        if (($result['status'] ?? '') !== 'ok') {
            $error = $result['error'] ?? 'unknown';

            // max_retries_exceeded means we couldn't get a response — restart search
            if ($error === 'max_retries_exceeded') {
                return response()->json([
                    'status'  => 'error',
                    'error'   => 'max_retries_exceeded',
                    'message' => 'No se pudo crear la reserva tras varios intentos. Por favor, busca de nuevo.',
                ], 503);
            }

            return response()->json([
                'status'  => 'error',
                'error'   => $error,
                'message' => 'No se pudo inicializar la reserva. Por favor, inténtalo de nuevo.',
            ], 422);
        }

        return response()->json([
            'status'           => 'ok',
            'partner_order_id' => $partnerOrderId,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 4.2 — Start Booking Process
    // POST /api/book
    //
    // Input:  partner_order_id (from createBookingForm) + guest details
    // Output: booking initiated — frontend must poll /api/booking-status/{id}
    //
    // Booking is saved to local DB with status = 'pending'.
    // ─────────────────────────────────────────────────────────────────────────

    public function book(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'partner_order_id'    => 'required|string',
            'book_hash'           => 'required|string',
            'hotel_id'            => 'required|string',
            'hotel_name'          => 'required|string',
            'hotel_city'          => 'nullable|string',
            'hotel_country'       => 'nullable|string',
            'hotel_image'         => 'nullable|url',
            'check_in'            => 'required|date',
            'check_out'           => 'required|date|after:check_in',
            'guests'              => 'required|integer|min:1',
            'children'            => 'nullable|integer|min:0',
            'currency'            => 'nullable|string|size:3',
            'total_price'         => 'required|numeric|min:0',
            'cancellation_policy' => 'nullable|string',
            // Lead guest (for DB snapshot)
            'guest.first_name'    => 'required|string|max:100',
            'guest.last_name'     => 'required|string|max:100',
            'guest.email'         => 'required|email',
            'guest.phone'         => 'nullable|string|max:30',
            // Rooms with per-room guests (for ETG API payload)
            'rooms'               => 'nullable|array|max:9',
            'rooms.*.guests'      => 'nullable|array',
            'rooms.*.guests.*.first_name' => 'required_with:rooms|string',
            'rooms.*.guests.*.last_name'  => 'required_with:rooms|string',
        ]);

        $result = $this->bookingService->startBooking($validated, Auth::id());

        $status = $result['status'] ?? '';
        $error  = $result['error']  ?? '';

        // booking_form_expired and rate_not_found are fatal for this step
        if (in_array($error, ['booking_form_expired', 'rate_not_found', 'return_path_required'])) {
            return response()->json([
                'status'  => 'error',
                'error'   => $error,
                'message' => 'La reserva no pudo iniciarse: ' . $error . '. Por favor, busca de nuevo.',
            ], 422);
        }

        // For timeout, unknown, 5xx — ETG says proceed to poll /finish/status/
        // so we return ok and let the frontend poll
        if (!in_array($status, ['ok', 'processing']) && empty($error)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se pudo iniciar la reserva. Por favor, inténtalo de nuevo.',
            ], 422);
        }

        return response()->json([
            'status'           => 'ok',
            'partner_order_id' => $validated['partner_order_id'],
            'message'          => 'Reserva iniciada. Verificando disponibilidad...',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 4.3 — Poll Booking Status
    // GET /api/booking-status/{partnerOrderId}
    //
    // Poll every ~2 seconds until booking_status is 'confirmed' or 'failed'.
    // Final failure errors: soldout, book_limit, provider, not_allowed,
    //   booking_finish_did_not_succeed, block, charge
    // Retryable (keep polling): processing, timeout, unknown, 5xx
    // ─────────────────────────────────────────────────────────────────────────

    public function status(Request $request, string $partnerOrderId): JsonResponse
    {
        $query = Booking::where('partner_order_id', $partnerOrderId);
        
        if (Auth::check()) {
            $query->where(function($q) {
                $q->where('user_id', Auth::id())
                  ->orWhereNull('user_id');
            });
        } else {
            $query->whereNull('user_id');
        }

        $booking = $query->first();

        if (!$booking) {
            return response()->json(['status' => 'error', 'message' => 'Reserva no encontrada'], 404);
        }

        // Already in a terminal state — return from DB without hitting ETG again
        if (in_array($booking->status, ['confirmed', 'cancelled', 'failed'])) {
            return response()->json([
                'status'         => 'ok',
                'booking_status' => $booking->status,
                'partner_order_id' => $partnerOrderId,
            ]);
        }

        // Still pending — poll ETG
        $apiResult = $this->bookingService->pollBookingStatus($partnerOrderId);
        
        Log::info("[RateHawk] Poll result for {$partnerOrderId}", [
            'api_status' => $apiResult['data']['status'] ?? 'N/A',
            'api_error'  => $apiResult['error'] ?? 'none'
        ]);

        $apiStatus = $apiResult['data']['status'] ?? '';
        $apiError  = $apiResult['error']          ?? '';

        // Terminal success
        if ($apiStatus === 'ok' || $apiStatus === 'confirmed') {
            $booking->update(['status' => 'confirmed']);
        }

        // Terminal failures — stop polling
        $terminalErrors = ['soldout', 'book_limit', 'provider', 'not_allowed',
                           'booking_finish_did_not_succeed', 'block', 'charge', '3ds'];

        if (in_array($apiError, $terminalErrors) || in_array($apiStatus, ['failed', 'cancelled'])) {
            $booking->update(['status' => 'failed']);
        }

        // For: processing, timeout, unknown, 5xx — keep status as 'pending',
        // frontend continues polling.

        return response()->json([
            'status'           => 'ok',
            'booking_status'   => $booking->fresh()->status,
            'partner_order_id' => $partnerOrderId,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/my-bookings
    // ─────────────────────────────────────────────────────────────────────────

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

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE /api/bookings/{id}/cancel
    // ─────────────────────────────────────────────────────────────────────────

    public function cancel(Request $request, int $id): JsonResponse
    {
        $booking = Booking::where('id', $id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        if ($booking->status === 'cancelled') {
            return response()->json(['status' => 'error', 'message' => 'La reserva ya está cancelada'], 422);
        }

        if ($booking->status === 'pending') {
            return response()->json([
                'status'  => 'error',
                'message' => 'No se puede cancelar una reserva que aún está siendo procesada',
            ], 422);
        }

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
