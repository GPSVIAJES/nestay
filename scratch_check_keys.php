<?php
$handle = fopen('storage/app/dumps/dump.json', 'r');
$line = fgets($handle);
$data = json_decode($line, true);
echo "Keys: " . implode(', ', array_keys($data)) . "\n";
if (isset($data['region'])) {
    echo "Region: " . json_encode($data['region']) . "\n";
}
fclose($handle);
