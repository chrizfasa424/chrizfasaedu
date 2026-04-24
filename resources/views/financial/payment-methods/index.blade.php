@extends('layouts.app')
@section('title', 'Payment Methods')
@section('header', 'Payment Methods')

@section('content')
<div class="space-y-6 max-w-6xl">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Payment Methods</h1>
        <p class="text-sm text-slate-500 mt-0.5">Enable channels and configure online gateway credentials for your school.</p>
    </div>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Please fix the following issues:</p>
            <ul class="mt-1 list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Method</th>
                    <th class="px-5 py-3 text-left">Code</th>
                    <th class="px-5 py-3 text-left">Scope</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($methods as $method)
                    <tr>
                        <td class="px-5 py-3 font-semibold text-slate-800">{{ $method->name }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ $method->code }}</td>
                        <td class="px-5 py-3 text-xs text-slate-500">{{ $method->school_id ? 'School Override' : 'System Default' }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $method->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $method->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <form method="POST" action="{{ route('financial.payment-methods.update', $method) }}" class="inline-flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_active" value="{{ $method->is_active ? 0 : 1 }}">
                                <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                                    {{ $method->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        @php
            $paystack = $gatewayConfigs['paystack'] ?? null;
            $flutterwave = $gatewayConfigs['flutterwave'] ?? null;
        @endphp

        @if($paystack && $paystack['method'])
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Paystack Setup</h2>
                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $paystack['is_active'] ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ $paystack['is_active'] ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-slate-500">Keys are saved as your school override. Leave secret field blank to keep existing secret.</p>

                <form method="POST" action="{{ route('financial.payment-methods.gateway-settings', $paystack['method']) }}" class="mt-4 space-y-3">
                    @csrf
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-600" {{ old('is_active', $paystack['is_active']) ? 'checked' : '' }}>
                        Enable Paystack for students
                    </label>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Public Key</label>
                        <input type="text" name="public_key" value="{{ old('public_key', $paystack['settings']['public_key'] ?? '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Secret Key</label>
                        <input type="text" name="secret_key" value="{{ old('secret_key') }}" placeholder="{{ ($paystack['settings']['secret_key'] ?? '') ? 'Saved: ' . $paystack['settings']['secret_key'] : 'Paste new secret key' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Merchant Email</label>
                        <input type="email" name="merchant_email" value="{{ old('merchant_email', $paystack['settings']['merchant_email'] ?? '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">API Base URL (Optional)</label>
                        <input type="url" name="base_url" value="{{ old('base_url', $paystack['settings']['base_url'] ?? '') }}" placeholder="https://api.paystack.co" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>

                    <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save Paystack Settings</button>
                </form>
            </div>
        @endif

        @if($flutterwave && $flutterwave['method'])
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Flutterwave Setup</h2>
                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $flutterwave['is_active'] ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ $flutterwave['is_active'] ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-slate-500">Keys are saved as your school override. Leave secret/hash fields blank to keep existing values.</p>

                <form method="POST" action="{{ route('financial.payment-methods.gateway-settings', $flutterwave['method']) }}" class="mt-4 space-y-3">
                    @csrf
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-600" {{ old('is_active', $flutterwave['is_active']) ? 'checked' : '' }}>
                        Enable Flutterwave for students
                    </label>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Public Key</label>
                        <input type="text" name="public_key" value="{{ old('public_key', $flutterwave['settings']['public_key'] ?? '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Secret Key</label>
                        <input type="text" name="secret_key" value="{{ old('secret_key') }}" placeholder="{{ ($flutterwave['settings']['secret_key'] ?? '') ? 'Saved: ' . $flutterwave['settings']['secret_key'] : 'Paste new secret key' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Webhook Secret Hash</label>
                        <input type="text" name="secret_hash" value="{{ old('secret_hash') }}" placeholder="{{ ($flutterwave['settings']['secret_hash'] ?? '') ? 'Saved: ' . $flutterwave['settings']['secret_hash'] : 'Paste webhook hash' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Encryption Key (Optional)</label>
                        <input type="text" name="encryption_key" value="{{ old('encryption_key') }}" placeholder="{{ ($flutterwave['settings']['encryption_key'] ?? '') ? 'Saved: ' . $flutterwave['settings']['encryption_key'] : 'Paste encryption key' }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">API Base URL (Optional)</label>
                        <input type="url" name="base_url" value="{{ old('base_url', $flutterwave['settings']['base_url'] ?? '') }}" placeholder="https://api.flutterwave.com/v3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>

                    <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save Flutterwave Settings</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
