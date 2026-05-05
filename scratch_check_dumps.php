<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$client = app(App\Services\RateHawk\RateHawkClient::class);
echo "Checking FULL dump (ES):\n";
$response = $client->post('api/b2b/v3/hotel/info/dump/', ['language' => 'es']);
echo json_encode($response, JSON_PRETTY_PRINT) . "\n\n";

echo "Checking FULL dump (EN):\n";
$response = $client->post('api/b2b/v3/hotel/info/dump/', ['language' => 'en']);
echo json_encode($response, JSON_PRETTY_PRINT) . "\n\n";

echo "Checking INCREMENTAL dump:\n";
$response = $client->post('api/b2b/v3/hotel/info/incremental_dump/', ['language' => 'es']);
echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
