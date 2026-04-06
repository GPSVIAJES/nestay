<?php

namespace App\Services\RateHawk\MockData;

class HotelDetail
{
    public static function get(string $hotelId, array $params = []): array
    {
        $checkIn  = $params['checkin']  ?? now()->addDays(7)->format('Y-m-d');
        $checkOut = $params['checkout'] ?? now()->addDays(10)->format('Y-m-d');
        $nights   = (int) ceil((strtotime($checkOut) - strtotime($checkIn)) / 86400);

        return [
            'status' => 'ok',
            'data' => [
                'hotel' => [
                    'id'          => $hotelId,
                    'hid'         => 12345,
                    'name'        => 'Gran Hotel Melia Madrid',
                    'stars'       => 5,
                    'rating'      => 8.9,
                    'reviews'     => 2847,
                    'latitude'    => 40.4168,
                    'longitude'   => -3.7038,
                    'address'     => 'Calle de Recoletos 4, Madrid 28001, España',
                    'city'        => 'Madrid',
                    'country'     => 'España',
                    'phone'       => '+34 91 702 7000',
                    'email'       => 'reservations@melia-madrid.com',
                    'website'     => 'https://www.melia.com',
                    'check_in_time'  => '15:00',
                    'check_out_time' => '12:00',
                    'description' => 'Un oasis de lujo en el corazón de Madrid. Situado en el elegante barrio de Recoletos, el Gran Hotel Meliá Madrid ofrece una experiencia de hospitalidad incomparable con 201 habitaciones y suites, restaurante de alta cocina, spa de clase mundial y vistas panorámicas de la ciudad.',
                    'images' => [
                        'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=1200',
                        'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200',
                        'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=1200',
                        'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=1200',
                        'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=1200',
                        'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?w=1200',
                    ],
                    'amenities' => [
                        'wifi'        => ['label' => 'WiFi gratuito', 'icon' => 'wifi'],
                        'pool'        => ['label' => 'Piscina', 'icon' => 'pool'],
                        'spa'         => ['label' => 'Spa & Wellness', 'icon' => 'spa'],
                        'restaurant'  => ['label' => 'Restaurante', 'icon' => 'restaurant'],
                        'bar'         => ['label' => 'Bar & Lounge', 'icon' => 'bar'],
                        'gym'         => ['label' => 'Gimnasio', 'icon' => 'gym'],
                        'parking'     => ['label' => 'Parking', 'icon' => 'parking'],
                        'concierge'   => ['label' => 'Concierge 24h', 'icon' => 'concierge'],
                        'roomservice' => ['label' => 'Room Service 24h', 'icon' => 'service'],
                        'business'    => ['label' => 'Centro de negocios', 'icon' => 'business'],
                    ],
                    'policies' => [
                        'check_in'     => 'Check-in desde las 15:00. Llegadas anticipadas sujetas a disponibilidad.',
                        'check_out'    => 'Check-out antes de las 12:00. Late checkout sujeto a disponibilidad.',
                        'pets'         => 'No se admiten mascotas.',
                        'smoking'      => 'Hotel 100% libre de humo.',
                        'children'     => 'Menores de 12 años gratis cuando comparten habitación con sus padres.',
                    ],
                ],
                'rates' => [
                    [
                        'book_hash'        => 'hash_madrid_01_deluxe_' . time(),
                        'match_hash'       => 'match_madrid_01',
                        'room_name'        => 'Habitación Deluxe Doble',
                        'room_description' => 'Elegante habitación doble con todas las comodidades de lujo, escritorio ejecutivo y baño de mármol. Disponible con cama king size o dos camas individuales.',
                        'meal'             => 'breakfast',
                        'meal_label'       => 'Desayuno buffet incluido',
                        'refundable'       => true,
                        'refundable_until' => now()->addDays(5)->format('Y-m-d') . ' 12:00:00',
                        'payment_type'     => 'now',
                        'daily_price'      => 189.00,
                        'total_price'      => 189.00 * $nights,
                        'currency'         => 'USD',
                        'cancellation_policy' => [
                            [
                                'start_at'   => null,
                                'end_at'     => now()->addDays(5)->format('Y-m-d') . 'T12:00:00Z',
                                'penalty'    => 0,
                                'penalty_type' => 'percent',
                            ],
                            [
                                'start_at'   => now()->addDays(5)->format('Y-m-d') . 'T12:00:00Z',
                                'end_at'     => null,
                                'penalty'    => 100,
                                'penalty_type' => 'percent',
                            ],
                        ],
                        'amenities' => ['wifi', 'minibar', 'safe', 'ac', 'flatscreen'],
                        'max_occupancy' => 2,
                    ],
                    [
                        'book_hash'        => 'hash_madrid_01_deluxe_bb_' . time(),
                        'match_hash'       => 'match_madrid_01b',
                        'room_name'        => 'Habitación Deluxe Doble',
                        'room_description' => 'Idéntica habitación Deluxe pero sin desayuno incluido. Opción más económica para quienes prefieren explorar los cafés de Madrid.',
                        'meal'             => 'none',
                        'meal_label'       => 'Solo alojamiento',
                        'refundable'       => true,
                        'refundable_until' => now()->addDays(5)->format('Y-m-d') . ' 12:00:00',
                        'payment_type'     => 'now',
                        'daily_price'      => 159.00,
                        'total_price'      => 159.00 * $nights,
                        'currency'         => 'USD',
                        'cancellation_policy' => [
                            [
                                'start_at'   => null,
                                'end_at'     => now()->addDays(5)->format('Y-m-d') . 'T12:00:00Z',
                                'penalty'    => 0,
                                'penalty_type' => 'percent',
                            ],
                        ],
                        'amenities' => ['wifi', 'minibar', 'safe', 'ac', 'flatscreen'],
                        'max_occupancy' => 2,
                    ],
                    [
                        'book_hash'        => 'hash_madrid_01_suite_' . time(),
                        'match_hash'       => 'match_madrid_02',
                        'room_name'        => 'Suite Junior con Vistas',
                        'room_description' => 'Espectacular suite junior con sala de estar separada, terraza privada con vistas panorámicas al Paseo de Recoletos y baño de lujo con jacuzzi.',
                        'meal'             => 'breakfast',
                        'meal_label'       => 'Desayuno buffet en habitación incluido',
                        'refundable'       => false,
                        'payment_type'     => 'now',
                        'daily_price'      => 310.00,
                        'total_price'      => 310.00 * $nights,
                        'currency'         => 'USD',
                        'cancellation_policy' => [
                            [
                                'start_at'     => null,
                                'end_at'       => null,
                                'penalty'      => 100,
                                'penalty_type' => 'percent',
                            ],
                        ],
                        'amenities' => ['wifi', 'minibar', 'safe', 'ac', 'flatscreen', 'jacuzzi', 'terrace'],
                        'max_occupancy' => 3,
                    ],
                ],
            ],
        ];
    }
}
