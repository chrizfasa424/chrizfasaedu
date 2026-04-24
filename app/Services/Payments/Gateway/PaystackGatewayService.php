<?php

namespace App\Services\Payments\Gateway;

use Illuminate\Support\Facades\Http;
use Throwable;

class PaystackGatewayService
{
    public function __construct(private readonly GatewaySettingsService $gatewaySettings)
    {
    }

    public function initialize(array $payload, ?int $schoolId = null): array
    {
        try {
            $response = $this->requestClient($schoolId)
                ->post($this->baseUrl($schoolId) . '/transaction/initialize', $payload);
        } catch (Throwable $exception) {
            return $this->failedResponse($exception);
        }

        return [
            'ok' => $response->successful() && (bool) $response->json('status'),
            'status' => $response->status(),
            'data' => $response->json('data') ?? [],
            'raw' => $response->json() ?? [],
        ];
    }

    public function verify(string $reference, ?int $schoolId = null): array
    {
        $url = $this->baseUrl($schoolId) . '/transaction/verify/' . urlencode($reference);
        try {
            $response = $this->requestClient($schoolId)->get($url);
        } catch (Throwable $exception) {
            return $this->failedResponse($exception);
        }

        $data = $response->json('data') ?? [];
        $isSuccess = $response->successful()
            && (bool) $response->json('status')
            && strtolower((string) ($data['status'] ?? '')) === 'success';

        return [
            'ok' => $isSuccess,
            'status' => $response->status(),
            'data' => $data,
            'raw' => $response->json() ?? [],
        ];
    }

    public function validWebhookSignature(string $payload, ?string $signature, ?int $schoolId = null): bool
    {
        if (!$signature) {
            return false;
        }

        $secret = $this->secretKey($schoolId);
        if ($secret === '') {
            return false;
        }

        $computed = hash_hmac('sha512', $payload, $secret);

        return hash_equals($computed, $signature);
    }

    protected function baseUrl(?int $schoolId = null): string
    {
        $settings = $schoolId ? $this->gatewaySettings->resolvedSettings($schoolId, 'paystack') : [];
        $url = (string) ($settings['base_url'] ?? config('services.paystack.url', 'https://api.paystack.co'));

        return rtrim($url, '/');
    }

    protected function secretKey(?int $schoolId = null): string
    {
        $settings = $schoolId ? $this->gatewaySettings->resolvedSettings($schoolId, 'paystack') : [];

        return (string) ($settings['secret_key'] ?? config('services.paystack.secret', ''));
    }

    protected function requestClient(?int $schoolId = null)
    {
        $settings = $schoolId ? $this->gatewaySettings->resolvedSettings($schoolId, 'paystack') : [];
        $verify = $this->resolveVerifyOption($settings);

        return Http::withToken($this->secretKey($schoolId))
            ->acceptJson()
            ->timeout(30)
            ->withOptions(['verify' => $verify]);
    }

    protected function resolveVerifyOption(array $settings): bool|string
    {
        $caBundle = trim((string) ($settings['ca_bundle'] ?? config('services.payment_gateways.ca_bundle', '')));
        if ($caBundle !== '') {
            return $caBundle;
        }

        $configured = $settings['ssl_verify'] ?? config('services.payment_gateways.ssl_verify');
        if (is_bool($configured)) {
            return $configured;
        }

        if (is_numeric($configured)) {
            return (bool) ((int) $configured);
        }

        if (is_string($configured)) {
            $normalized = strtolower(trim($configured));
            if (in_array($normalized, ['1', 'true', 'yes', 'on'], true)) {
                return true;
            }
            if (in_array($normalized, ['0', 'false', 'no', 'off'], true)) {
                return false;
            }
        }

        return !app()->environment(['local', 'testing']);
    }

    protected function failedResponse(Throwable $exception): array
    {
        return [
            'ok' => false,
            'status' => 0,
            'data' => [],
            'raw' => [
                'error' => class_basename($exception),
                'message' => $exception->getMessage(),
            ],
        ];
    }
}
