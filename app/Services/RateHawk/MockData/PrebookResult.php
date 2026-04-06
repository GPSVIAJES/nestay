<?php

namespace App\Services\RateHawk\MockData;

class PrebookResult
{
    public static function get(string $bookHash): array
    {
        return [
            'status' => 'ok',
            'data' => [
                'book_hash'   => $bookHash,
                'match_hash'  => 'match_confirmed_' . substr($bookHash, -8),
                'price_changed' => false,
                'rate' => [
                    'room_name'        => 'Habitación Deluxe Doble',
                    'meal'             => 'breakfast',
                    'meal_label'       => 'Desayuno buffet incluido',
                    'refundable'       => true,
                    'refundable_until' => now()->addDays(5)->format('Y-m-d') . ' 12:00:00',
                    'payment_type'     => 'now',
                    'daily_price'      => 189.00,
                    'total_price'      => 567.00,
                    'currency'         => 'USD',
                    'cancellation_policy' => 'Cancelación gratuita hasta 5 días antes del check-in. Después se cobrará el 100% del importe total.',
                ],
                'hotel' => [
                    'id'      => 'hotel_madrid_01',
                    'name'    => 'Gran Hotel Melia Madrid',
                    'address' => 'Calle de Recoletos 4, Madrid',
                    'stars'   => 5,
                ],
            ],
        ];
    }
}
