<?php

namespace App\Services\RateHawk\MockData;

/**
 * Simulates the ETG async booking flow:
 *  - startMock()  → returns 'processing' + order_id (step 1: start)
 *  - pollMock()   → returns 'confirmed'  + full data (step 2: finish/poll)
 *
 * In production, ETG processes bookings in the background.
 * The start endpoint returns immediately with an order_id,
 * and the finish endpoint is polled until the booking is confirmed or failed.
 */
class BookingResult
{
    /**
     * Simulate the START of a booking (async step 1).
     * Returns an order_id with status 'processing'.
     */
    public static function startMock(array $data = []): array
    {
        $orderId = 'NEST-' . strtoupper(substr(md5(uniqid()), 0, 8));

        return [
            'status' => 'ok',
            'data'   => [
                'order_id'  => $orderId,
                'status'    => 'processing', // async — not confirmed yet
                'book_hash' => $data['book_hash'] ?? 'hash_mock_' . uniqid(),
                'hotel'     => [
                    'id'      => $data['hotel_id']   ?? 'hotel_madrid_01',
                    'name'    => $data['hotel_name'] ?? 'Gran Hotel Melia Madrid',
                    'address' => 'Calle de Recoletos 4, Madrid 28001, España',
                    'stars'   => 5,
                    'phone'   => '+34 91 702 7000',
                ],
                'rate'      => [
                    'room_name'   => $data['room_name'] ?? 'Habitación Deluxe Doble',
                    'meal_label'  => 'Desayuno buffet incluido',
                    'check_in'    => $data['check_in']    ?? now()->addDays(7)->format('Y-m-d'),
                    'check_out'   => $data['check_out']   ?? now()->addDays(10)->format('Y-m-d'),
                    'total_price' => $data['total_price'] ?? 567.00,
                    'currency'    => 'USD',
                    'refundable'  => true,
                ],
                'guest'     => [
                    'first_name' => $data['guest']['first_name'] ?? '',
                    'last_name'  => $data['guest']['last_name']  ?? '',
                    'email'      => $data['guest']['email']      ?? '',
                ],
            ],
        ];
    }

    /**
     * Simulate POLLING a booking status (async step 2).
     * In mock mode, always returns 'confirmed' to complete the flow.
     * In a real stub scenario you could add a cache/counter to simulate delay.
     */
    public static function pollMock(string $orderId): array
    {
        return [
            'status' => 'ok',
            'data'   => [
                'order_id'   => $orderId,
                'status'     => 'confirmed',
                'voucher_url' => route('booking.confirm', ['id' => $orderId]),
            ],
        ];
    }
}
