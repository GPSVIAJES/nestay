<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$client = app(App\Services\RateHawk\RateHawkClient::class);
$response = $client->post('api/b2b/v3/hotel/info/dump/', ['language' => 'es']);
echo json_encode($response);
