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
     * Start the booking process.
     * Saves to local DB after confirmation.
     */
    public function startBooking(array $data, int $userId): array
    {
        if (config('ratehawk.use_mock')) {
            Log::info('[RateHawk:MOCK] startBooking', ['guest' => $data['guest']['email'] ?? '']);

            $result = BookingResult::get($data);

            if ($result['status'] === 'ok') {
                $this->saveBooking($result['data'], $data, $userId);
            }

            return $result;
        }

        // Real API call
        $payload = [
            'book_hash' => $data['book_hash'],
            'user_data' => [
                'email'      => $data['guest']['email'],
                'phone'      => $data['guest']['phone'] ?? '',
                'first_name' => $data['guest']['first_name'],
                'last_name'  => $data['guest']['last_name'],
            ],
            'language'  => config('ratehawk.language', 'en'),
        ];

        $result = $this->client->post('api/b2b/v3/order/order_id/', $payload);

        if (($result['status'] ?? '') === 'ok') {
            $this->saveBooking($result['data'], $data, $userId);
        }

        return $result;
    }

    /**
     * Check the status of a booking by order ID.
     */
    public function getBookingStatus(string $orderId): array
    {
        if (config('ratehawk.use_mock')) {
            Log::info('[RateHawk:MOCK] getBookingStatus', ['order_id' => $orderId]);
            return [
                'status' => 'ok',
                'data'   => ['order_id' => $orderId, 'status' => 'confirmed'],
            ];
        }

        return $this->client->get("api/b2b/v3/order/order_id/{$orderId}/");
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
     * Save confirmed booking to local database.
     */
    protected function saveBooking(array $apiData, array $inputData, int $userId): void
    {
        try {
            Booking::create([
                'user_id'             => $userId,
                'ratehawk_order_id'   => $apiData['order_id'],
                'book_hash'           => $inputData['book_hash'],
                'hotel_id'            => $apiData['hotel']['id']      ?? $inputData['hotel_id'] ?? '',
                'hotel_name'          => $apiData['hotel']['name']    ?? '',
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
                'total_price'         => $apiData['rate']['total_price'] ?? 0,
                'currency'            => $apiData['rate']['currency']    ?? 'USD',
                'guest_first_name'    => $inputData['guest']['first_name'],
                'guest_last_name'     => $inputData['guest']['last_name'],
                'guest_email'         => $inputData['guest']['email'],
                'guest_phone'         => $inputData['guest']['phone']    ?? '',
                'status'              => $apiData['status']              ?? 'confirmed',
                'cancellation_policy' => $inputData['cancellation_policy'] ?? '',
            ]);

            Log::info('[RateHawk] Booking saved to DB', ['order_id' => $apiData['order_id']]);
        } catch (\Exception $e) {
            Log::error('[RateHawk] Failed to save booking', ['error' => $e->getMessage()]);
        }
    }
}
