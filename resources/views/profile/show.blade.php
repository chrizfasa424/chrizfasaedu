@extends('layouts.app')
@section('title', 'My Profile')
@section('header', 'My Profile')

@section('content')
@php
    $isPortal  = auth('portal')->check();
    $photoSrc  = null;

    // Resolve photo: check profile first, then user avatar
    if ($profile && !empty($profile->photo)) {
        $photoSrc = asset('storage/' . $profile->photo);
    } elseif (!empty($user->avatar)) {
        $photoSrc = asset('storage/' . $user->avatar);
    }

    $initials = strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1));
    $role     = $user->role?->value ?? 'user';

    // Route helpers based on guard
    $infoRoute     = $isPortal ? route('portal.profile.update.info')     : route('profile.update.info');
    $detailsRoute  = $isPortal ? route('portal.profile.update.details')  : route('profile.update.details');
    $passwordRoute = $isPortal ? route('portal.profile.change.password') : route('profile.change.password');
    $photoRoute    = $isPortal ? route('portal.profile.update.photo')    : route('profile.update.photo');
    $deletePhotoRoute = $isPortal ? route('portal.profile.delete.photo') : route('profile.delete.photo');

    $isStudent = $user->isStudent();
    $isStaff   = $user->isTeacher() || $user->isSchoolAdmin() || $user->isSuperAdmin();
    $isParent  = $user->isParent();
@endphp

{{-- Active tab from session or default --}}
@php $activeTab = session('profile_tab', request('tab', 'info')); @endphp

<div class="max-w-4xl space-y-6">

    {{-- Profile Header Card --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
        <div class="h-24 w-full" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);"></div>
        <div class="px-6 pb-6 -mt-12 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div class="flex items-end gap-4">
                {{-- Avatar --}}
                <div class="relative">
                    @if($photoSrc)
                        <img src="{{ $photoSrc }}" alt="{{ $user->full_name }}"
                             class="h-24 w-24 rounded-2xl object-cover border-4 border-white shadow-lg">
                    @else
                        <div class="h-24 w-24 rounded-2xl border-4 border-white shadow-lg bg-indigo-600 flex items-center justify-center text-white text-3xl font-extrabold">
                            {{ $initials }}
                        </div>
                    @endif
                    {{-- Camera button trigger --}}
                    <button type="button" onclick="document.getElementById('photoTab').click()"
                            class="absolute -bottom-2 -right-2 flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-white shadow-md hover:bg-indigo-700 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                </div>
                <div class="pb-1">
                    <h2 class="text-xl font-extrabold text-slate-900">{{ $user->full_name }}</h2>
                    <p class="text-sm text-slate-500">{{ ucfirst(str_replace('_', ' ', $role)) }}
                        @if($user->school) · {{ $user->school->name }} @endif
                    </p>
                    @if($isStudent && $profile?->admission_number)
                        <p class="text-xs text-slate-400 mt-0.5">Adm No: {{ $profile->admission_number }}</p>
                    @endif
                </div>
            </div>
            <div class="text-xs text-slate-400 pb-1">
                Last login: {{ $user->last_login_at?->format('d M Y, g:i A') ?? 'Never' }}
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">

        {{-- Tab bar --}}
        <div class="flex border-b border-slate-100 overflow-x-auto" id="profileTabBar">
            @foreach([
                ['info',     'infoTab',     'Personal Info',     'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['details',  'detailsTab',  'Additional Details','M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['password', 'passwordTab', 'Change Password',   'M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z'],
                ['photo',    'photoTab',    'Profile Photo',     'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z'],
            ] as [$key, $id, $label, $icon])
            <button id="{{ $id }}" onclick="switchTab('{{ $key }}')"
                    class="tab-btn flex items-center gap-2 whitespace-nowrap px-5 py-4 text-sm font-semibold border-b-2 transition-colors
                           {{ $activeTab === $key ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                </svg>
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- ── TAB: Personal Info ── --}}
        <div id="tab-info" class="tab-panel p-6 {{ $activeTab !== 'info' ? 'hidden' : '' }}">
            @if($errors->has('first_name') || $errors->has('email') || $errors->has('last_name') || $errors->has('phone'))
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                <ul class="text-sm text-red-700 space-y-0.5">
                    @foreach($errors->only(['first_name','last_name','other_names','email','phone']) as $field => $msgs)
                        @foreach($msgs as $msg)<li>{{ $msg }}</li>@endforeach
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ $infoRoute }}" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="label">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                               class="input-field">
                    </div>
                    <div>
                        <label class="label">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                               class="input-field">
                    </div>
                    <div>
                        <label class="label">Other Names</label>
                        <input type="text" name="other_names" value="{{ old('other_names', $user->other_names) }}"
                               class="input-field">
                    </div>
                    <div>
                        <label class="label">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="input-field">
                    </div>
                    <div>
                        <label class="label">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="input-field">
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn-primary">Save Personal Info</button>
                </div>
            </form>
        </div>

        {{-- ── TAB: Additional Details ── --}}
        <div id="tab-details" class="tab-panel p-6 {{ $activeTab !== 'details' ? 'hidden' : '' }}">
            @if(!$profile && !$isStudent)
            <p class="text-sm text-slate-400 italic">No extended profile linked to your account yet.</p>
            @else
            <form method="POST" action="{{ $detailsRoute }}" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="label">Home Address</label>
                        <textarea name="address" rows="2" class="input-field">{{ old('address', $profile?->address) }}</textarea>
                    </div>
                    <div>
                        <label class="label">City</label>
                        <input type="text" name="city" value="{{ old('city', $profile?->city) }}" class="input-field">
                    </div>
                    <div>
                        <label class="label">State</label>
                        <input type="text" name="state" value="{{ old('state', $profile?->state) }}" class="input-field">
                    </div>
                    <div>
                        <label class="label">Nationality</label>
                        <input type="text" name="nationality" value="{{ old('nationality', $profile?->nationality) }}" class="input-field">
                    </div>
                    <div>
                        <label class="label">Religion</label>
                        <input type="text" name="religion" value="{{ old('religion', $profile?->religion) }}" class="input-field">
                    </div>

                    @if($isStudent)
                    <div>
                        <label class="label">Blood Group</label>
                        <select name="blood_group" class="input-field">
                            <option value="">-- Select --</option>
                            @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                            <option value="{{ $bg }}" {{ old('blood_group', $profile?->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Genotype</label>
                        <select name="genotype" class="input-field">
                            <option value="">-- Select --</option>
                            @foreach(['AA','AS','AC','SS','SC'] as $g)
                            <option value="{{ $g }}" {{ old('genotype', $profile?->genotype) == $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name"
                               value="{{ old('emergency_contact_name', $profile?->emergency_contact_name) }}"
                               class="input-field">
                    </div>
                    <div>
                        <label class="label">Emergency Contact Phone</label>
                        <input type="tel" name="emergency_contact_phone"
                               value="{{ old('emergency_contact_phone', $profile?->emergency_contact_phone) }}"
                               class="input-field">
                    </div>
                    @endif

                    @if($isStaff)
                    <div>
                        <label class="label">Designation / Role</label>
                        <input type="text" name="designation" value="{{ old('designation', $profile?->designation) }}" class="input-field">
                    </div>
                    <div>
                        <label class="label">Qualification</label>
                        <input type="text" name="qualification" value="{{ old('qualification', $profile?->qualification) }}" class="input-field">
                    </div>
                    @endif
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn-primary">Save Details</button>
                </div>
            </form>
            @endif
        </div>

        {{-- ── TAB: Change Password ── --}}
        <div id="tab-password" class="tab-panel p-6 {{ $activeTab !== 'password' ? 'hidden' : '' }}">
            @if($errors->has('current_password') || $errors->has('password'))
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                <ul class="text-sm text-red-700 space-y-0.5">
                    @foreach($errors->only(['current_password','password']) as $field => $msgs)
                        @foreach($msgs as $msg)<li>{{ $msg }}</li>@endforeach
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="max-w-md">
                <form method="POST" action="{{ $passwordRoute }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="label">Current Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" name="current_password" id="cur_pw" required
                                   placeholder="Your current password" class="input-field pr-10">
                            <button type="button" onclick="togglePw('cur_pw','cur_eye')" class="eye-btn">
                                <svg id="cur_eye" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="label">New Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" name="password" id="new_pw" required
                                   placeholder="Min. 8 chars, upper, lower & number" class="input-field pr-10">
                            <button type="button" onclick="togglePw('new_pw','new_eye')" class="eye-btn">
                                <svg id="new_eye" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                        </div>
                        {{-- strength bar --}}
                        <div class="mt-2 h-1.5 w-full rounded-full bg-slate-100">
                            <div id="strengthBar" class="h-1.5 rounded-full transition-all duration-300" style="width:0%;"></div>
                        </div>
                        <p id="strengthLabel" class="mt-1 text-xs text-slate-400"></p>
                    </div>
                    <div>
                        <label class="label">Confirm New Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="conf_pw" required
                                   placeholder="Repeat new password" class="input-field pr-10">
                            <button type="button" onclick="togglePw('conf_pw','conf_eye')" class="eye-btn">
                                <svg id="conf_eye" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── TAB: Profile Photo ── --}}
        <div id="tab-photo" class="tab-panel p-6 {{ $activeTab !== 'photo' ? 'hidden' : '' }}">
            <div class="flex flex-col sm:flex-row gap-10 items-start">

                {{-- Current photo --}}
                <div class="flex flex-col items-center gap-3">
                    @if($photoSrc)
                        <img src="{{ $photoSrc }}" alt="Profile photo"
                             class="h-36 w-36 rounded-2xl object-cover border border-slate-200 shadow">
                    @else
                        <div class="h-36 w-36 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600 text-4xl font-extrabold shadow">
                            {{ $initials }}
                        </div>
                    @endif
                    <span class="text-xs text-slate-400">Current Photo</span>

                    @if($photoSrc)
                    <form method="POST" action="{{ $deletePhotoRoute }}" onsubmit="return confirm('Remove profile photo?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 transition">
                            Remove photo
                        </button>
                    </form>
                    @endif
                </div>

                {{-- Upload form --}}
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-700 mb-4">Upload New Photo</p>
                    <form method="POST" action="{{ $photoRoute }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @if($errors->has('photo'))
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first('photo') }}
                        </div>
                        @endif

                        {{-- Drag & drop zone --}}
                        <label for="photoInput"
                               class="flex flex-col items-center justify-center w-full h-44 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition"
                               id="dropZone">
                            <svg class="h-10 w-10 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                            </svg>
                            <p class="text-sm font-medium text-slate-500">Click to select or drag & drop</p>
                            <p class="text-xs text-slate-400 mt-1">JPG, PNG, WEBP · Max 2MB</p>
                            <p id="fileName" class="text-xs font-semibold text-indigo-600 mt-2 hidden"></p>
                            <input type="file" name="photo" id="photoInput" accept="image/*" class="hidden"
                                   onchange="previewPhoto(this)">
                        </label>

                        {{-- Preview --}}
                        <div id="photoPreviewWrap" class="hidden">
                            <img id="photoPreview" src="" alt="Preview"
                                 class="h-28 w-28 rounded-2xl object-cover border border-slate-200 shadow">
                        </div>

                        <button type="submit" class="btn-primary">Upload Photo</button>
                    </form>
                </div>
            </div>
        </div>

    </div>{{-- end card --}}
</div>

<style>
    .label { @apply block text-sm font-semibold text-slate-700 mb-1.5; }
    .input-field { @apply w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100; }
    .btn-primary { @apply inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 transition; }
    .eye-btn { @apply absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-indigo-600 transition; }
</style>

@push('scripts')
<script>
    // ── Tab switching ───────────────────────────────────────
    function switchTab(key) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('border-indigo-600', 'text-indigo-600');
            b.classList.add('border-transparent', 'text-slate-500');
        });
        document.getElementById('tab-' + key).classList.remove('hidden');
        const btn = document.getElementById(key + 'Tab') || document.getElementById(key.charAt(0).toUpperCase() + key.slice(1) + 'Tab');
        if (btn) {
            btn.classList.remove('border-transparent', 'text-slate-500');
            btn.classList.add('border-indigo-600', 'text-indigo-600');
        }
    }

    // Map button ids to tab keys
    document.getElementById('infoTab').onclick     = () => switchTab('info');
    document.getElementById('detailsTab').onclick  = () => switchTab('details');
    document.getElementById('passwordTab').onclick = () => switchTab('password');
    document.getElementById('photoTab').onclick    = () => switchTab('photo');

    // ── Show / hide password ────────────────────────────────
    function togglePw(fieldId, iconId) {
        const field = document.getElementById(fieldId);
        const icon  = document.getElementById(iconId);
        const show  = field.type === 'password';
        field.type  = show ? 'text' : 'password';
        icon.innerHTML = show
            ? '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>'
            : '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>';
    }

    // ── Password strength meter ─────────────────────────────
    document.getElementById('new_pw')?.addEventListener('input', function () {
        const val = this.value;
        let score = 0;
        if (val.length >= 8)             score++;
        if (/[A-Z]/.test(val))           score++;
        if (/[a-z]/.test(val))           score++;
        if (/[0-9]/.test(val))           score++;
        if (/[^A-Za-z0-9]/.test(val))   score++;

        const bar    = document.getElementById('strengthBar');
        const label  = document.getElementById('strengthLabel');
        const levels = [
            { w: '0%',   bg: '',               text: '' },
            { w: '25%',  bg: '#ef4444',        text: 'Weak' },
            { w: '50%',  bg: '#f59e0b',        text: 'Fair' },
            { w: '75%',  bg: '#6366f1',        text: 'Good' },
            { w: '100%', bg: '#10b981',        text: 'Strong' },
        ];
        const l = levels[Math.min(score, 4)];
        bar.style.width = l.w;
        bar.style.background = l.bg;
        label.textContent = l.text;
        label.style.color = l.bg;
    });

    // ── Photo preview ───────────────────────────────────────
    function previewPhoto(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileName').classList.remove('hidden');

        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('photoPreview').src = e.target.result;
            document.getElementById('photoPreviewWrap').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    // ── Drag & drop ─────────────────────────────────────────
    const dz = document.getElementById('dropZone');
    dz?.addEventListener('dragover',  e => { e.preventDefault(); dz.classList.add('border-indigo-500', 'bg-indigo-50'); });
    dz?.addEventListener('dragleave', () => dz.classList.remove('border-indigo-500', 'bg-indigo-50'));
    dz?.addEventListener('drop', e => {
        e.preventDefault();
        dz.classList.remove('border-indigo-500', 'bg-indigo-50');
        const input = document.getElementById('photoInput');
        input.files = e.dataTransfer.files;
        previewPhoto(input);
    });

    // ── Re-open correct tab if errors exist ─────────────────
    @if($errors->hasAny(['first_name','last_name','email','phone']))
        switchTab('info');
    @elseif($errors->hasAny(['current_password','password']))
        switchTab('password');
    @elseif($errors->has('photo'))
        switchTab('photo');
    @endif
</script>
@endpush
@endsection
