<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RateHawk\RateHawkClient;
use Illuminate\Support\Facades\Storage;
use App\Models\Hotel;

class FetchRateHawkDump extends Command
{
    protected $signature = 'ratehawk:fetch-dump {--language=es} {--import : Automatically import into database after extracting} {--limit= : Limit the number of hotels to import} {--sync : Delete hotels not present in the new dump}';
    protected $description = 'Fetches the hotel dump from RateHawk API, decompresses it, and optionally imports it.';

    public function handle(RateHawkClient $client)
    {
        $this->info("Fetching dump URL from RateHawk API...");
        
        $response = $client->post('api/b2b/v3/hotel/info/dump/', [
            'language' => $this->option('language')
        ]);

        if (empty($response['data']['url'])) {
            if (isset($response['error']) && $response['error'] === 'dump_not_ready') {
                $this->warn("RateHawk is currently compiling/updating the dump on their servers (dump_not_ready).");
                $this->warn("This is normal. Please try running the command again in a few hours.");
                return 1;
            }
            
            $this->error("Failed to get dump URL. Response: " . json_encode($response));
            return 1;
        }

        $dumpUrl = $response['data']['url'];
        $this->info("Dump URL obtained: {$dumpUrl}");

        $storagePath = storage_path('app/dumps');
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        $compressedFile = $storagePath . '/dump.json.zst';
        $decompressedFile = $storagePath . '/dump.json';

        $this->info("Downloading dump to {$compressedFile}...");
        
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
            $this->error("zstd binary not found at {$zstdPath}. Please install it to decompress the dump.");
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
        
        $syncStartTime = now();

        // Optional import phase
        if ($this->option('import')) {
            $this->importHotels($decompressedFile);
            
            // Sync: Delete hotels that were NOT touched by this import
            if ($this->option('sync') && !$this->option('limit')) {
                $this->info("Syncing: Deleting hotels that are no longer in the RateHawk dump...");
                $deletedCount = Hotel::where('updated_at', '<', $syncStartTime)->delete();
                $this->info("Cleanup complete: {$deletedCount} old hotels removed.");
            }
        } else {
            $this->info("Run with --import flag to import the extracted JSON into the database.");
        }

        return 0;
    }

    protected function importHotels($filePath)
    {
        $this->info("Starting import into database...");
        $handle = fopen($filePath, "r");
        if (!$handle) {
            $this->error("Could not open {$filePath} for reading.");
            return;
        }

        $count = 0;
        $batch = [];
        $batchSize = 1000;
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        while (($line = fgets($handle)) !== false) {
            $data = json_decode($line, true);
            if (!$data || !isset($data['id'])) continue;

            $batch[] = [
                'id' => $data['id'],
                'name' => $data['name'] ?? null,
                'city' => $data['region']['name'] ?? null,
                'country' => $data['region']['country_code'] ?? null,
                'description' => $data['description_struct'][0]['paragraphs'][0] ?? null,
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'star_rating' => $data['star_rating'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'region_id' => $data['region']['id'] ?? null,
                'images' => isset($data['images']) ? json_encode(array_slice($data['images'], 0, 5)) : null,
                'amenities' => isset($data['amenities']) ? json_encode(array_slice($data['amenities'], 0, 10)) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize || ($limit && ($count + count($batch)) >= $limit)) {
                // If we have a limit and the batch would exceed it, slice it
                if ($limit && ($count + count($batch)) > $limit) {
                    $needed = $limit - $count;
                    $batch = array_slice($batch, 0, $needed);
                }

                Hotel::upsert($batch, ['id'], [
                    'name', 'city', 'country', 'description', 'address', 'phone', 'email', 
                    'star_rating', 'latitude', 'longitude', 'region_id', 'images', 'amenities', 'updated_at'
                ]);
                $count += count($batch);
                $this->info("Imported {$count} hotels...");
                $batch = [];

                if ($limit && $count >= $limit) {
                    break;
                }
            }
        }

        // process remaining
        if (count($batch) > 0 && (!$limit || $count < $limit)) {
            Hotel::upsert($batch, ['id'], [
                'name', 'city', 'country', 'description', 'address', 'phone', 'email', 
                'star_rating', 'latitude', 'longitude', 'region_id', 'images', 'amenities', 'updated_at'
            ]);
            $count += count($batch);
        }

        fclose($handle);
        $this->info("Import complete. Total: {$count} hotels.");
    }
}
