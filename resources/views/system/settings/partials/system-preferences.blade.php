@php
    $system = $school->settings ?? [];
    $smtp = $system['smtp'] ?? [];
@endphp

<form action="{{ route('settings.system') }}" method="POST" class="space-y-8">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Grading System</label>
            <select name="grading_system" class="w-full rounded-2xl border-slate-300">
                <option value="">Select grading system</option>
                <option value="waec" {{ old('grading_system', $system['grading_system'] ?? '') === 'waec' ? 'selected' : '' }}>WAEC</option>
                <option value="custom" {{ old('grading_system', $system['grading_system'] ?? '') === 'custom' ? 'selected' : '' }}>Custom</option>
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Currency Symbol</label>
            <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $system['currency_symbol'] ?? 'NGN') }}" class="w-full rounded-2xl border-slate-300">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4 rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <label class="inline-flex items-center text-sm font-medium text-slate-700"><input type="checkbox" name="result_approval_required" value="1" class="mr-2 rounded border-slate-300" {{ old('result_approval_required', $system['result_approval_required'] ?? false) ? 'checked' : '' }}>Result approval required</label>
        <label class="inline-flex items-center text-sm font-medium text-slate-700"><input type="checkbox" name="online_admission_enabled" value="1" class="mr-2 rounded border-slate-300" {{ old('online_admission_enabled', $system['online_admission_enabled'] ?? true) ? 'checked' : '' }}>Online admission enabled</label>
        <label class="inline-flex items-center text-sm font-medium text-slate-700"><input type="checkbox" name="sms_notifications_enabled" value="1" class="mr-2 rounded border-slate-300" {{ old('sms_notifications_enabled', $system['sms_notifications_enabled'] ?? false) ? 'checked' : '' }}>SMS notifications enabled</label>
        <label class="inline-flex items-center text-sm font-medium text-slate-700"><input type="checkbox" name="email_notifications_enabled" value="1" class="mr-2 rounded border-slate-300" {{ old('email_notifications_enabled', $system['email_notifications_enabled'] ?? false) ? 'checked' : '' }}>Email notifications enabled</label>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <h2 class="text-base font-bold text-slate-900">SMTP Setup (Admin Controlled)</h2>
        <p class="mt-1 text-sm text-slate-500">These credentials are used for Contact Us form email delivery.</p>
        <label class="mt-4 inline-flex items-center text-sm font-medium text-slate-700"><input type="checkbox" name="smtp_enabled" value="1" class="mr-2 rounded border-slate-300" {{ old('smtp_enabled', $smtp['enabled'] ?? false) ? 'checked' : '' }}>Enable SMTP sending for Contact Us form</label>
        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">SMTP Host</label><input type="text" name="smtp_host" value="{{ old('smtp_host', $smtp['host'] ?? '') }}" class="w-full rounded-2xl border-slate-300" placeholder="smtp.example.com"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">SMTP Port</label><input type="number" name="smtp_port" value="{{ old('smtp_port', $smtp['port'] ?? 587) }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Encryption</label>@php $smtpEncryption = old('smtp_encryption', $smtp['encryption'] ?? 'tls'); @endphp<select name="smtp_encryption" class="w-full rounded-2xl border-slate-300"><option value="tls" {{ $smtpEncryption === 'tls' ? 'selected' : '' }}>TLS</option><option value="ssl" {{ $smtpEncryption === 'ssl' ? 'selected' : '' }}>SSL</option><option value="none" {{ $smtpEncryption === 'none' ? 'selected' : '' }}>None</option></select></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">SMTP Username</label><input type="text" name="smtp_username" value="{{ old('smtp_username', $smtp['username'] ?? '') }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">SMTP Password</label><input type="password" name="smtp_password" value="" class="w-full rounded-2xl border-slate-300" placeholder="Leave blank to keep existing password"><p class="mt-2 text-xs text-slate-500">Leave blank to retain the current saved password.</p></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">From Email</label><input type="email" name="smtp_from_address" value="{{ old('smtp_from_address', $smtp['from_address'] ?? '') }}" class="w-full rounded-2xl border-slate-300" placeholder="noreply@school.com"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">From Name</label><input type="text" name="smtp_from_name" value="{{ old('smtp_from_name', $smtp['from_name'] ?? ($school->name ?? '')) }}" class="w-full rounded-2xl border-slate-300"></div>
            <div><label class="mb-2 block text-sm font-semibold text-slate-700">Contact Recipient Email</label><input type="email" name="smtp_to_address" value="{{ old('smtp_to_address', $smtp['to_address'] ?? ($school->email ?? '')) }}" class="w-full rounded-2xl border-slate-300"></div>
        </div>
    </div>

    <button type="submit" class="inline-flex items-center rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C]">Save System Preferences</button>
</form>

@if($errors->has('smtp_test'))
    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
        {{ $errors->first('smtp_test') }}
    </div>
@endif

<form action="{{ route('settings.smtp-test') }}" method="POST" class="grid grid-cols-1 gap-4 rounded-3xl border border-slate-200 bg-slate-50 p-5 md:grid-cols-[1fr_auto]">
    @csrf
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Test Recipient Email</label>
        <input type="email" name="smtp_test_recipient" value="{{ old('smtp_test_recipient', $smtp['to_address'] ?? ($school->email ?? '')) }}" class="w-full rounded-2xl border-slate-300" placeholder="recipient@school.com">
    </div>
    <div class="md:self-end">
        <button type="submit" class="inline-flex items-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-[#2D1D5C] hover:text-[#2D1D5C]">Send Test SMTP Email</button>
    </div>
</form>

