<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hotel = \App\Models\Hotel::first();
if ($hotel) {
    echo "First Hotel:\n";
    echo json_encode($hotel->toArray(), JSON_PRETTY_PRINT) . "\n";
    echo "Count: " . \App\Models\Hotel::count() . "\n";
} else {
    echo "No hotels found.\n";
}
