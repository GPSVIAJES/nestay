<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\RateHawk\HotelSearchService::class);
$result = $service->suggest('duba');

echo json_encode($result, JSON_PRETTY_PRINT);
