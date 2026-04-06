<?php

namespace App\Services\RateHawk;

use App\Services\RateHawk\MockData\SearchResults;
use App\Services\RateHawk\MockData\HotelDetail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HotelSearchService
{
    public function __construct(protected RateHawkClient $client)
    {}

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
        $ttl      = config('ratehawk.cache.search', 300);

        return Cache::remember($cacheKey, $ttl, function () use ($params) {
            return $this->client->post('api/b2b/v3/search/serp/region/', [
                'region_id'   => $params['region_id'],
                'checkin'     => $params['checkin'],
                'checkout'    => $params['checkout'],
                'guests'      => [['adults' => $params['adults'] ?? 2]],
                'language'    => config('ratehawk.language', 'en'),
                'currency'    => 'USD',
                'residency'   => config('ratehawk.residency', 'US'),
            ]);
        });
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
        $ttl      = config('ratehawk.cache.hotel_page', 86400);

        return Cache::remember($cacheKey, $ttl, function () use ($hotelId, $params) {
            return $this->client->post('api/b2b/v3/search/hp/', [
                'id'        => $hotelId,
                'checkin'   => $params['checkin'],
                'checkout'  => $params['checkout'],
                'guests'    => [['adults' => $params['adults'] ?? 2]],
                'language'  => config('ratehawk.language', 'en'),
                'currency'  => 'USD',
                'residency' => config('ratehawk.residency', 'US'),
            ]);
        });
    }

    /**
     * Autocomplete destinations/hotels (multicomplete).
     */
    public function suggest(string $query, string $language = 'es'): array
    {
        if (config('ratehawk.use_mock')) {
            Log::info('[RateHawk:MOCK] suggest', ['query' => $query]);
            return $this->getMockSuggestions($query);
        }

        $cacheKey = 'rh_suggest_' . md5($query . $language);
        $ttl      = config('ratehawk.cache.suggestions', 3600);

        return Cache::remember($cacheKey, $ttl, function () use ($query, $language) {
            return $this->client->post('api/b2b/v3/search/multicomplete/', [
                'query'    => $query,
                'language' => $language,
            ]);
        });
    }

    /**
     * Mock suggestions matching RateHawk multicomplete response format.
     */
    protected function getMockSuggestions(string $query): array
    {
        $destinations = [
            ['id' => 4230, 'name' => 'Madrid', 'type' => 'region',  'country' => 'España', 'hotels_count' => 892],
            ['id' => 4231, 'name' => 'Barcelona', 'type' => 'region', 'country' => 'España', 'hotels_count' => 1205],
            ['id' => 4232, 'name' => 'Málaga', 'type' => 'region',  'country' => 'España', 'hotels_count' => 423],
            ['id' => 4233, 'name' => 'Valencia', 'type' => 'region', 'country' => 'España', 'hotels_count' => 386],
            ['id' => 4234, 'name' => 'Sevilla', 'type' => 'region', 'country' => 'España', 'hotels_count' => 341],
            ['id' => 2100, 'name' => 'Bogotá', 'type' => 'region',  'country' => 'Colombia', 'hotels_count' => 654],
            ['id' => 2101, 'name' => 'Medellín', 'type' => 'region', 'country' => 'Colombia', 'hotels_count' => 412],
            ['id' => 3300, 'name' => 'México D.F.', 'type' => 'region', 'country' => 'México', 'hotels_count' => 987],
            ['id' => 3301, 'name' => 'Cancún', 'type' => 'region', 'country' => 'México', 'hotels_count' => 756],
            ['id' => 1000, 'name' => 'París', 'type' => 'region', 'country' => 'Francia', 'hotels_count' => 2310],
            ['id' => 1001, 'name' => 'Londres', 'type' => 'region', 'country' => 'Reino Unido', 'hotels_count' => 3102],
            ['id' => 1002, 'name' => 'Roma', 'type' => 'region', 'country' => 'Italia', 'hotels_count' => 1876],
            ['id' => 1003, 'name' => 'Nueva York', 'type' => 'region', 'country' => 'EE.UU.', 'hotels_count' => 4231],
            ['id' => 1004, 'name' => 'Miami', 'type' => 'region', 'country' => 'EE.UU.', 'hotels_count' => 1543],
        ];

        $q = mb_strtolower($query);
        $filtered = array_filter($destinations, fn($d) =>
            str_contains(mb_strtolower($d['name']), $q) ||
            str_contains(mb_strtolower($d['country']), $q)
        );

        return [
            'status' => 'ok',
            'data'   => array_values($filtered),
        ];
    }
}
