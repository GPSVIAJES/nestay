<?php

$url = 'https://github.com/facebook/zstd/releases/download/v1.5.6/zstd-v1.5.6-win64.zip';
$zipFile = __DIR__ . '/storage/app/zstd.zip';
$extractTo = __DIR__ . '/storage/app/bin/';

if (!is_dir($extractTo)) {
    mkdir($extractTo, 0777, true);
}

echo "Downloading zstd...\n";
file_put_contents($zipFile, file_get_contents($url));

echo "Extracting zstd...\n";
$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo($extractTo);
    $zip->close();
    echo "Extracted to $extractTo\n";
} else {
    echo "Failed to extract\n";
}

// move zstd.exe to bin
rename($extractTo . 'zstd.exe', $extractTo . 'zstd_main.exe');
copy($extractTo . 'zstd-v1.5.6-win64/zstd.exe', $extractTo . 'zstd.exe');
unlink($zipFile);
echo "Done.\n";
