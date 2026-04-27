<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Config::set('ratehawk.use_mock', false); // Force real API

$client = app(App\Services\RateHawk\RateHawkClient::class);

// 1. Search SERP
$res = $client->post('api/b2b/v3/search/serp/region/', [
    'region_id'   => 4230,
    'checkin'     => '2026-05-01',
    'checkout'    => '2026-05-05',
    'guests'      => [['adults' => 2]],
    'language'    => 'es',
    'currency'    => 'USD',
    'residency'   => 'US',
]);

if (!empty($res['data']['hotels'])) {
    $hotelId = $res['data']['hotels'][0]['id'];
    echo "Found hotel ID: $hotelId\n";
    
    // 2. Fetch Info
    $info = $client->post('api/b2b/v3/hotel/info/', [
        'id'       => $hotelId,
        'language' => 'es'
    ]);
    
    // Output parts of info to understand structure
    if (!empty($info['data'])) {
        $d = $info['data'];
        echo "Name: " . ($d['name'] ?? 'N/A') . "\n";
        echo "Images: " . json_encode(array_slice($d['images'] ?? [], 0, 1)) . "\n";
        echo "Amenities: " . json_encode(array_slice($d['amenity_groups'] ?? $d['amenities'] ?? [], 0, 1)) . "\n";
    } else {
        echo "Info Data empty. " . json_encode($info) . "\n";
    }
} else {
    echo "No hotels found in SERP.\n";
    echo json_encode($res);
}
