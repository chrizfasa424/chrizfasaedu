<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaystackService
{
    protected string $baseUrl = 'https://api.paystack.co';
    protected string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret');
    }

    public function initializeTransaction(string $email, float $amount, string $reference, string $callbackUrl, array $metadata = []): ?array
    {
        $response = Http::withToken($this->secretKey)->post("{$this->baseUrl}/transaction/initialize", [
            'email' => $email,
            'amount' => (int) ($amount * 100),
            'reference' => $reference,
            'callback_url' => $callbackUrl,
            'metadata' => $metadata,
        ]);

        return $response->successful() ? $response->json('data') : null;
    }

    public function verifyTransaction(string $reference): ?array
    {
        $response = Http::withToken($this->secretKey)->get("{$this->baseUrl}/transaction/verify/{$reference}");
        return $response->successful() ? $response->json('data') : null;
    }

    public function listBanks(): array
    {
        $response = Http::withToken($this->secretKey)->get("{$this->baseUrl}/bank");
        return $response->successful() ? $response->json('data') : [];
    }
}
