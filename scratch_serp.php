<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$svc = app(App\Services\RateHawk\HotelSearchService::class);
$res = $svc->searchByRegion(['region_id' => 4230, 'checkin' => '2026-05-01', 'checkout' => '2026-05-05', 'adults' => 2]);
echo json_encode($res, JSON_PRETTY_PRINT);
