<?php

namespace App\Services\RateHawk\MockData;

class BookingResult
{
    public static function get(array $data = []): array
    {
        $orderId = 'NEST-' . strtoupper(substr(md5(uniqid()), 0, 8));

        return [
            'status' => 'ok',
            'data' => [
                'order_id'     => $orderId,
                'status'       => 'confirmed',
                'hotel_check'  => 'ok',
                'book_hash'    => $data['book_hash'] ?? 'hash_mock_' . uniqid(),
                'hotel' => [
                    'id'      => $data['hotel_id'] ?? 'hotel_madrid_01',
                    'name'    => 'Gran Hotel Melia Madrid',
                    'address' => 'Calle de Recoletos 4, Madrid 28001, España',
                    'stars'   => 5,
                    'phone'   => '+34 91 702 7000',
                ],
                'rate' => [
                    'room_name'    => $data['room_name'] ?? 'Habitación Deluxe Doble',
                    'meal_label'   => 'Desayuno buffet incluido',
                    'check_in'     => $data['check_in']    ?? now()->addDays(7)->format('Y-m-d'),
                    'check_out'    => $data['check_out']   ?? now()->addDays(10)->format('Y-m-d'),
                    'total_price'  => $data['total_price'] ?? 567.00,
                    'currency'     => 'USD',
                    'refundable'   => true,
                ],
                'guest' => [
                    'first_name' => $data['guest']['first_name'] ?? '',
                    'last_name'  => $data['guest']['last_name']  ?? '',
                    'email'      => $data['guest']['email']      ?? '',
                ],
                'created_at' => now()->toISOString(),
                'voucher_url' => route('booking.voucher', ['id' => $orderId]),
            ],
        ];
    }
}
