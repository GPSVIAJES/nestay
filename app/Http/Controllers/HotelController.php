<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HotelController extends Controller
{
    /**
     * Home page with search form.
     */
    public function home()
    {
        $popularDestinations = [
            ['id' => 4230, 'name' => 'Madrid',     'country' => 'España',       'image' => 'https://images.unsplash.com/photo-1539037116277-4db20889f2d4?w=600', 'hotels' => 892],
            ['id' => 4231, 'name' => 'Barcelona',  'country' => 'España',       'image' => 'https://images.unsplash.com/photo-1583422409516-2895a77efded?w=600', 'hotels' => 1205],
            ['id' => 2100, 'name' => 'Bogotá',     'country' => 'Colombia',     'image' => 'https://images.unsplash.com/photo-1614169538396-b65adfd7e83c?w=600', 'hotels' => 654],
            ['id' => 3301, 'name' => 'Cancún',     'country' => 'México',       'image' => 'https://images.unsplash.com/photo-1510097467424-192d713fd8b2?w=600', 'hotels' => 756],
            ['id' => 1000, 'name' => 'París',      'country' => 'Francia',      'image' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=600', 'hotels' => 2310],
            ['id' => 1002, 'name' => 'Roma',       'country' => 'Italia',       'image' => 'https://images.unsplash.com/photo-1525874684015-58379d421a52?w=600', 'hotels' => 1876],
        ];

        return view('pages.home', compact('popularDestinations'));
    }

    /**
     * Search results page.
     */
    public function results(Request $request)
    {
        // Pass search params to view — actual search happens via JS/API
        $searchParams = $request->only(['destination', 'region_id', 'checkin', 'checkout', 'adults', 'children']);
        return view('pages.results', compact('searchParams'));
    }

    /**
     * Hotel detail page.
     */
    public function show(Request $request, string $id)
    {
        $searchParams = $request->only(['checkin', 'checkout', 'adults', 'children']);
        return view('pages.hotel', compact('id', 'searchParams'));
    }
}
