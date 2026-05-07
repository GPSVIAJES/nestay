<?php

namespace App\Services\RateHawk;

use App\Models\Booking;
use App\Services\RateHawk\MockData\BookingFormResult;
use App\Services\RateHawk\MockData\BookingResult;
use App\Services\RateHawk\MockData\PrebookResult;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookingService
{
    public function __construct(protected RateHawkClient $client)
    {}

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 3 — Prebook
    // Endpoint: POST /api/b2b/v3/hotel/prebook/
    //
    // Receives an "h-..." book_hash (from hotelpage) and returns a "p-..." hash
    // that must be used in the booking/form step.
    // Never cache prebook responses.
    // ─────────────────────────────────────────────────────────────────────────

    public function prebook(string $bookHash, int $priceIncreasePct = 0): array
    {
        if (config('ratehawk.use_mock') || str_starts_with($bookHash, 'hash_') || str_starts_with($bookHash, 'p-hash_')) {
            Log::info('[RateHawk:MOCK] prebook fallback', ['book_hash' => $bookHash]);
            return PrebookResult::get($bookHash);
        }

        return $this->client->post('api/b2b/v3/hotel/prebook/', [
            'book_hash'             => $bookHash,
            'price_increase_percent'=> $priceIncreasePct,
            'user_ip'               => request()->ip() === '127.0.0.1' || request()->ip() === '::1' ? '181.50.236.13' : request()->ip(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 3.2 — SERP Prebook (Alternative)
    // Endpoint: POST /api/b2b/v3/serp/prebook/
    //
    // Used if bypassing hotel page and booking directly from search results.
    // ─────────────────────────────────────────────────────────────────────────
    public function serpPrebook(string $bookHash, int $priceIncreasePct = 0): array
    {
        if (config('ratehawk.use_mock') || str_starts_with($bookHash, 'hash_') || str_starts_with($bookHash, 'p-hash_')) {
            Log::info('[RateHawk:MOCK] serpPrebook fallback', ['book_hash' => $bookHash]);
            return PrebookResult::get($bookHash);
        }

        return $this->client->post('api/b2b/v3/serp/prebook/', [
            'book_hash'             => $bookHash,
            'price_increase_percent'=> $priceIncreasePct,
            'user_ip'               => request()->ip() === '127.0.0.1' || request()->ip() === '::1' ? '181.50.236.13' : request()->ip(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 4.1 — Create Booking Form
    // Endpoint: POST /api/b2b/v3/hotel/order/booking/form/
    //
    // Creates the booking on ETG's side and links it to our partner_order_id.
    // Must be retried up to 10 times on 5xx / timeout / unknown errors.
    // On success (status ok), proceed to booking/finish.
    //
    // Returns the ETG response including the partner_order_id echoed back.
    // ─────────────────────────────────────────────────────────────────────────

    public function createBookingForm(string $bookHash, string $partnerOrderId): array
    {
        if (config('ratehawk.use_mock') || str_starts_with($bookHash, 'hash_') || str_starts_with($bookHash, 'p-hash_')) {
            Log::info('[RateHawk:MOCK] createBookingForm fallback', [
                'book_hash'        => $bookHash,
                'partner_order_id' => $partnerOrderId,
            ]);
            return BookingFormResult::get($partnerOrderId);
        }

        $maxRetries  = 10;
        $lastResult  = [];

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            Log::info("[RateHawk] booking/form attempt {$attempt}", [
                'partner_order_id' => $partnerOrderId,
            ]);

            try {
                $result = $this->client->post('api/b2b/v3/hotel/order/booking/form/', [
                    'book_hash'        => $bookHash,
                    'partner_order_id' => $partnerOrderId,
                    'user_ip'          => request()->ip() === '127.0.0.1' || request()->ip() === '::1' ? '181.50.236.13' : request()->ip(),
                    'language'         => config('ratehawk.language', 'en'),
                ]);

                $lastResult = $result;
                $status     = $result['status'] ?? '';
                $error      = $result['error']  ?? '';

                // Unrecoverable errors — stop immediately
                if (in_array($error, [
                    'contract_mismatch',
                    'hotel_not_found',
                    'insufficient_b2b_balance',
                    'reservation_is_not_allowed',
                    'rate_not_found',
                    'sandbox_restriction',
                ])) {
                    Log::warning("[RateHawk] booking/form fatal error: {$error}");
                    return $result;
                }

                // Duplicate: generate a new partner_order_id and retry
                if (in_array($error, ['double_booking_form', 'duplicate_reservation'])) {
                    $partnerOrderId = $partnerOrderId . '_r' . $attempt;
                    Log::info("[RateHawk] booking/form duplicate — new partner_order_id: {$partnerOrderId}");
                    continue;
                }

                // Success
                if ($status === 'ok') {
                    return $result;
                }

                // Retryable: 5xx, timeout, unknown — loop again
                Log::warning("[RateHawk] booking/form retryable response", [
                    'status' => $status,
                    'error'  => $error,
                    'attempt'=> $attempt,
                ]);

            } catch (\RuntimeException $e) {
                Log::warning("[RateHawk] booking/form exception on attempt {$attempt}", [
                    'message' => $e->getMessage(),
                ]);
                $lastResult = ['status' => 'error', 'error' => 'connection_error'];
            }
        }

        // Exhausted all retries — signal that a new search is needed
        Log::error('[RateHawk] booking/form exhausted retries — restart search', [
            'partner_order_id' => $partnerOrderId,
        ]);

        return array_merge($lastResult, ['error' => 'max_retries_exceeded']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 4.2 — Start Booking Process (Finish)
    // Endpoint: POST /api/b2b/v3/hotel/order/booking/finish/
    //
    // Submits guest details and triggers the actual booking with suppliers.
    // The booking will be in "in progress" state after this call.
    // After this, poll /booking/finish/status/ until you get a final status.
    //
    // Saves the booking to DB with status = 'pending'.
    // ─────────────────────────────────────────────────────────────────────────

    public function startBooking(array $data, ?int $userId): array
    {
        $bookHash = $data['book_hash'] ?? '';
        if (config('ratehawk.use_mock') || str_starts_with($bookHash, 'hash_') || str_starts_with($bookHash, 'p-hash_')) {
            Log::info('[RateHawk:MOCK] startBooking fallback', [
                'partner_order_id' => $data['partner_order_id'] ?? '',
            ]);

            $result = BookingResult::startMock($data);

            if (in_array($result['status'] ?? '', ['ok', 'processing'])) {
                $this->saveBooking($result['data'], $data, $userId, 'pending');
            }

            return $result;
        }

        // Build the rooms/guests array
        // ETG expects: "rooms": [{"guests": [{"first_name": "...", "last_name": "..."}]}]
        $rooms = [];
        foreach (($data['rooms'] ?? []) as $room) {
            $roomGuests = [];
            foreach (($room['guests'] ?? []) as $guest) {
                $guestData = [
                    'first_name' => $guest['first_name'],
                    'last_name'  => $guest['last_name'],
                ];
                if (!empty($guest['is_child']) && isset($guest['age'])) {
                    $guestData['age'] = (int) $guest['age'];
                }
                $roomGuests[] = $guestData;
            }
            $rooms[] = ['guests' => $roomGuests];
        }

        // Fallback: build a single room from the legacy guest format
        if (empty($rooms)) {
            $rooms[] = ['guests' => [[
                'first_name' => $data['guest']['first_name'],
                'last_name'  => $data['guest']['last_name'],
            ]]];
        }

        $payload = [
            'partner'          => [
                'partner_order_id' => $data['partner_order_id'],
            ],
            'user'             => [
                // B2B: always use a fixed corporate email for confirmation emails
                'email' => config('ratehawk.corporate_email', $data['guest']['email']),
                'phone' => $data['guest']['phone'] ?? '',
            ],
            'rooms'            => $rooms,
            'payment_type'     => [
                'type'          => 'deposit',
                'currency_code' => $data['currency'] ?? 'USD',
                'amount'        => (string) ($data['total_price'] ?? '0'),
            ],
            'user_ip'          => request()->ip() === '127.0.0.1' || request()->ip() === '::1' ? '181.50.236.13' : request()->ip(),
            'language'         => config('ratehawk.language', 'en'),
        ];

        $result = $this->client->post('api/b2b/v3/hotel/order/booking/finish/', $payload);

        // Save to DB immediately regardless of final status
        if (!empty($data['partner_order_id'])) {
            $this->saveBooking($result['data'] ?? [], $data, $userId, 'pending');
        }

        return $result;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 4.3 — Check Booking Status
    // Endpoint: POST /api/b2b/v3/hotel/order/booking/finish/status/
    //
    // Poll this endpoint every ~2s until status is 'ok' (confirmed)
    // or a final failure error is received.
    // ─────────────────────────────────────────────────────────────────────────

    public function pollBookingStatus(string $partnerOrderId): array
    {
        $booking = Booking::where('partner_order_id', $partnerOrderId)->first();
        $isMock = $booking && (str_starts_with((string)$booking->book_hash, 'hash_') || str_starts_with((string)$booking->book_hash, 'p-hash_'));

        if (config('ratehawk.use_mock') || $isMock) {
            Log::info('[RateHawk:MOCK] pollBookingStatus fallback', ['partner_order_id' => $partnerOrderId]);
            return BookingResult::pollMock($partnerOrderId);
        }

        return $this->client->post('api/b2b/v3/hotel/order/booking/finish/status/', [
            'partner_order_id' => $partnerOrderId,
            'partner' => [
                'partner_order_id' => $partnerOrderId,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 5.1 — Retrieve Order Info (post-booking only)
    // Endpoint: POST /api/b2b/v3/hotel/order/info/
    //
    // Do NOT use this for checking booking status — use pollBookingStatus().
    // Wait some time after confirmation before calling this.
    // ─────────────────────────────────────────────────────────────────────────

    public function retrieveOrderInfo(string $partnerOrderId): array
    {
        if (config('ratehawk.use_mock')) {
            return ['status' => 'ok', 'data' => []];
        }

        return $this->client->post('api/b2b/v3/hotel/order/info/', [
            'partner_order_id' => $partnerOrderId,
            'partner' => [
                'partner_order_id' => $partnerOrderId,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 5.2 — Cancel Booking
    // Endpoint: POST /api/b2b/v3/hotel/order/cancel/
    //
    // If timeout error is received, retry the cancel call once.
    // ─────────────────────────────────────────────────────────────────────────

    public function cancelBookingViaApi(Booking $booking): array
    {
        $isMock = str_starts_with((string)$booking->book_hash, 'hash_') || str_starts_with((string)$booking->book_hash, 'p-hash_');

        if (config('ratehawk.use_mock') || $isMock) {
            Log::info('[RateHawk:MOCK] cancelBooking fallback', [
                'partner_order_id' => $booking->partner_order_id,
            ]);
            return ['status' => 'ok', 'data' => [
                'partner_order_id' => $booking->partner_order_id,
                'status'           => 'cancelled',
            ]];
        }

        $result = $this->client->post('api/b2b/v3/hotel/order/cancel/', [
            'partner_order_id' => $booking->partner_order_id,
            'partner' => [
                'partner_order_id' => $booking->partner_order_id,
            ],
        ]);

        // ETG recommends retrying once on timeout
        if (($result['error'] ?? '') === 'timeout') {
            Log::warning('[RateHawk] cancel timeout — retrying once');
            $result = $this->client->post('api/b2b/v3/hotel/order/cancel/', [
                'partner_order_id' => $booking->partner_order_id,
                'partner' => [
                    'partner_order_id' => $booking->partner_order_id,
                ],
            ]);
        }

        return $result;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper — Generate a unique partner_order_id
    // Format: nestay-{userId}-{timestamp}-{random}
    // ─────────────────────────────────────────────────────────────────────────

    public function generatePartnerOrderId(?int $userId): string
    {
        return (string) Str::uuid();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Internal — Save booking to local DB
    // ─────────────────────────────────────────────────────────────────────────

    protected function saveBooking(array $apiData, array $inputData, ?int $userId, string $initialStatus = 'pending'): void
    {
        try {
            Booking::create([
                'user_id'             => $userId,
                'partner_order_id'    => $inputData['partner_order_id'] ?? null,
                'ratehawk_order_id'   => $apiData['order_id']           ?? null,
                'book_hash'           => $inputData['book_hash']         ?? null,
                'hotel_id'            => $apiData['hotel']['id']         ?? $inputData['hotel_id']   ?? '',
                'hotel_name'          => $apiData['hotel']['name']       ?? $inputData['hotel_name'] ?? '',
                'hotel_address'       => $apiData['hotel']['address']    ?? '',
                'hotel_city'          => $inputData['hotel_city']        ?? '',
                'hotel_country'       => $inputData['hotel_country']     ?? '',
                'hotel_stars'         => $apiData['hotel']['stars']      ?? '',
                'hotel_image'         => $inputData['hotel_image']       ?? '',
                'check_in'            => $apiData['rate']['check_in']    ?? $inputData['check_in'],
                'check_out'           => $apiData['rate']['check_out']   ?? $inputData['check_out'],
                'guests'              => $inputData['guests']            ?? 1,
                'children'            => $inputData['children']          ?? 0,
                'rooms'               => count($inputData['rooms'] ?? []) ?: ($inputData['rooms_count'] ?? 1),
                'rooms_data'          => json_encode([
                    'api_rate'     => $apiData['rate'] ?? [],
                    'rooms_config' => $inputData['rooms_config'] ?? null,
                    'rooms_detail' => $inputData['rooms'] ?? []
                ]),
                'total_price'         => $apiData['rate']['total_price'] ?? $inputData['total_price'] ?? 0,
                'currency'            => $apiData['rate']['currency']    ?? $inputData['currency']    ?? 'USD',
                'guest_first_name'    => $inputData['guest']['first_name'],
                'guest_last_name'     => $inputData['guest']['last_name'],
                'guest_email'         => $inputData['guest']['email'],
                'guest_phone'         => $inputData['guest']['phone']       ?? '',
                'status'              => $initialStatus,
                'cancellation_policy' => $inputData['cancellation_policy']  ?? '',
            ]);

            Log::info('[RateHawk] Booking saved to DB', [
                'partner_order_id' => $inputData['partner_order_id'] ?? null,
                'status'           => $initialStatus,
            ]);
        } catch (\Exception $e) {
            Log::error('[RateHawk] Failed to save booking', ['error' => $e->getMessage()]);
        }
    }
}
