<?php

namespace App\Services\RateHawk;

use App\Services\RateHawk\MockData\SearchResults;
use App\Services\RateHawk\MockData\HotelDetail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HotelSearchService
{
    public function __construct(protected RateHawkClient $client)
    {
    }

    /**
     * Search hotels by region (SERP).
     */
    public function searchByRegion(array $params): array
    {
        if (config('ratehawk.use_mock')) {
            Log::info('[RateHawk:MOCK] searchByRegion', $params);
            return SearchResults::get($params);
        }


        $cacheKey = 'rh_search_' . md5(json_encode($params));
        $ttl = config('ratehawk.cache.search', 300);

        try {
            $raw = Cache::remember($cacheKey, $ttl, function () use ($params) {
                return $this->client->post('api/b2b/v3/search/serp/region/', [
                    'region_id' => $params['region_id'],
                    'checkin' => $params['checkin'],
                    'checkout' => $params['checkout'],
                    'guests' => [[
                        'adults' => (int)($params['adults'] ?? 2),
                        'children' => $params['children_ages'] ?? []
                    ]],
                    'language' => config('ratehawk.language', 'en'),
                    'currency' => 'USD',
                    'residency' => config('ratehawk.residency', 'US'),
                ]);
            });

            // Sandbox returns {status:'error'} for unsupported regions
            if (($raw['status'] ?? '') === 'error' || empty($raw['data']['hotels'])) {
                Log::info('[RateHawk] searchByRegion returned no results, using mock fallback', [
                    'region_id' => $params['region_id'],
                    'status' => $raw['status'] ?? 'empty',
                    'error' => $raw['error'] ?? null,
                ]);
                // Clear the cached error so it re-tries next time
                Cache::forget($cacheKey);
                return SearchResults::get($params);
            }

            // Normalize real API response to match our mock format
            return $this->normalizeSearchResponse($raw, $params);

        } catch (\Exception $e) {
            Log::warning('[RateHawk] searchByRegion API error, using mock fallback: ' . $e->getMessage());
            return SearchResults::get($params);
        }
    }

    /**
     * Get full hotel page (hotelpage) with all rates.
     */
    public function getHotelPage(string $hotelId, array $params): array
    {
        if (config('ratehawk.use_mock')) {
            Log::info('[RateHawk:MOCK] getHotelPage', ['hotel_id' => $hotelId]);
            return HotelDetail::get($hotelId, $params);
        }

        $cacheKey = 'rh_hotel_' . $hotelId . '_' . md5(json_encode($params));
        $ttl = config('ratehawk.cache.hotel_page', 86400);

        try {
            $raw = Cache::remember($cacheKey, $ttl, function () use ($hotelId, $params) {
                return $this->client->post('api/b2b/v3/search/hp/', [
                    'id' => $hotelId,
                    'checkin' => $params['checkin'],
                    'checkout' => $params['checkout'],
                    'guests' => [[
                        'adults' => (int)($params['adults'] ?? 2),
                        'children' => $params['children_ages'] ?? []
                    ]],
                    'language' => config('ratehawk.language', 'en'),
                    'currency' => 'USD',
                    'residency' => config('ratehawk.residency', 'US'),
                ]);
            });

            if (($raw['status'] ?? '') === 'error' || empty($raw['data']['hotels'])) {
                Log::info('[RateHawk] getHotelPage returned no data, using mock fallback', ['hotel_id' => $hotelId]);
                Cache::forget($cacheKey);

                $mock = HotelDetail::get($hotelId, $params);
                if (!empty($params['hotel_name'])) {
                    $mock['data']['hotel']['name'] = $params['hotel_name'];
                    $mock['data']['hotel']['address'] = $params['hotel_address'] ?? $mock['data']['hotel']['address'];
                    $mock['data']['hotel']['stars'] = $params['hotel_stars'] ?? $mock['data']['hotel']['stars'];
                }
                return $mock;
            }

            // If we get real RateHawk data, it ONLY contains rates (hp endpoint doesn't return static info).
            // We need to transform it to our standard structure and inject fallback static data so the UI doesn't break.
            $hpData = $raw['data']['hotels'][0];

            $local = \App\Models\Hotel::find($hotelId);
            $mockStatic = HotelDetail::get($hotelId, $params)['data']['hotel'];

            $images = is_array($local?->images) ? $local->images : (is_string($local?->images) ? json_decode($local->images, true) : $mockStatic['images']);
            $amenities = is_array($local?->amenities) ? $local->amenities : (is_string($local?->amenities) ? json_decode($local->amenities, true) : $mockStatic['amenities']);

            if (empty($amenities)) {
                $amenities = ['WiFi gratuito', 'Aire acondicionado', 'Servicio diario de limpieza'];
            }
            $description = $local ? $local->description : $mockStatic['description'];

            if (is_array($images)) {
                $images = array_map(fn($url) => str_replace('{size}', 'x500', $url), $images);
            }

            $rating = $hotelId ? round((crc32($hotelId) % 30 + 70) / 10, 1) : 8.5;

            $normalizedRates = array_map(function ($r) {
                return [
                    'book_hash' => $r['book_hash'] ?? '',
                    'room_name' => $r['room_name'] ?? 'Habitación Estándar',
                    'meal_label' => $r['meal'] ?? 'Solo alojamiento',
                    'daily_price' => $r['payment_options']['payment_types'][0]['show_amount'] ?? 0,
                    'total_price' => $r['payment_options']['payment_types'][0]['show_amount'] ?? 0,
                    'refundable' => !empty($r['cancellation_info']['free_cancellation_before']),
                    'cancellation_info' => $r['cancellation_info'] ?? null,
                    'currency' => $r['payment_options']['payment_types'][0]['show_currency_code'] ?? 'USD',
                    'amenities' => ['wifi', 'ac', 'tv']
                ];
            }, $hpData['rates'] ?? []);

            return [
                'status' => 'ok',
                'data' => [
                    'hotel' => [
                        'id' => $hpData['id'] ?? $hotelId,
                        'name' => $local ? $local->name : ($hpData['name'] ?? $params['hotel_name'] ?? 'Hotel ' . ($hpData['id'] ?? $hotelId)),
                        'stars' => $local ? $local->star_rating : ($hpData['star_rating'] ?? $params['hotel_stars'] ?? 4),
                        'rating' => $rating,
                        'reviews' => rand(100, 500),
                        'address' => $local ? $local->address : ($hpData['address'] ?? $params['hotel_address'] ?? 'Dirección del hotel'),
                        'city' => $local ? $local->city : ($hpData['region']['name'] ?? 'Ciudad'),
                        'images' => $images,
                        'amenities' => $amenities,
                        'description' => $description,
                    ],
                    'rates' => $normalizedRates,
                ]
            ];

        } catch (\Exception $e) {
            Log::warning('[RateHawk] getHotelPage API error, using mock fallback: ' . $e->getMessage());
            $mock = HotelDetail::get($hotelId, $params);
            if (!empty($params['hotel_name'])) {
                $mock['data']['hotel']['name'] = $params['hotel_name'];
                $mock['data']['hotel']['address'] = $params['hotel_address'] ?? $mock['data']['hotel']['address'];
                $mock['data']['hotel']['stars'] = $params['hotel_stars'] ?? $mock['data']['hotel']['stars'];
            }
            return $mock;
        }
    }

    /**
     * Autocomplete destinations/hotels (multicomplete).
     */
    public function suggest(string $query, string $language = 'es'): array
    {
        $q = mb_strtolower($query);

        // 1. Search in local database (hotels and cities)
        $localHotels = \App\Models\Hotel::where('name', 'like', "%{$query}%")
            ->orWhere('city', 'like', "%{$query}%")
            ->limit(20)
            ->get();

        $regions = [];
        $hotels = [];
        
        // Extract cities
        $cities = $localHotels->pluck('city')->unique();
        foreach ($cities as $city) {
            if (str_contains(mb_strtolower($city), $q)) {
                $sampleHotel = $localHotels->where('city', $city)->first();
                $regions[] = [
                    'id' => $sampleHotel->region_id,
                    'name' => $city,
                    'type' => 'region',
                    'country' => $sampleHotel->country,
                    'hotels_count' => \App\Models\Hotel::where('city', $city)->count(),
                ];
            }
        }

        // Extract individual hotels
        foreach ($localHotels as $hotel) {
            if (str_contains(mb_strtolower($hotel->name), $q)) {
                $hotels[] = [
                    'id' => $hotel->region_id,
                    'name' => $hotel->name,
                    'type' => 'hotel',
                    'country' => $hotel->country,
                    'hotel_id' => $hotel->id,
                ];
            }
        }

        // Sort regions: Exact matches first
        usort($regions, function($a, $b) use ($q) {
            if (mb_strtolower($a['name']) === $q) return -1;
            if (mb_strtolower($b['name']) === $q) return 1;
            return 0;
        });

        // Combine: Regions first, then hotels
        $items = array_merge($regions, $hotels);

        // 2. If nothing found locally AND not using mock, try the real API
        if (empty($items) && !config('ratehawk.use_mock')) {
            $cacheKey = 'rh_suggest_' . md5($query . $language);
            $ttl = config('ratehawk.cache.suggestions', 3600);

            $raw = Cache::remember($cacheKey, $ttl, function () use ($query, $language) {
                return $this->client->post('api/b2b/v3/search/multicomplete/', [
                    'query' => $query,
                    'language' => $language,
                ]);
            });

            $apiRegions = $raw['data']['regions'] ?? [];
            $apiHotels = $raw['data']['hotels'] ?? [];

            $items = array_merge(
                array_map(fn($r) => array_merge($r, ['type' => 'region']), $apiRegions),
                array_map(fn($h) => array_merge($h, ['type' => 'hotel']), $apiHotels)
            );
        }

        // 3. Last fallback: Hardcoded mocks if still empty
        if (empty($items)) {
            return $this->getMockSuggestions($query);
        }

        return [
            'status' => 'ok',
            'data' => array_slice($items, 0, 10), // Limit to top 10 results total
        ];
    }

    /**
     * Mock suggestions matching RateHawk multicomplete response format.
     */
    protected function getMockSuggestions(string $query): array
    {
        $destinations = [
            // ── Regiones / Ciudades ──────────────────────────────
            ['id' => 4230, 'name' => 'Madrid', 'type' => 'region', 'country' => 'España', 'hotels_count' => 892],
            ['id' => 4231, 'name' => 'Barcelona', 'type' => 'region', 'country' => 'España', 'hotels_count' => 1205],
            ['id' => 4232, 'name' => 'Málaga', 'type' => 'region', 'country' => 'España', 'hotels_count' => 423],
            ['id' => 4233, 'name' => 'Valencia', 'type' => 'region', 'country' => 'España', 'hotels_count' => 386],
            ['id' => 4234, 'name' => 'Sevilla', 'type' => 'region', 'country' => 'España', 'hotels_count' => 341],
            ['id' => 2100, 'name' => 'Bogotá', 'type' => 'region', 'country' => 'Colombia', 'hotels_count' => 654],
            ['id' => 2101, 'name' => 'Medellín', 'type' => 'region', 'country' => 'Colombia', 'hotels_count' => 412],
            ['id' => 3300, 'name' => 'México D.F.', 'type' => 'region', 'country' => 'México', 'hotels_count' => 987],
            ['id' => 3301, 'name' => 'Cancún', 'type' => 'region', 'country' => 'México', 'hotels_count' => 756],
            ['id' => 1000, 'name' => 'París', 'type' => 'region', 'country' => 'Francia', 'hotels_count' => 2310],
            ['id' => 1001, 'name' => 'Londres', 'type' => 'region', 'country' => 'Reino Unido', 'hotels_count' => 3102],
            ['id' => 1002, 'name' => 'Roma', 'type' => 'region', 'country' => 'Italia', 'hotels_count' => 1876],
            ['id' => 1003, 'name' => 'Nueva York', 'type' => 'region', 'country' => 'EE.UU.', 'hotels_count' => 4231],
            ['id' => 1004, 'name' => 'Miami', 'type' => 'region', 'country' => 'EE.UU.', 'hotels_count' => 1543],

            // ── Hoteles de demo ──────────────────────────────────
            ['id' => 4230, 'name' => 'Gran Hotel Melia Madrid', 'type' => 'hotel', 'country' => 'España', 'hotel_id' => 'hotel_madrid_01'],
            ['id' => 4230, 'name' => 'Hotel NH Collection Suecia', 'type' => 'hotel', 'country' => 'España', 'hotel_id' => 'hotel_madrid_02'],
            ['id' => 4230, 'name' => 'Barceló Torre de Madrid', 'type' => 'hotel', 'country' => 'España', 'hotel_id' => 'hotel_madrid_03'],
            ['id' => 4230, 'name' => 'Ibis Madrid Centro Las Ventas', 'type' => 'hotel', 'country' => 'España', 'hotel_id' => 'hotel_madrid_04'],
            ['id' => 4230, 'name' => 'Rosewood Villa Magna Madrid', 'type' => 'hotel', 'country' => 'España', 'hotel_id' => 'hotel_madrid_05'],
        ];

        $q = mb_strtolower($query);
        $filtered = array_filter(
            $destinations,
            fn($d) =>
            str_contains(mb_strtolower($d['name']), $q) ||
            str_contains(mb_strtolower($d['country']), $q)
        );

        return [
            'status' => 'ok',
            'data' => array_values($filtered),
        ];
    }

    /**
     * Normalize real RateHawk SERP response to match our internal format.
     * Real API: {status, data: {hotels: [{id, rates:[{book_hash,...}]}], ...}}
     * Our format: {status, data: {hotels: [{id, name, stars, images, rates, ...}]}}
     */
    protected function normalizeSearchResponse(array $raw, array $params): array
    {
        $hotels = $raw['data']['hotels'] ?? [];

        $hotelIds = array_column($hotels, 'id');
        $localData = \App\Models\Hotel::whereIn('id', $hotelIds)->get()->keyBy('id');

        $normalized = array_map(function ($h) use ($localData) {
            $local = $localData->get($h['id'] ?? '');

            $images = is_array($local?->images) ? $local->images : (is_string($local?->images) ? json_decode($local->images, true) : array_column(is_array($h['images'] ?? null) ? $h['images'] : [], 'src'));
            $amenities = is_array($local?->amenities) ? $local->amenities : (is_string($local?->amenities) ? json_decode($local->amenities, true) : array_column(is_array($h['amenities'] ?? null) ? $h['amenities'] : [], 'name'));

            if (empty($amenities)) {
                $amenities = ['WiFi gratuito', 'Aire acondicionado', 'Servicio diario de limpieza'];
            }

            if (is_array($images)) {
                $images = array_map(fn($url) => str_replace('{size}', 'x500', $url), $images);
            }

            // Generate a consistent mock rating since RateHawk dump doesn't provide it
            $ratingId = $h['id'] ?? '';
            $rating = $ratingId ? round((crc32($ratingId) % 30 + 70) / 10, 1) : 8.5;

            return [
                'id' => $h['id'] ?? '',
                'name' => $local ? $local->name : ($h['name'] ?? ''),
                'stars' => $local ? $local->star_rating : ($h['star_rating'] ?? $h['stars'] ?? 0),
                'rating' => $rating,
                'address' => $local ? $local->address : ($h['address'] ?? ''),
                'city' => $local ? $local->city : ($h['region']['name'] ?? ''),
                'images' => $images,
                'amenities' => $amenities,
                'rates' => array_map(fn($r) => [
                    'book_hash' => $r['book_hash'] ?? '',
                    'room_name' => $r['room_name'] ?? '',
                    'meal_label' => $r['meal'] ?? '',
                    'daily_price' => $r['payment_options']['payment_types'][0]['show_amount'] ?? 0,
                    'total_price' => $r['payment_options']['payment_types'][0]['show_amount'] ?? 0,
                    'refundable' => !empty($r['cancellation_info']['free_cancellation_before']),
                    'cancellation_info' => $r['cancellation_info'] ?? null,
                    'currency' => $r['payment_options']['payment_types'][0]['show_currency_code'] ?? 'USD',
                ], $h['rates'] ?? []),
            ];
        }, $hotels);

        return [
            'status' => 'ok',
            'data' => [
                'hotels' => $normalized,
                'total' => count($normalized),
                'region_name' => $raw['data']['region']['name'] ?? '',
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 1.2 — Retrieve Hotel Content
    // POST /api/b2b/v3/hotel/info/
    // ─────────────────────────────────────────────────────────────────────────
    public function getHotelContent(array $hotelIds, string $language = 'es'): array
    {
        if (config('ratehawk.use_mock')) {
            Log::info('[RateHawk:MOCK] getHotelContent fallback', ['ids' => $hotelIds]);
            return ['status' => 'ok', 'data' => []];
        }

        return $this->client->post('api/b2b/v3/hotel/info/', [
            'id' => $hotelIds[0] ?? '', // Assuming checking single or we can iterate
            'language' => $language,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 2.1 — Search By Hotels
    // POST /api/b2b/v3/search/serp/hotels/
    // ─────────────────────────────────────────────────────────────────────────
    public function searchByHotels(array $hotelIds, array $params): array
    {
        if (config('ratehawk.use_mock')) {
            Log::info('[RateHawk:MOCK] searchByHotels fallback', ['ids' => $hotelIds]);
            return SearchResults::get($params);
        }

        $raw = $this->client->post('api/b2b/v3/search/serp/hotels/', [
            'ids' => $hotelIds,
            'checkin' => $params['checkin'],
            'checkout' => $params['checkout'],
            'guests' => [[
                'adults' => (int)($params['adults'] ?? 2),
                'children' => $params['children_ages'] ?? []
            ]],
            'language' => config('ratehawk.language', 'en'),
            'currency' => 'USD',
            'residency' => config('ratehawk.residency', 'US'),
        ]);

        return $this->normalizeSearchResponse($raw, $params);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 2.1 — Search By Geo
    // POST /api/b2b/v3/search/serp/geo/
    // ─────────────────────────────────────────────────────────────────────────
    public function searchByGeo(float $lat, float $lon, int $radius, array $params): array
    {
        if (config('ratehawk.use_mock')) {
            Log::info('[RateHawk:MOCK] searchByGeo fallback', ['lat' => $lat, 'lon' => $lon]);
            return SearchResults::get($params);
        }

        $raw = $this->client->post('api/b2b/v3/search/serp/geo/', [
            'latitude' => $lat,
            'longitude' => $lon,
            'radius' => $radius,
            'checkin' => $params['checkin'],
            'checkout' => $params['checkout'],
            'guests' => [[
                'adults' => (int)($params['adults'] ?? 2),
                'children' => $params['children_ages'] ?? []
            ]],
            'language' => config('ratehawk.language', 'en'),
            'currency' => 'USD',
            'residency' => config('ratehawk.residency', 'US'),
        ]);

        return $this->normalizeSearchResponse($raw, $params);
    }
}
