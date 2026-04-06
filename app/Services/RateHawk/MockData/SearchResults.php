<?php

namespace App\Services\RateHawk\MockData;

class SearchResults
{
    /**
     * Realistic mock response matching RateHawk B2B API v3 search/serp/region/ format.
     */
    public static function get(array $params = []): array
    {
        $checkIn  = $params['checkin']  ?? now()->addDays(7)->format('Y-m-d');
        $checkOut = $params['checkout'] ?? now()->addDays(10)->format('Y-m-d');
        $guests   = $params['guests']   ?? 2;
        $nights   = (int) ceil((strtotime($checkOut) - strtotime($checkIn)) / 86400);

        return [
            'status' => 'ok',
            'data' => [
                'hotels' => [
                    [
                        'id'        => 'hotel_madrid_01',
                        'hid'       => 12345,
                        'name'      => 'Gran Hotel Melia Madrid',
                        'stars'     => 5,
                        'rating'    => 8.9,
                        'reviews'   => 2847,
                        'latitude'  => 40.4168,
                        'longitude' => -3.7038,
                        'address'   => 'Calle de Recoletos 4, Madrid, España',
                        'city'      => 'Madrid',
                        'country'   => 'España',
                        'images'    => [
                            'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800',
                            'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
                        ],
                        'amenities' => ['wifi', 'pool', 'spa', 'restaurant', 'gym', 'parking'],
                        'rates' => [
                            [
                                'book_hash'        => 'hash_madrid_01_deluxe_' . time(),
                                'match_hash'       => 'match_madrid_01',
                                'room_name'        => 'Habitación Deluxe Doble',
                                'meal'             => 'breakfast',
                                'meal_label'       => 'Desayuno incluido',
                                'refundable'       => true,
                                'refundable_until' => now()->addDays(5)->format('Y-m-d'),
                                'payment_type'     => 'now',
                                'daily_price'      => 189.00,
                                'total_price'      => 189.00 * $nights,
                                'currency'         => 'USD',
                            ],
                            [
                                'book_hash'    => 'hash_madrid_01_suite_' . time(),
                                'match_hash'   => 'match_madrid_02',
                                'room_name'    => 'Suite Junior con vistas',
                                'meal'         => 'none',
                                'meal_label'   => 'Solo alojamiento',
                                'refundable'   => false,
                                'payment_type' => 'now',
                                'daily_price'  => 310.00,
                                'total_price'  => 310.00 * $nights,
                                'currency'     => 'USD',
                            ],
                        ],
                    ],
                    [
                        'id'        => 'hotel_madrid_02',
                        'hid'       => 12346,
                        'name'      => 'Hotel NH Collection Suecia',
                        'stars'     => 4,
                        'rating'    => 8.4,
                        'reviews'   => 1923,
                        'latitude'  => 40.4180,
                        'longitude' => -3.6950,
                        'address'   => 'Calle del Marqués de Casa Riera 4, Madrid',
                        'city'      => 'Madrid',
                        'country'   => 'España',
                        'images'    => [
                            'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800',
                            'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=800',
                        ],
                        'amenities' => ['wifi', 'restaurant', 'bar', 'gym'],
                        'rates' => [
                            [
                                'book_hash'        => 'hash_madrid_02_std_' . time(),
                                'match_hash'       => 'match_madrid_03',
                                'room_name'        => 'Habitación Estándar',
                                'meal'             => 'none',
                                'meal_label'       => 'Solo alojamiento',
                                'refundable'       => true,
                                'refundable_until' => now()->addDays(3)->format('Y-m-d'),
                                'payment_type'     => 'now',
                                'daily_price'      => 125.00,
                                'total_price'      => 125.00 * $nights,
                                'currency'         => 'USD',
                            ],
                        ],
                    ],
                    [
                        'id'        => 'hotel_madrid_03',
                        'hid'       => 12347,
                        'name'      => 'Barceló Torre de Madrid',
                        'stars'     => 4,
                        'rating'    => 8.7,
                        'reviews'   => 3102,
                        'latitude'  => 40.4214,
                        'longitude' => -3.7111,
                        'address'   => 'Plaza de España 18, Madrid',
                        'city'      => 'Madrid',
                        'country'   => 'España',
                        'images'    => [
                            'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=800',
                            'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=800',
                        ],
                        'amenities' => ['wifi', 'pool', 'spa', 'restaurant', 'bar', 'rooftop'],
                        'rates' => [
                            [
                                'book_hash'        => 'hash_madrid_03_classic_' . time(),
                                'match_hash'       => 'match_madrid_04',
                                'room_name'        => 'Habitación Clásica Doble',
                                'meal'             => 'breakfast',
                                'meal_label'       => 'Desayuno incluido',
                                'refundable'       => true,
                                'refundable_until' => now()->addDays(6)->format('Y-m-d'),
                                'payment_type'     => 'now',
                                'daily_price'      => 145.00,
                                'total_price'      => 145.00 * $nights,
                                'currency'         => 'USD',
                            ],
                        ],
                    ],
                    [
                        'id'        => 'hotel_madrid_04',
                        'hid'       => 12348,
                        'name'      => 'Ibis Madrid Centro Las Ventas',
                        'stars'     => 2,
                        'rating'    => 7.6,
                        'reviews'   => 876,
                        'latitude'  => 40.4275,
                        'longitude' => -3.6627,
                        'address'   => 'Calle Alcalá 276, Madrid',
                        'city'      => 'Madrid',
                        'country'   => 'España',
                        'images'    => [
                            'https://images.unsplash.com/photo-1576354302919-96748cb8299e?w=800',
                        ],
                        'amenities' => ['wifi', 'breakfast', 'parking'],
                        'rates' => [
                            [
                                'book_hash'    => 'hash_madrid_04_std_' . time(),
                                'match_hash'   => 'match_madrid_05',
                                'room_name'    => 'Habitación Estándar Doble',
                                'meal'         => 'none',
                                'meal_label'   => 'Sin desayuno',
                                'refundable'   => false,
                                'payment_type' => 'now',
                                'daily_price'  => 65.00,
                                'total_price'  => 65.00 * $nights,
                                'currency'     => 'USD',
                            ],
                        ],
                    ],
                    [
                        'id'        => 'hotel_madrid_05',
                        'hid'       => 12349,
                        'name'      => 'Rosewood Villa Magna Madrid',
                        'stars'     => 5,
                        'rating'    => 9.2,
                        'reviews'   => 1456,
                        'latitude'  => 40.4240,
                        'longitude' => -3.6901,
                        'address'   => 'Paseo de la Castellana 22, Madrid',
                        'city'      => 'Madrid',
                        'country'   => 'España',
                        'images'    => [
                            'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
                            'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=800',
                        ],
                        'amenities' => ['wifi', 'pool', 'spa', 'restaurant', 'bar', 'gym', 'concierge', 'butler'],
                        'rates' => [
                            [
                                'book_hash'        => 'hash_madrid_05_lux_' . time(),
                                'match_hash'       => 'match_madrid_06',
                                'room_name'        => 'Superior Room Garden View',
                                'meal'             => 'breakfast',
                                'meal_label'       => 'Desayuno de lujo incluido',
                                'refundable'       => true,
                                'refundable_until' => now()->addDays(7)->format('Y-m-d'),
                                'payment_type'     => 'now',
                                'daily_price'      => 480.00,
                                'total_price'      => 480.00 * $nights,
                                'currency'         => 'USD',
                            ],
                        ],
                    ],
                ],
                'total'       => 5,
                'region_id'   => 4230,
                'region_name' => 'Madrid',
                'search_id'   => 'mock_search_' . uniqid(),
            ],
        ];
    }
}
