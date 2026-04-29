<?php

use App\Models\User;
use Illuminate\Support\Facades\Validator;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$email = 'lumokito@mailinator.com';

$data = [
    'name' => 'Test',
    'email' => $email,
    'password' => 'password',
    'password_confirmation' => 'password',
];

$validator = Validator::make($data, [
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    'password' => ['required', 'confirmed'],
]);

if ($validator->fails()) {
    echo "Validation failed as expected:\n";
    print_r($validator->errors()->all());
} else {
    echo "Validation PASSED (This is the bug!)\n";
}
