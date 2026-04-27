<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$client = app(App\Services\RateHawk\RateHawkClient::class);
$res = $client->post('api/b2b/v3/hotel/info/', ['id' => 'test_hotel', 'language' => 'es']);
echo json_encode($res, JSON_PRETTY_PRINT);
