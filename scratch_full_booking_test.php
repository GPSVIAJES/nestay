<?php
/**
 * ============================================================
 * ETG / RateHawk API Certification — Full Booking Test
 * ============================================================
 * Requirements:
 *   - Room 1: 2 Adults + 1 Child (age 5)
 *   - Room 2: 2 Adults
 *   - Hotel:  test_hotel_do_not_book (hid 8473727)
 *   - Residency: uz
 * ============================================================
 */
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\RateHawk\BookingService;
use App\Services\RateHawk\RateHawkClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

// Force real API (never mock for certification)
Config::set('ratehawk.use_mock', false);

$client         = app(RateHawkClient::class);
$bookingService = app(BookingService::class);

// ── CERTIFICATION PARAMETERS ──────────────────────────────────
$TEST_HOTEL_ID = 'test_hotel_do_not_book';
$CHECKIN       = date('Y-m-d', strtotime('+30 days'));   // 30 days from now
$CHECKOUT      = date('Y-m-d', strtotime('+32 days'));   // 2-night stay
$RESIDENCY     = 'uz';
$CURRENCY      = 'USD';
$LANGUAGE      = 'en';

// Room 1: 2 adults + 1 child (age 5)
// Room 2: 2 adults
$GUESTS = [
    ['adults' => 2, 'children' => [5]],  // Room 1 with child age 5
    ['adults' => 2, 'children' => []],   // Room 2
];

// ── HELPERS ───────────────────────────────────────────────────
function section(string $title): void {
    echo "\n" . str_repeat('─', 60) . "\n";
    echo "  $title\n";
    echo str_repeat('─', 60) . "\n";
}

function ok(string $msg): void   { echo "  ✅  $msg\n"; }
function info(string $msg): void { echo "  ℹ️   $msg\n"; }
function fail(string $msg): void { echo "  ❌  $msg\n"; }
function dump(mixed $data): void { echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n"; }

echo "\n";
echo str_repeat('═', 60) . "\n";
echo "  ETG API CERTIFICATION — MULTIROOM + CHILD BOOKING TEST\n";
echo "  Hotel: $TEST_HOTEL_ID\n";
echo "  Checkin: $CHECKIN  →  Checkout: $CHECKOUT\n";
echo "  Room 1: 2 Adults + 1 Child (age 5)\n";
echo "  Room 2: 2 Adults\n";
echo "  Residency: $RESIDENCY\n";
echo str_repeat('═', 60) . "\n";

// ============================================================
// STEP 1 — HOTEL PAGE (get rates for the test hotel directly)
// ============================================================
section('STEP 1 — HOTEL PAGE (/search/hp/)');
info("Fetching rates for test hotel...");

$hp = $client->post('api/b2b/v3/search/hp/', [
    'id'        => $TEST_HOTEL_ID,
    'checkin'   => $CHECKIN,
    'checkout'  => $CHECKOUT,
    'guests'    => $GUESTS,
    'language'  => $LANGUAGE,
    'currency'  => $CURRENCY,
    'residency' => $RESIDENCY,
]);

echo "\n  RAW Hotel Page Response:\n";
dump($hp);

if (empty($hp['data']['hotels'][0]['rates'])) {
    fail("No rates available for test hotel. Full response above.");
    fail("Make sure your API credentials are valid and sandbox is accessible.");
    exit(1);
}

$hotelData  = $hp['data']['hotels'][0];
$rate       = $hotelData['rates'][0];
$bookHash   = $rate['book_hash'];
$rateAmount = $rate['payment_options']['payment_types'][0]['show_amount'] ?? 0;
$rateCurr   = $rate['payment_options']['payment_types'][0]['show_currency_code'] ?? $CURRENCY;

ok("Hotel Page OK. Found " . count($hotelData['rates']) . " rate(s).");
ok("Selected rate:  {$rate['room_name']}");
ok("Price:          $rateAmount $rateCurr");
ok("Initial hash:   $bookHash");

// ============================================================
// STEP 2 — PREBOOK (/hotel/prebook/)
// ============================================================
section('STEP 2 — PREBOOK (/hotel/prebook/)');
info("Prebooking rate with price_increase_percent=0...");

$prebook = $bookingService->prebook($bookHash, 0);

echo "\n  RAW Prebook Response:\n";
dump($prebook);

if (($prebook['status'] ?? '') !== 'ok') {
    fail("Prebook failed. See response above.");
    exit(1);
}

// ETG returns the confirmed hash in data.book_hash or data.price_changes.book_hash
$newBookHash   = $prebook['data']['book_hash']
    ?? $prebook['data']['price_changes']['book_hash']
    ?? $bookHash;

$prebookPrice  = $prebook['data']['price_changes']['new_price']['show_amount']
    ?? $rateAmount;

$priceChanged  = ($prebookPrice != $rateAmount);

ok("Prebook OK.");
ok("Confirmed hash: $newBookHash");
if ($priceChanged) {
    echo "  ⚠️   PRICE CHANGED: was $rateAmount → now $prebookPrice $rateCurr (user would be notified in UI)\n";
} else {
    ok("Price unchanged: $prebookPrice $rateCurr");
}

// ============================================================
// STEP 3 — BOOKING FORM (/order/booking/form/)
// ============================================================
section('STEP 3 — BOOKING FORM (/order/booking/form/)');
$partnerOrderId = (string) Str::uuid();
info("Creating booking form with partner_order_id: $partnerOrderId");

$form = $bookingService->createBookingForm($newBookHash, $partnerOrderId);

echo "\n  RAW Booking Form Response:\n";
dump($form);

if (($form['status'] ?? '') !== 'ok') {
    fail("Booking Form failed. Error: " . ($form['error'] ?? 'unknown'));
    exit(1);
}

ok("Booking Form created successfully.");
ok("Partner Order ID: $partnerOrderId");

// ============================================================
// STEP 4 — START BOOKING (/order/booking/finish/)
// ============================================================
section('STEP 4 — START BOOKING (/order/booking/finish/)');
info("Submitting guest details and triggering booking...");

// Build the rooms/guests array exactly as ETG requires:
//   Room 1: 2 adults (named) + 1 child (with age)
//   Room 2: 2 adults (named)
$bookingData = [
    'partner_order_id' => $partnerOrderId,
    'book_hash'        => $newBookHash,
    'hotel_id'         => $TEST_HOTEL_ID,
    'hotel_name'       => $hotelData['id'] ?? $TEST_HOTEL_ID,
    'hotel_city'       => 'Test City',
    'hotel_country'    => 'Test Country',
    'check_in'         => $CHECKIN,
    'check_out'        => $CHECKOUT,
    'guests'           => 4,    // total adults
    'children'         => 1,    // total children
    'currency'         => $CURRENCY,
    'total_price'      => $prebookPrice,
    'cancellation_policy' => json_encode($rate['cancellation_info'] ?? []),

    // Lead guest (for local DB)
    'guest' => [
        'first_name' => 'Ivan',
        'last_name'  => 'Petrov',
        'email'      => 'test@nestay.com',
        'phone'      => '+998901234567',
    ],

    // Per-room guest list (for ETG API payload)
    'rooms' => [
        // Room 1: 2 adults + 1 child age 5
        [
            'guests' => [
                ['first_name' => 'Ivan',   'last_name' => 'Petrov'],
                ['first_name' => 'Maria',  'last_name' => 'Petrova'],
                ['first_name' => 'Alyosha','last_name' => 'Petrov', 'is_child' => true, 'age' => 5],
            ]
        ],
        // Room 2: 2 adults
        [
            'guests' => [
                ['first_name' => 'Dmitri', 'last_name' => 'Smirnov'],
                ['first_name' => 'Elena',  'last_name' => 'Smirnova'],
            ]
        ],
    ],
];

$book = $bookingService->startBooking($bookingData, null);

echo "\n  RAW Booking Finish Response:\n";
dump($book);

$bookStatus = $book['status'] ?? '';
$bookError  = $book['error']  ?? '';

if (!in_array($bookStatus, ['ok', 'processing']) && empty($bookError)) {
    fail("Booking Finish returned unexpected status. See response above.");
    exit(1);
}

ok("Booking Finish sent. Status: $bookStatus");

// ============================================================
// STEP 5 — POLL STATUS (/order/booking/finish/status/)
// ============================================================
section('STEP 5 — POLLING STATUS (/order/booking/finish/status/)');
info("Polling every 3 seconds (max 20 attempts = 60s)...\n");

$finalStatus = 'pending';
$orderId     = null;
$MAX_POLLS   = 20;
$terminalErrors = ['soldout','book_limit','provider','not_allowed',
                   'booking_finish_did_not_succeed','block','charge','3ds'];

for ($i = 1; $i <= $MAX_POLLS; $i++) {
    $poll       = $bookingService->pollBookingStatus($partnerOrderId);
    $pollStatus = $poll['data']['status'] ?? '';
    $pollError  = $poll['error']          ?? '';
    $orderId    = $poll['data']['order_id'] ?? $orderId;

    echo "  [{$i}/{$MAX_POLLS}] Status: '$pollStatus'" . ($pollError ? " | Error: '$pollError'" : '') . "\n";

    if ($pollStatus === 'ok' || $pollStatus === 'confirmed') {
        $finalStatus = 'confirmed';
        break;
    }

    if (in_array($pollError, $terminalErrors) || in_array($pollStatus, ['failed', 'cancelled'])) {
        $finalStatus = 'failed';
        break;
    }

    if ($i < $MAX_POLLS) sleep(3);
}

// ============================================================
// FINAL RESULT
// ============================================================
echo "\n" . str_repeat('═', 60) . "\n";
if ($finalStatus === 'confirmed') {
    ok("BOOKING CONFIRMED! ✅");
    ok("Partner Order ID: $partnerOrderId");
    ok("ETG Order ID:     " . ($orderId ?? 'Check ETG dashboard'));
    echo "\n";
    echo "  ★  Send this to ETG in the checklist:\n";
    echo "     Partner Order ID: $partnerOrderId\n";
    if ($orderId) {
        echo "     ETG Order ID:     $orderId\n";
    }
} elseif ($finalStatus === 'failed') {
    fail("BOOKING FAILED after polling.");
    info("Check ETG sandbox dashboard for the order: $partnerOrderId");
} else {
    echo "  ⏳  Still processing after $MAX_POLLS polls.\n";
    info("Partner Order ID: $partnerOrderId");
    info("Poll manually at: /api/b2b/v3/hotel/order/booking/finish/status/");
}
echo str_repeat('═', 60) . "\n\n";
