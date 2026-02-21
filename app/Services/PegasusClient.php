<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PegasusClient
{
    protected string $baseUrl;

    protected string $apiKey;

    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('pacman.pegasus.base_url', ''), '/');
        $this->apiKey = config('pacman.pegasus.api_key', '');
        $this->timeout = config('pacman.pegasus.timeout', 30);
    }

    /**
     * Get full voucher information: tracking status, details, and pricing.
     *
     * @return array{status: string, details: array, pricing: array}
     */
    public function getVoucher(string $code): array
    {
        if (! $this->baseUrl) {
            return [
                'error' => false,
                'message' => 'Pegasus integration pending — API credentials not configured.',
                'voucher_code' => $code,
            ];
        }

        $response = Http::timeout($this->timeout)
            ->withHeaders(['Authorization' => "Bearer {$this->apiKey}"])
            ->get("{$this->baseUrl}/vouchers/{$code}");

        $response->throw();

        return $response->json();
    }

    /**
     * Get voucher tracking status only.
     *
     * @return array{status: string, last_update: string}
     */
    public function getVoucherStatus(string $code): array
    {
        if (! $this->baseUrl) {
            return [
                'error' => false,
                'message' => 'Pegasus integration pending — API credentials not configured.',
                'voucher_code' => $code,
            ];
        }

        $response = Http::timeout($this->timeout)
            ->withHeaders(['Authorization' => "Bearer {$this->apiKey}"])
            ->get("{$this->baseUrl}/vouchers/{$code}/status");

        $response->throw();

        return $response->json();
    }
}
