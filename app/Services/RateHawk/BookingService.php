<?php

namespace App\Services\RateHawk;

use App\Models\Booking;
use App\Services\RateHawk\MockData\PrebookResult;
use App\Services\RateHawk\MockData\BookingResult;
use Illuminate\Support\Facades\Log;

class BookingService
{
    public function __construct(protected RateHawkClient $client)
    {}

    /**
     * Validate a rate before booking (prebook step).
     * Always fetches real-time price — never cached.
     */
    public function prebook(string $bookHash): array
    {
        if (config('ratehawk.use_mock')) {
            Log::info('[RateHawk:MOCK] prebook', ['book_hash' => $bookHash]);
            return PrebookResult::get($bookHash);
        }

        // Real API — never cache prebook responses
        return $this->client->post('api/b2b/v3/search/prebook/', [
            'book_hash' => $bookHash,
        ]);
    }

    /**
     * START the booking process (async step 1).
     * ETG processes bookings asynchronously — this call only initiates
     * the booking. The actual confirmation comes via polling (pollBookingStatus).
     *
     * Saves the booking to DB with status = 'pending'.
     * Returns the order_id so the frontend can poll for status.
     */
    public function startBooking(array $data, int $userId): array
    {
        if (config('ratehawk.use_mock')) {
            Log::info('[RateHawk:MOCK] startBooking (async start)', ['guest' => $data['guest']['email'] ?? '']);

            $result = BookingResult::startMock($data);

            if (in_array($result['status'], ['ok', 'processing'])) {
                $this->saveBooking($result['data'], $data, $userId, 'pending');
            }

            return $result;
        }

        // Real ETG API — Start async booking process
        $payload = [
            'book_hash' => $data['book_hash'],
            'user_data' => [
                'email'      => $data['guest']['email'],
                'phone'      => $data['guest']['phone'] ?? '',
                'first_name' => $data['guest']['first_name'],
                'last_name'  => $data['guest']['last_name'],
            ],
            'language' => config('ratehawk.language', 'en'),
        ];

        $result = $this->client->post('api/b2b/v3/hotel/order/booking/start/', $payload);

        if (!empty($result['data']['order_id'])) {
            $this->saveBooking($result['data'], $data, $userId, 'pending');
        }

        return $result;
    }

    /**
     * POLL the booking status (async step 2).
     * Call this repeatedly until status is 'confirmed' or 'failed'.
     * ETG recommends polling every 2–5 seconds for up to 60 seconds.
     */
    public function pollBookingStatus(string $orderId): array
    {
        if (config('ratehawk.use_mock')) {
            Log::info('[RateHawk:MOCK] pollBookingStatus', ['order_id' => $orderId]);
            return BookingResult::pollMock($orderId);
        }

        return $this->client->get("api/b2b/v3/hotel/order/booking/finish/{$orderId}/");
    }

    /**
     * Retrieve all bookings for the account (post-booking).
     */
    public function retrieveBookings(array $filters = []): array
    {
        if (config('ratehawk.use_mock')) {
            return ['status' => 'ok', 'data' => ['orders' => []]];
        }

        return $this->client->post('api/b2b/v3/order/get_orders/', $filters);
    }

    /**
     * Cancel a booking — calls the ETG API first, then the caller updates local DB.
     */
    public function cancelBookingViaApi(Booking $booking): array
    {
        if (config('ratehawk.use_mock')) {
            Log::info('[RateHawk:MOCK] cancelBooking', ['order_id' => $booking->ratehawk_order_id]);
            return ['status' => 'ok', 'data' => ['order_id' => $booking->ratehawk_order_id, 'status' => 'cancelled']];
        }

        return $this->client->post('api/b2b/v3/hotel/order/cancel/', [
            'order_id' => $booking->ratehawk_order_id,
        ]);
    }

    /**
     * Save a booking record to the local database.
     */
    protected function saveBooking(array $apiData, array $inputData, int $userId, string $initialStatus = 'pending'): void
    {
        try {
            Booking::create([
                'user_id'             => $userId,
                'ratehawk_order_id'   => $apiData['order_id'],
                'book_hash'           => $inputData['book_hash'],
                'hotel_id'            => $apiData['hotel']['id']      ?? $inputData['hotel_id'] ?? '',
                'hotel_name'          => $apiData['hotel']['name']    ?? $inputData['hotel_name'] ?? '',
                'hotel_address'       => $apiData['hotel']['address'] ?? '',
                'hotel_city'          => $inputData['hotel_city']     ?? '',
                'hotel_country'       => $inputData['hotel_country']  ?? '',
                'hotel_stars'         => $apiData['hotel']['stars']   ?? '',
                'hotel_image'         => $inputData['hotel_image']    ?? '',
                'check_in'            => $apiData['rate']['check_in']    ?? $inputData['check_in'],
                'check_out'           => $apiData['rate']['check_out']   ?? $inputData['check_out'],
                'guests'              => $inputData['guests']            ?? 1,
                'rooms'               => 1,
                'rooms_data'          => json_encode($apiData['rate']    ?? []),
                'total_price'         => $apiData['rate']['total_price'] ?? $inputData['total_price'] ?? 0,
                'currency'            => $apiData['rate']['currency']    ?? 'USD',
                'guest_first_name'    => $inputData['guest']['first_name'],
                'guest_last_name'     => $inputData['guest']['last_name'],
                'guest_email'         => $inputData['guest']['email'],
                'guest_phone'         => $inputData['guest']['phone']    ?? '',
                'status'              => $initialStatus,
                'cancellation_policy' => $inputData['cancellation_policy'] ?? '',
            ]);

            Log::info('[RateHawk] Booking saved to DB', [
                'order_id' => $apiData['order_id'],
                'status'   => $initialStatus,
            ]);
        } catch (\Exception $e) {
            Log::error('[RateHawk] Failed to save booking', ['error' => $e->getMessage()]);
        }
    }
}
