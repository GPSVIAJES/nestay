<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RateHawk\BookingService;
use App\Services\RateHawk\RateHawkClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

Config::set('ratehawk.use_mock', false); // Force real API

$client = app(RateHawkClient::class);
$bookingService = app(BookingService::class);

echo "========================================\n";
echo "  RATEHAWK END-TO-END BOOKING TEST (SANDBOX)\n";
echo "========================================\n\n";

// 1. SEARCH (SERP)
echo "[1] Searching hotels in Madrid (Region 4230)...\n";
$serp = $client->post('api/b2b/v3/search/serp/region/', [
    'region_id' => 4230,
    'checkin' => '2026-05-01',
    'checkout' => '2026-05-05',
    'guests' => [['adults' => 2]],
    'language' => 'es',
    'currency' => 'USD',
    'residency' => 'US',
]);

if (empty($serp['data']['hotels'])) {
    die("Error: No hotels found or API error: " . json_encode($serp) . "\n");
}
$hotel = $serp['data']['hotels'][0];
$hotelId = $hotel['id'];
echo "    -> Found Hotel: " . $hotel['name'] . " ($hotelId)\n\n";

// 2. HOTEL PAGE (Rates)
echo "[2] Fetching rates for hotel $hotelId (HotelPage)...\n";
$hp = $client->post('api/b2b/v3/search/hp/', [
    'id' => $hotelId,
    'checkin' => '2026-05-01',
    'checkout' => '2026-05-05',
    'guests' => [['adults' => 2]],
    'language' => 'es',
    'currency' => 'USD',
    'residency' => 'US',
]);

if (empty($hp['data']['hotels'][0]['rates'])) {
    die("Error: No rates found for this hotel.\n");
}
$rate = $hp['data']['hotels'][0]['rates'][0];
$bookHash = $rate['book_hash'];
echo "    -> Found Rate: " . $rate['room_name'] . " - " . $rate['payment_options']['payment_types'][0]['show_amount'] . " USD\n";
echo "    -> Initial Book Hash: $bookHash\n\n";

// 3. PREBOOK
echo "[3] Prebooking the rate...\n";
$prebook = $bookingService->prebook($bookHash);
if (($prebook['status'] ?? '') !== 'ok') {
    die("Error in Prebook: " . json_encode($prebook) . "\n");
}
$newBookHash = $prebook['data']['price_changes']['book_hash'] ?? $bookHash;
echo "    -> Prebook OK. Validated Hash: $newBookHash\n\n";

// 4. BOOKING FORM
echo "[4] Creating Booking Form...\n";
$partnerOrderId = 'test-' . time() . '-' . Str::random(5);
$form = $bookingService->createBookingForm($newBookHash, $partnerOrderId);
if (($form['status'] ?? '') !== 'ok') {
    die("Error in Booking Form: " . json_encode($form) . "\n");
}
echo "    -> Form created! Partner Order ID: $partnerOrderId\n\n";

// 5. START BOOKING (FINISH)
echo "[5] Starting the Booking process...\n";
$bookingData = [
    'partner_order_id' => $partnerOrderId,
    'book_hash' => $newBookHash,
    'hotel_id' => $hotelId,
    'hotel_name' => $hotel['name'],
    'check_in' => '2026-05-01',
    'check_out' => '2026-05-05',
    'guests' => 2,
    'currency' => 'USD',
    'total_price' => $rate['payment_options']['payment_types'][0]['show_amount'],
    'guest' => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'phone' => '+1234567890'
    ]
];
$book = $bookingService->startBooking($bookingData, null);
if (($book['status'] ?? '') !== 'ok') {
    die("Error starting booking: " . json_encode($book) . "\n");
}
echo "    -> Booking sent to supplier! Status: Processing\n\n";

// 6. CHECK BOOKING STATUS (POLLING)
echo "[6] Checking booking status (Polling)...\n";
for ($i = 1; $i <= 10; $i++) {
    $status = $bookingService->pollBookingStatus($partnerOrderId);
    $apiStatus = $status['data']['status'] ?? 'unknown';
    echo "    -> Polling attempt $i: Status is '$apiStatus'\n";
    
    if ($apiStatus === 'ok') {
        echo "\n✅ BOOKING SUCCESSFUL! (RateHawk responded with OK)\n";
        break;
    } elseif (in_array($apiStatus, ['failed', 'cancelled'])) {
        echo "\n❌ BOOKING FAILED! Error: " . ($status['error'] ?? 'unknown') . "\n";
        break;
    }
    
    sleep(2);
}

echo "========================================\n";
echo "  TEST COMPLETE\n";
echo "========================================\n";
