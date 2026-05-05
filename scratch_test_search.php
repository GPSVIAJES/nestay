<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\RateHawk\HotelSearchService::class);
// Let's test searchByRegion with Dubai (6053839) or Paris (1000)
$params = [
    'region_id' => 6053839,
    'checkin' => date('Y-m-d', strtotime('+5 days')),
    'checkout' => date('Y-m-d', strtotime('+8 days')),
    'adults' => 2,
    'hotel_id' => null,
];
$result = $service->searchByRegion($params);

if (isset($result['data']['hotels'][0])) {
    $firstHotel = $result['data']['hotels'][0];
    echo "First hotel name: " . $firstHotel['name'] . "\n";
    echo "First hotel stars: " . $firstHotel['stars'] . "\n";
    echo "First hotel images count: " . count($firstHotel['images']) . "\n";
    if (count($firstHotel['images']) > 0) echo "First image: " . $firstHotel['images'][0] . "\n";
} else {
    echo "No hotels found.\n";
}

// Test getHotelPage
if (isset($result['data']['hotels'][0])) {
    $hotelId = $result['data']['hotels'][0]['id'];
    $hpResult = $service->getHotelPage($hotelId, $params);
    echo "\nHP Name: " . $hpResult['data']['hotel']['name'] . "\n";
}
