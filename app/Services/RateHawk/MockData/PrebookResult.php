<?php

namespace App\Services\RateHawk\MockData;

/**
 * Simulates the ETG response for:
 *   POST /api/b2b/v3/hotel/prebook/
 *
 * Input:  book_hash starting with "h-..." (from hotelpage)
 * Output: book_hash starting with "p-..." (to be used in booking/form)
 *
 * If price changed and price_increase_percent allows it, ETG returns
 * a new "p-..." hash with the adjusted price and price_changed = true.
 *
 * The "p-..." hash MUST be used in the Create Booking Form step (4.1).
 */
class PrebookResult
{
    public static function get(string $bookHash, int $priceIncreasePct = 0): array
    {
        // Simulate converting "h-..." → "p-..." hash
        $prebookHash = 'p-' . substr(md5($bookHash . 'prebook'), 0, 8) . '-'
                     . substr(md5($bookHash), 0, 4) . '-'
                     . substr(md5($bookHash . 'etg'), 0, 4) . '-'
                     . substr(md5($bookHash . 'nestay'), 0, 4) . '-'
                     . substr(md5($bookHash . 'mock'), 0, 12);

        return [
            'status' => 'ok',
            'data'   => [
                // This is the hash to use in /booking/form/
                'book_hash'     => $prebookHash,
                'match_hash'    => 'm-' . substr(md5($bookHash), 0, 36),
                'price_changed' => false,   // true if ETG found a rate with different price
                'rate'          => [
                    'room_name'        => 'Habitación Deluxe Doble',
                    'meal'             => 'breakfast',
                    'meal_data'        => [
                        'value'         => 'breakfast',
                        'has_breakfast' => true,
                        'no_child_meal' => false,
                    ],
                    'refundable'       => true,
                    'free_cancellation_before' => now()->addDays(5)->format('Y-m-d') . 'T12:00:00',
                    'payment_options'  => [
                        'payment_types' => [[
                            'type'                  => 'deposit',
                            'amount'                => '567.00',
                            'show_amount'           => '567.00',
                            'currency_code'         => 'USD',
                            'show_currency_code'    => 'USD',
                            'is_need_credit_card_data' => false,
                            'is_need_cvc'           => false,
                        ]],
                    ],
                    'cancellation_penalties' => [
                        'policies' => [
                            [
                                'start_at'      => null,
                                'end_at'        => now()->addDays(5)->format('Y-m-d\TH:i:s'),
                                'amount_charge' => '0.00',
                                'amount_show'   => '0.00',
                            ],
                            [
                                'start_at'      => now()->addDays(5)->format('Y-m-d\TH:i:s'),
                                'end_at'        => null,
                                'amount_charge' => '567.00',
                                'amount_show'   => '567.00',
                            ],
                        ],
                        'free_cancellation_before' => now()->addDays(5)->format('Y-m-d') . 'T12:00:00',
                    ],
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
