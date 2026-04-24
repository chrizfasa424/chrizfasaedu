<?php

namespace App\Services\Payments\Gateway;

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Crypt;

class GatewaySettingsService
{
    public function effectiveMethod(int $schoolId, string $code): ?PaymentMethod
    {
        $schoolSpecific = PaymentMethod::query()
            ->where('school_id', $schoolId)
            ->where('code', $code)
            ->first();

        if ($schoolSpecific) {
            return $schoolSpecific;
        }

        return PaymentMethod::query()
            ->whereNull('school_id')
            ->where('code', $code)
            ->first();
    }

    public function isMethodActive(int $schoolId, string $code): bool
    {
        $method = $this->effectiveMethod($schoolId, $code);
        return (bool) ($method?->is_active);
    }

    public function resolvedSettings(int $schoolId, string $code): array
    {
        $method = $this->effectiveMethod($schoolId, $code);
        $raw = is_array($method?->settings_json) ? $method->settings_json : [];

        return $this->decryptSecrets($raw, $code);
    }

    public function maskedSettings(int $schoolId, string $code): array
    {
        $settings = $this->resolvedSettings($schoolId, $code);
        $secrets = $this->secretFieldsFor($code);

        foreach ($secrets as $field) {
            if (!empty($settings[$field])) {
                $settings[$field] = $this->maskValue((string) $settings[$field]);
            }
        }

        return $settings;
    }

    public function updateForSchool(int $schoolId, string $code, array $payload): PaymentMethod
    {
        $base = PaymentMethod::query()
            ->whereNull('school_id')
            ->where('code', $code)
            ->first();

        $isActive = array_key_exists('is_active', $payload) ? (bool) $payload['is_active'] : null;
        unset($payload['is_active']);

        $method = PaymentMethod::query()->firstOrNew([
            'school_id' => $schoolId,
            'code' => $code,
        ]);

        if (!$method->exists) {
            $method->name = (string) ($base?->name ?: ucwords(str_replace('_', ' ', $code)));
        }

        if ($isActive !== null) {
            $method->is_active = $isActive;
        } elseif (!$method->exists) {
            $method->is_active = (bool) ($base?->is_active ?? true);
        }

        $existing = is_array($method->settings_json) ? $method->settings_json : [];
        $existingDecrypted = $this->decryptSecrets($existing, $code);
        $merged = array_merge($existingDecrypted, $payload);

        foreach ($this->secretFieldsFor($code) as $secretField) {
            if (array_key_exists($secretField, $payload) && trim((string) $payload[$secretField]) === '') {
                $merged[$secretField] = $existingDecrypted[$secretField] ?? null;
            }
        }

        $method->settings_json = $this->encryptSecrets($merged, $code);
        $method->save();

        return $method->fresh();
    }

    public function methodsWithOverrides(int $schoolId)
    {
        $methods = PaymentMethod::query()
            ->where(function ($query) use ($schoolId) {
                $query->whereNull('school_id')
                    ->orWhere('school_id', $schoolId);
            })
            ->orderByRaw('case when school_id is null then 1 else 0 end')
            ->orderBy('name')
            ->get()
            ->groupBy('code')
            ->map(fn ($items) => $items->first())
            ->values();

        return $methods;
    }

    protected function secretFieldsFor(string $code): array
    {
        return match ($code) {
            'paystack' => ['secret_key'],
            'flutterwave' => ['secret_key', 'secret_hash', 'encryption_key'],
            default => [],
        };
    }

    protected function encryptSecrets(array $settings, string $code): array
    {
        $secrets = $this->secretFieldsFor($code);

        foreach ($secrets as $field) {
            if (!array_key_exists($field, $settings)) {
                continue;
            }

            $value = trim((string) $settings[$field]);
            if ($value === '') {
                $settings[$field] = null;
                continue;
            }

            try {
                $settings[$field] = Crypt::encryptString($value);
                $settings[$field . '_encrypted'] = true;
            } catch (\Throwable) {
                $settings[$field] = $value;
                $settings[$field . '_encrypted'] = false;
            }
        }

        return $settings;
    }

    protected function decryptSecrets(array $settings, string $code): array
    {
        $secrets = $this->secretFieldsFor($code);

        foreach ($secrets as $field) {
            if (!array_key_exists($field, $settings)) {
                continue;
            }

            $value = (string) $settings[$field];
            if ($value === '') {
                $settings[$field] = null;
                continue;
            }

            $isEncrypted = (bool) ($settings[$field . '_encrypted'] ?? true);
            if (!$isEncrypted) {
                continue;
            }

            try {
                $settings[$field] = Crypt::decryptString($value);
            } catch (\Throwable) {
                $settings[$field] = $value;
            }
        }

        return $settings;
    }

    protected function maskValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (strlen($value) <= 8) {
            return str_repeat('*', strlen($value));
        }

        return substr($value, 0, 4) . str_repeat('*', max(4, strlen($value) - 8)) . substr($value, -4);
    }
}
