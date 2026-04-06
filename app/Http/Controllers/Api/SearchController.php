<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RateHawk\HotelSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(protected HotelSearchService $searchService)
    {}

    /**
     * Autocomplete: suggest destinations and hotels.
     * GET/POST /api/suggest?q=madrid
     */
    public function suggest(Request $request): JsonResponse
    {
        $request->validate([
            'q'        => 'required|string|min:2|max:100',
            'language' => 'nullable|string|size:2',
        ]);

        $results = $this->searchService->suggest(
            $request->input('q'),
            $request->input('language', 'es')
        );

        return response()->json($results);
    }

    /**
     * Search hotels by region.
     * POST /api/search-hotels
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'region_id' => 'required|integer',
            'checkin'   => 'required|date|after:today',
            'checkout'  => 'required|date|after:checkin',
            'adults'    => 'required|integer|min:1|max:8',
            'children'  => 'nullable|integer|min:0|max:6',
            'rooms'     => 'nullable|integer|min:1|max:5',
            'currency'  => 'nullable|string|size:3',
        ]);

        $results = $this->searchService->searchByRegion($validated);

        return response()->json($results);
    }

    /**
     * Get full hotel detail page with all rates.
     * POST /api/hotel-details
     */
    public function details(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hotel_id' => 'required|string',
            'checkin'  => 'required|date|after:today',
            'checkout' => 'required|date|after:checkin',
            'adults'   => 'required|integer|min:1|max:8',
            'children' => 'nullable|integer|min:0|max:6',
        ]);

        $result = $this->searchService->getHotelPage(
            $validated['hotel_id'],
            $validated
        );

        return response()->json($result);
    }
}
