<?php

namespace App\Services\RateHawk;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class RateHawkClient
{
    protected string $baseUrl;
    protected string $authHeader;
    protected int $timeout;

    public function __construct()
    {
        $keyId  = config('ratehawk.key_id');
        $apiKey = config('ratehawk.api_key');

        $this->baseUrl    = rtrim(config('ratehawk.base_url'), '/');
        $this->authHeader = 'Basic ' . base64_encode("{$keyId}:{$apiKey}");
        $this->timeout    = (int) config('ratehawk.timeout', 30);
    }

    /**
     * Send a POST request to the RateHawk API.
     */
    public function post(string $endpoint, array $payload = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        Log::info('[RateHawk] POST ' . $endpoint, ['payload_keys' => array_keys($payload)]);

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->authHeader,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post($url, $payload);

            if ($response->failed()) {
                Log::error('[RateHawk] Request failed', [
                    'url'    => $url,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                throw new \RuntimeException(
                    'RateHawk API error: ' . $response->status() . ' — ' . $response->body()
                );
            }

            return $response->json() ?? [];
        } catch (RequestException $e) {
            Log::error('[RateHawk] HTTP exception', ['message' => $e->getMessage()]);
            throw new \RuntimeException('RateHawk connection error: ' . $e->getMessage());
        }
    }

    /**
     * Send a GET request to the RateHawk API.
     */
    public function get(string $endpoint, array $params = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        Log::info('[RateHawk] GET ' . $endpoint);

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->authHeader,
                'Accept'        => 'application/json',
            ])
            ->timeout($this->timeout)
            ->get($url, $params);

            if ($response->failed()) {
                Log::error('[RateHawk] GET failed', [
                    'url'    => $url,
                    'status' => $response->status(),
                ]);
                throw new \RuntimeException('RateHawk API error: ' . $response->status());
            }

            return $response->json() ?? [];
        } catch (RequestException $e) {
            Log::error('[RateHawk] GET exception', ['message' => $e->getMessage()]);
            throw new \RuntimeException('RateHawk connection error: ' . $e->getMessage());
        }
    }
}
