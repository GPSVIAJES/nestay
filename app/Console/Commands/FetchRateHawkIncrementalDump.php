<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RateHawk\RateHawkClient;
use Illuminate\Support\Facades\Storage;
use App\Models\Hotel;

class FetchRateHawkIncrementalDump extends Command
{
    protected $signature = 'ratehawk:fetch-incremental {--language=es} {--import : Automatically import into database after extracting}';
    protected $description = 'Fetches the incremental hotel dump from RateHawk API and optionally imports it.';

    public function handle(RateHawkClient $client)
    {
        $this->info("Fetching incremental dump URL from RateHawk API...");
        
        $response = $client->post('api/b2b/v3/hotel/info/incremental_dump/', [
            'language' => $this->option('language')
        ]);

        if (empty($response['data']['url'])) {
            if (isset($response['error']) && $response['error'] === 'dump_not_ready') {
                $this->warn("RateHawk is currently compiling/updating the incremental dump on their servers (dump_not_ready).");
                $this->warn("This is normal. Please try running the command again later.");
                return 1;
            }
            
            $this->error("Failed to get incremental dump URL. Response: " . json_encode($response));
            return 1;
        }

        $dumpUrl = $response['data']['url'];
        $this->info("Incremental Dump URL obtained: {$dumpUrl}");

        $storagePath = storage_path('app/dumps');
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        $compressedFile = $storagePath . '/incremental_dump.json.zst';
        $decompressedFile = $storagePath . '/incremental_dump.json';

        $this->info("Downloading incremental dump to {$compressedFile}...");
        
        // Use streaming download to avoid memory issues
        set_time_limit(0);
        $fp = fopen($compressedFile, 'w+');
        $ch = curl_init(str_replace(' ', '%20', $dumpUrl));
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_FILE, $fp); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch); 
        curl_close($ch);
        fclose($fp);

        $this->info("Download complete. Decompressing...");

        // Ensure zstd executable exists
        $zstdPath = storage_path('app/bin/zstd-v1.5.6-win64/zstd.exe');
        if (!file_exists($zstdPath)) {
            $this->error("zstd binary not found at {$zstdPath}. Please run the full dump setup first.");
            return 1;
        }

        // Decompress using zstd
        $command = escapeshellarg($zstdPath) . " -d -f " . escapeshellarg($compressedFile) . " -o " . escapeshellarg($decompressedFile);
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error("Decompression failed with code {$returnVar}. Output: " . implode("\n", $output));
            return 1;
        }

        $this->info("Decompression successful to {$decompressedFile}.");

        // Optional import phase
        if ($this->option('import')) {
            $this->importHotels($decompressedFile);
        } else {
            $this->info("Run with --import flag to import the extracted JSON into the database.");
        }

        return 0;
    }

    protected function importHotels($filePath)
    {
        $this->info("Starting incremental import into database...");
        $handle = fopen($filePath, "r");
        if (!$handle) {
            $this->error("Could not open {$filePath} for reading.");
            return;
        }

        $count = 0;
        $batch = [];
        $batchSize = 1000;

        while (($line = fgets($handle)) !== false) {
            $data = json_decode($line, true);
            if (!$data || !isset($data['id'])) continue;

            $batch[] = [
                'id' => $data['id'],
                'name' => $data['name'] ?? null,
                'description' => $data['description_struct'][0]['paragraphs'][0] ?? null,
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'star_rating' => $data['star_rating'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'region_id' => $data['region_id'] ?? null,
                'images' => isset($data['images']) ? json_encode(array_slice($data['images'], 0, 5)) : null,
                'amenities' => isset($data['amenities']) ? json_encode(array_slice($data['amenities'], 0, 10)) : null,
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                Hotel::upsert($batch, ['id'], [
                    'name', 'description', 'address', 'phone', 'email', 
                    'star_rating', 'latitude', 'longitude', 'region_id', 'images', 'amenities', 'updated_at'
                ]);
                $count += count($batch);
                $this->info("Imported {$count} incremental updates...");
                $batch = [];
            }
        }

        // process remaining
        if (count($batch) > 0) {
            Hotel::upsert($batch, ['id'], [
                'name', 'description', 'address', 'phone', 'email', 
                'star_rating', 'latitude', 'longitude', 'region_id', 'images', 'amenities', 'updated_at'
            ]);
            $count += count($batch);
        }

        fclose($handle);
        $this->info("Incremental import complete. Total: {$count} hotels updated.");
    }
}
