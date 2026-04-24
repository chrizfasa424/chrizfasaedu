@php
    $system = $school->settings ?? [];
    $smtp = $system['smtp'] ?? [];
    $smtpEncryption = old('smtp_encryption', $smtp['encryption'] ?? 'tls');

    $labelClass = 'mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-500';
    $fieldClass = 'w-full h-11 rounded-xl border border-slate-300 bg-white px-3.5 text-sm text-slate-800 shadow-sm placeholder:text-slate-400 focus:border-[#2D1D5C] focus:ring-2 focus:ring-[#2D1D5C]/10';
    $cardClass = 'rounded-2xl border border-slate-200 bg-white p-4 shadow-sm';
@endphp

<form action="{{ route('settings.system') }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="{{ $cardClass }}">
            <label class="{{ $labelClass }}">Grading System</label>
            <select name="grading_system" class="{{ $fieldClass }}">
                <option value="">Select grading system</option>
                <option value="waec" {{ old('grading_system', $system['grading_system'] ?? '') === 'waec' ? 'selected' : '' }}>WAEC</option>
                <option value="custom" {{ old('grading_system', $system['grading_system'] ?? '') === 'custom' ? 'selected' : '' }}>Custom</option>
            </select>
        </div>

        <div class="{{ $cardClass }}">
            <label class="{{ $labelClass }}">Currency Symbol</label>
            <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $system['currency_symbol'] ?? 'NGN') }}" class="{{ $fieldClass }}" placeholder="NGN">
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
        <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-slate-500">Operational Toggles</p>
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
            <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700">
                <input type="checkbox" name="result_approval_required" value="1" class="rounded border-slate-300 text-[#2D1D5C] focus:ring-[#2D1D5C]" {{ old('result_approval_required', $system['result_approval_required'] ?? false) ? 'checked' : '' }}>
                Result approval required
            </label>
            <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700">
                <input type="checkbox" name="online_admission_enabled" value="1" class="rounded border-slate-300 text-[#2D1D5C] focus:ring-[#2D1D5C]" {{ old('online_admission_enabled', $system['online_admission_enabled'] ?? true) ? 'checked' : '' }}>
                Online admission enabled
            </label>
            <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700">
                <input type="checkbox" name="sms_notifications_enabled" value="1" class="rounded border-slate-300 text-[#2D1D5C] focus:ring-[#2D1D5C]" {{ old('sms_notifications_enabled', $system['sms_notifications_enabled'] ?? false) ? 'checked' : '' }}>
                SMS notifications enabled
            </label>
            <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700">
                <input type="checkbox" name="email_notifications_enabled" value="1" class="rounded border-slate-300 text-[#2D1D5C] focus:ring-[#2D1D5C]" {{ old('email_notifications_enabled', $system['email_notifications_enabled'] ?? false) ? 'checked' : '' }}>
                Email notifications enabled
            </label>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-gradient-to-r from-slate-50 to-indigo-50 px-4 py-4">
            <h2 class="text-base font-bold text-slate-900">SMTP Setup (Admin Controlled)</h2>
            <p class="mt-1 text-sm text-slate-500">These credentials are used for Contact Us form email delivery.</p>
        </div>

        <div class="p-4 md:p-5">
            <label class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700">
                <input type="checkbox" name="smtp_enabled" value="1" class="rounded border-indigo-300 text-[#2D1D5C] focus:ring-[#2D1D5C]" {{ old('smtp_enabled', $smtp['enabled'] ?? false) ? 'checked' : '' }}>
                Enable SMTP sending for Contact Us form
            </label>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div>
                    <label class="{{ $labelClass }}">SMTP Host</label>
                    <input type="text" name="smtp_host" value="{{ old('smtp_host', $smtp['host'] ?? '') }}" class="{{ $fieldClass }}" placeholder="smtp.example.com">
                </div>

                <div>
                    <label class="{{ $labelClass }}">SMTP Port</label>
                    <input type="number" name="smtp_port" value="{{ old('smtp_port', $smtp['port'] ?? 587) }}" class="{{ $fieldClass }}" placeholder="587">
                </div>

                <div>
                    <label class="{{ $labelClass }}">Encryption</label>
                    <select name="smtp_encryption" class="{{ $fieldClass }}">
                        <option value="tls" {{ $smtpEncryption === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ $smtpEncryption === 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="none" {{ $smtpEncryption === 'none' ? 'selected' : '' }}>None</option>
                    </select>
                </div>

                <div>
                    <label class="{{ $labelClass }}">SMTP Username</label>
                    <input type="text" name="smtp_username" value="{{ old('smtp_username', $smtp['username'] ?? '') }}" class="{{ $fieldClass }}">
                </div>

                <div>
                    <label class="{{ $labelClass }}">SMTP Password</label>
                    <input type="password" name="smtp_password" value="" class="{{ $fieldClass }}" placeholder="Leave blank to keep existing password">
                    <p class="mt-1 text-xs text-slate-500">Leave blank to retain the current saved password.</p>
                </div>

                <div>
                    <label class="{{ $labelClass }}">From Email</label>
                    <input type="email" name="smtp_from_address" value="{{ old('smtp_from_address', $smtp['from_address'] ?? '') }}" class="{{ $fieldClass }}" placeholder="noreply@school.com">
                </div>

                <div>
                    <label class="{{ $labelClass }}">From Name</label>
                    <input type="text" name="smtp_from_name" value="{{ old('smtp_from_name', $smtp['from_name'] ?? ($school->name ?? '')) }}" class="{{ $fieldClass }}">
                </div>

                <div class="md:col-span-2 xl:col-span-2">
                    <label class="{{ $labelClass }}">Contact Recipient Email</label>
                    <input type="email" name="smtp_to_address" value="{{ old('smtp_to_address', $smtp['to_address'] ?? ($school->email ?? '')) }}" class="{{ $fieldClass }}">
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="inline-flex items-center rounded-xl bg-gradient-to-r from-[#2D1D5C] to-[#4a2fa1] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:-translate-y-0.5 hover:shadow-indigo-300">
        Save System Preferences
    </button>
</form>

@if($errors->has('smtp_test'))
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
        {{ $errors->first('smtp_test') }}
    </div>
@endif

<form action="{{ route('settings.smtp-test') }}" method="POST" class="grid grid-cols-1 gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[1fr_auto]">
    @csrf
    <div>
        <label class="{{ $labelClass }}">Test Recipient Email</label>
        <input type="email" name="smtp_test_recipient" value="{{ old('smtp_test_recipient', $smtp['to_address'] ?? ($school->email ?? '')) }}" class="{{ $fieldClass }}" placeholder="recipient@school.com">
    </div>
    <div class="md:self-end">
        <button type="submit" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-[#2D1D5C] hover:text-[#2D1D5C]">
            Send Test SMTP Email
        </button>
    </div>
</form>
