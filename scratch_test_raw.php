<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\RateHawk\HotelSearchService::class);
$params = [
    'region_id' => 6053839,
    'checkin' => date('Y-m-d', strtotime('+5 days')),
    'checkout' => date('Y-m-d', strtotime('+8 days')),
    'adults' => 2,
    'hotel_id' => null,
];
$raw = Cache::remember('rh_search_' . md5(json_encode($params)), 300, function () use ($params, $service) {
    return (new \ReflectionClass($service))->getProperty('client')->getValue($service)->post('api/b2b/v3/search/serp/region/', [
        'region_id' => $params['region_id'],
        'checkin' => $params['checkin'],
        'checkout' => $params['checkout'],
        'guests' => [['adults' => $params['adults'] ?? 2]],
        'language' => 'en',
        'currency' => 'USD',
        'residency' => 'US',
    ]);
});
var_dump($raw['data']['hotels'][0]);
