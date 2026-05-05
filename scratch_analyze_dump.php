<?php
$handle = fopen('storage/app/dumps/dump.json', 'r');
$stats = [];
for ($i = 0; $i < 1000; $i++) {
    $line = fgets($handle);
    if (!$line) break;
    $data = json_decode($line, true);
    if (!$data) continue;
    
    $city = $data['region']['name'] ?? 'Unknown';
    $country = $data['region']['country_code'] ?? 'Unknown';
    
    if (!isset($stats[$country])) $stats[$country] = [];
    if (!isset($stats[$country][$city])) $stats[$country][$city] = 0;
    $stats[$country][$city]++;
}
fclose($handle);

echo json_encode($stats, JSON_PRETTY_PRINT);
