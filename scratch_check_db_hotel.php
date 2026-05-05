<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hotel = \App\Models\Hotel::first();
echo json_encode($hotel->toArray(), JSON_PRETTY_PRINT);
