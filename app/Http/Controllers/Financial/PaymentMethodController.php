<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\UpdateGatewaySettingsRequest;
use App\Http\Requests\Financial\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use App\Services\Payments\Gateway\GatewaySettingsService;
use Illuminate\Support\Facades\DB;

class PaymentMethodController extends Controller
{
    protected function authorizeFinanceUser(): void
    {
        $user = auth()->user();

        abort_unless($user && in_array((string) ($user->role?->value ?? ''), [
            'super_admin',
            'school_admin',
            'principal',
            'vice_principal',
            'accountant',
        ], true), 403, 'Unauthorized access.');
    }

    public function index(GatewaySettingsService $gatewaySettings)
    {
        $this->authorizeFinanceUser();

        $schoolId = (int) auth()->user()->school_id;
        $methods = $gatewaySettings->methodsWithOverrides($schoolId);

        $gatewayConfigs = [
            'paystack' => [
                'method' => $methods->firstWhere('code', 'paystack'),
                'settings' => $gatewaySettings->maskedSettings($schoolId, 'paystack'),
                'is_active' => $gatewaySettings->isMethodActive($schoolId, 'paystack'),
            ],
            'flutterwave' => [
                'method' => $methods->firstWhere('code', 'flutterwave'),
                'settings' => $gatewaySettings->maskedSettings($schoolId, 'flutterwave'),
                'is_active' => $gatewaySettings->isMethodActive($schoolId, 'flutterwave'),
            ],
        ];

        return view('financial.payment-methods.index', compact('methods', 'gatewayConfigs'));
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        $this->authorizeFinanceUser();
        $schoolId = (int) auth()->user()->school_id;

        if ($paymentMethod->school_id && (int) $paymentMethod->school_id !== $schoolId) {
            abort(403, 'Unauthorized payment method access.');
        }

        $payload = ['is_active' => (bool) $request->boolean('is_active')];

        if ($paymentMethod->school_id) {
            $paymentMethod->update($payload);
            return back()->with('success', 'Payment method updated.');
        }

        DB::transaction(function () use ($paymentMethod, $payload, $schoolId) {
            $custom = PaymentMethod::query()->firstOrNew([
                'school_id' => $schoolId,
                'code' => (string) $paymentMethod->code,
            ]);

            $custom->name = $paymentMethod->name;
            $custom->is_active = $payload['is_active'];
            $custom->settings_json = $paymentMethod->settings_json;
            $custom->save();
        });

        return back()->with('success', 'Payment method updated.');
    }

    public function updateGatewaySettings(
        UpdateGatewaySettingsRequest $request,
        PaymentMethod $paymentMethod,
        GatewaySettingsService $gatewaySettings
    ) {
        $this->authorizeFinanceUser();

        $schoolId = (int) auth()->user()->school_id;

        if ($paymentMethod->school_id && (int) $paymentMethod->school_id !== $schoolId) {
            abort(403, 'Unauthorized payment method access.');
        }

        abort_unless(in_array((string) $paymentMethod->code, ['paystack', 'flutterwave'], true), 422, 'This payment method does not support gateway keys.');

        $payload = $request->validated();
        $payload['is_active'] = $request->boolean('is_active');

        $gatewaySettings->updateForSchool($schoolId, (string) $paymentMethod->code, $payload);

        return back()->with('success', ucfirst((string) $paymentMethod->name) . ' settings saved successfully.');
    }
}

