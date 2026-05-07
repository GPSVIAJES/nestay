<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestHotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Hotel::updateOrCreate(
            ['id' => '8473727'],
            [
                'name' => 'Test Hotel (Do Not Book)',
                'city' => 'Test City',
                'country' => 'US',
                'description' => 'Mandatory test hotel for ETG API certification.',
                'address' => '123 Test Street',
                'star_rating' => 4,
                'latitude' => 0.0,
                'longitude' => 0.0,
                'region_id' => 1,
                'images' => json_encode(['https://placehold.co/600x400?text=Test+Hotel']),
                'amenities' => json_encode(['WiFi', 'Test']),
            ]
        );
    }
}
