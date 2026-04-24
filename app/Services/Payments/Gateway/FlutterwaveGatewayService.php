<?php

namespace App\Services\Payments\Gateway;

use Illuminate\Support\Facades\Http;
use Throwable;

class FlutterwaveGatewayService
{
    public function __construct(private readonly GatewaySettingsService $gatewaySettings)
    {
    }

    public function initialize(array $payload, ?int $schoolId = null): array
    {
        try {
            $response = $this->requestClient($schoolId)
                ->post($this->baseUrl($schoolId) . '/payments', $payload);
        } catch (Throwable $exception) {
            return $this->failedResponse($exception);
        }

        $raw = $response->json() ?? [];

        return [
            'ok' => $response->successful() && in_array((string) ($raw['status'] ?? ''), ['success', 'successful'], true),
            'status' => $response->status(),
            'data' => $raw['data'] ?? [],
            'raw' => $raw,
        ];
    }

    public function verifyTransaction(string $transactionId, ?int $schoolId = null): array
    {
        try {
            $response = $this->requestClient($schoolId)
                ->get($this->baseUrl($schoolId) . '/transactions/' . urlencode($transactionId) . '/verify');
        } catch (Throwable $exception) {
            return $this->failedResponse($exception);
        }

        $raw = $response->json() ?? [];
        $data = $raw['data'] ?? [];

        $isSuccess = $response->successful()
            && in_array((string) ($raw['status'] ?? ''), ['success', 'successful'], true)
            && in_array(strtolower((string) ($data['status'] ?? '')), ['successful', 'completed'], true);

        return [
            'ok' => $isSuccess,
            'status' => $response->status(),
            'data' => $data,
            'raw' => $raw,
        ];
    }

    public function validWebhookSignature(?string $signature, ?int $schoolId = null): bool
    {
        $expected = $this->secretHash($schoolId);

        if ($expected === '' || !$signature) {
            return false;
        }

        return hash_equals($expected, $signature);
    }

    protected function baseUrl(?int $schoolId = null): string
    {
        $settings = $schoolId ? $this->gatewaySettings->resolvedSettings($schoolId, 'flutterwave') : [];
        $url = (string) ($settings['base_url'] ?? config('services.flutterwave.url', 'https://api.flutterwave.com/v3'));

        return rtrim($url, '/');
    }

    protected function secretKey(?int $schoolId = null): string
    {
        $settings = $schoolId ? $this->gatewaySettings->resolvedSettings($schoolId, 'flutterwave') : [];

        return (string) ($settings['secret_key'] ?? config('services.flutterwave.secret', ''));
    }

    protected function secretHash(?int $schoolId = null): string
    {
        $settings = $schoolId ? $this->gatewaySettings->resolvedSettings($schoolId, 'flutterwave') : [];

        return (string) ($settings['secret_hash'] ?? config('services.flutterwave.hash', ''));
    }

    protected function requestClient(?int $schoolId = null)
    {
        $settings = $schoolId ? $this->gatewaySettings->resolvedSettings($schoolId, 'flutterwave') : [];
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
