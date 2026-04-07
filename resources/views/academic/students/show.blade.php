@extends('layouts.app')
@section('title', $student->full_name)
@section('header', $student->full_name)

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('academic.students.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Students</a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-medium text-slate-800">{{ $student->full_name }}</span>
        </div>
        <a href="{{ route('academic.students.edit', $student) }}"
            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Edit</a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Reset credentials flash --}}
    @if(session('reset_credentials'))
    @php $rc = session('reset_credentials'); @endphp
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
        <div class="flex items-start gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white text-base font-bold">!</div>
            <div class="flex-1">
                <p class="font-semibold text-amber-900 text-sm mb-2">Password reset — share these credentials with the student/parent</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div class="rounded-xl bg-white border border-amber-200 px-4 py-3">
                        <p class="text-amber-600 font-semibold uppercase tracking-wider mb-1">Portal URL</p>
                        <p class="font-mono font-bold text-slate-800 break-all">{{ route('portal.login') }}</p>
                    </div>
                    <div class="rounded-xl bg-white border border-amber-200 px-4 py-3">
                        <p class="text-amber-600 font-semibold uppercase tracking-wider mb-1">Email / Login</p>
                        <p class="font-mono font-bold text-slate-800 break-all">{{ $rc['email'] }}</p>
                    </div>
                    <div class="rounded-xl bg-white border border-amber-200 px-4 py-3">
                        <p class="text-amber-600 font-semibold uppercase tracking-wider mb-1">New Password</p>
                        <p class="font-mono font-bold text-slate-800 tracking-widest">{{ $rc['password'] }}</p>
                    </div>
                </div>
                <p class="mt-3 text-xs text-amber-700">Advise the student to change this password after first login.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profile Card --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm text-center">
            @if($student->photo)
                <img src="{{ asset('storage/'.$student->photo) }}" class="h-24 w-24 rounded-full object-cover mx-auto border-4 border-white shadow" alt="">
            @else
                <div class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center text-3xl font-bold text-indigo-700 mx-auto">
                    {{ strtoupper(substr($student->first_name,0,1)) }}
                </div>
            @endif
            <h2 class="mt-4 text-lg font-bold text-slate-900">{{ $student->full_name }}</h2>
            <p class="text-sm text-slate-500">{{ $student->admission_number }}</p>
            <div class="mt-3 flex justify-center gap-2">
                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">{{ $student->schoolClass?->name ?? 'No class' }}</span>
                @if($student->arm)
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $student->arm->name }}</span>
                @endif
            </div>
        </div>

        {{-- Details --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">Personal Information</h3>
            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                @foreach([
                    ['Gender', ucfirst($student->gender)],
                    ['Date of Birth', $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : '—'],
                    ['Blood Group', $student->blood_group ?? '—'],
                    ['Genotype', $student->genotype ?? '—'],
                    ['Religion', $student->religion ?? '—'],
                    ['State of Origin', $student->state_of_origin ?? '—'],
                    ['LGA', $student->lga ?? '—'],
                    ['Previous School', $student->previous_school ?? '—'],
                    ['Session Admitted', $student->session_admitted ?? '—'],
                    ['Status', ucfirst($student->status ?? 'active')],
                ] as [$label, $val])
                <div>
                    <dt class="text-xs text-slate-500">{{ $label }}</dt>
                    <dd class="font-medium text-slate-800 mt-0.5">{{ $val }}</dd>
                </div>
                @endforeach
            </dl>
            @if($student->address)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <dt class="text-xs text-slate-500">Address</dt>
                <dd class="text-sm text-slate-700 mt-0.5">{{ $student->address }}</dd>
            </div>
            @endif
        </div>
    </div>

    {{-- Portal Account Card --}}
    @if($student->user)
    @php $u = $student->user; @endphp
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700 text-xs font-bold">@</span>
                <h3 class="text-sm font-semibold text-slate-800">Portal Account</h3>
            </div>
            @if($u->is_active)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Active
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 border border-red-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Blocked
                </span>
            @endif
        </div>

        <div class="p-6 space-y-6">

            {{-- Login credentials box --}}
            <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-5 py-4">
                <p class="text-xs font-bold uppercase tracking-wider text-indigo-400 mb-3">Portal Login Credentials</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-indigo-400 font-semibold mb-1">Username / Email</p>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-sm font-bold text-slate-800 break-all">{{ $u->email }}</span>
                            <button type="button" onclick="copyText('{{ $u->email }}', this)"
                                class="shrink-0 rounded-md border border-indigo-200 bg-white px-2 py-0.5 text-xs font-medium text-indigo-600 hover:bg-indigo-100 transition">
                                Copy
                            </button>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-indigo-400 font-semibold mb-1">Portal URL</p>
                        <span class="font-mono text-sm font-bold text-slate-800 break-all">{{ route('portal.login') }}</span>
                    </div>
                </div>
                @if(session('reset_credentials') && session('reset_credentials.email') === $u->email)
                <div class="mt-3 pt-3 border-t border-indigo-100">
                    <p class="text-xs text-indigo-400 font-semibold mb-1">Last Reset Password <span class="text-amber-500">(visible once — save it now)</span></p>
                    <div class="flex items-center gap-2">
                        <span id="last_pw" class="font-mono text-sm font-bold text-slate-800 tracking-widest">{{ session('reset_credentials.password') }}</span>
                    </div>
                </div>
                @endif
                <p class="mt-3 text-xs text-indigo-300">Passwords are encrypted — only visible immediately after a reset. Use "Auto-Reset Password" or "Set a specific password" below to generate/change it.</p>
            </div>

            {{-- Login details --}}
            <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1">Login Email</dt>
                    <dd class="font-medium text-slate-800 break-all">{{ $u->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1">Role</dt>
                    <dd class="font-medium text-slate-800">{{ ucfirst($u->role?->value ?? $u->role ?? 'student') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1">Last Login</dt>
                    <dd class="font-medium text-slate-800">
                        {{ $u->last_login_at ? $u->last_login_at->format('d M Y, g:i A') : 'Never' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-1">Account Created</dt>
                    <dd class="font-medium text-slate-800">{{ $u->created_at->format('d M Y') }}</dd>
                </div>
            </dl>

            <div class="border-t border-slate-100 pt-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- Block / Activate --}}
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs font-semibold text-slate-600 mb-1">Account Access</p>
                    <p class="text-xs text-slate-400 mb-3">
                        @if($u->is_active) This account is active. Block it to prevent portal login.
                        @else This account is blocked. Activate to restore portal access.
                        @endif
                    </p>
                    <form method="POST" action="{{ route('academic.students.toggle-active', $student) }}"
                          onsubmit="return confirm('{{ $u->is_active ? 'Block this student portal account?' : 'Activate this student portal account?' }}')">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition
                            {{ $u->is_active
                                ? 'bg-red-600 hover:bg-red-700 text-white'
                                : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                            {{ $u->is_active ? 'Block Account' : 'Activate Account' }}
                        </button>
                    </form>
                </div>

                {{-- Reset password (auto-generate) --}}
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs font-semibold text-slate-600 mb-1">Auto-Reset Password</p>
                    <p class="text-xs text-slate-400 mb-3">Generate a new random password. The new password will be shown on screen after reset.</p>
                    <form method="POST" action="{{ route('academic.students.reset-password', $student) }}"
                          onsubmit="return confirm('Generate a new random password for {{ $student->full_name }}?')">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 text-sm font-semibold transition">
                            Reset &amp; Show Password
                        </button>
                    </form>
                </div>
            </div>

            {{-- Set custom password --}}
            <div class="border-t border-slate-100 pt-5">
                <button type="button" onclick="document.getElementById('changePwForm').classList.toggle('hidden')"
                    class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                    Set a specific password
                </button>
                <div id="changePwForm" class="{{ $errors->hasAny(['new_password','new_password_confirmation']) ? '' : 'hidden' }} mt-4">
                    @if($errors->hasAny(['new_password','new_password_confirmation']))
                    <div class="mb-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2">
                        <ul class="text-xs text-red-600 space-y-0.5">
                            @foreach($errors->get('new_password') as $e)<li>{{ $e }}</li>@endforeach
                            @foreach($errors->get('new_password_confirmation') as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('academic.students.change-password', $student) }}"
                          class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                        @csrf
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">New Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="new_password" id="sp_new" minlength="8" required
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 pr-9 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                    placeholder="Min. 8 characters">
                                <button type="button" onclick="togglePw('sp_new','sp_new_eye')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-indigo-600">
                                    <svg id="sp_new_eye" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="password" name="new_password_confirmation" id="sp_confirm" minlength="8" required
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 pr-9 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                    placeholder="Repeat password">
                                <button type="button" onclick="togglePw('sp_confirm','sp_confirm_eye')"
                                    class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-indigo-600">
                                    <svg id="sp_confirm_eye" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <button type="submit"
                                class="w-full rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-sm font-semibold transition">
                                Save Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    @else
    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-5 text-sm text-slate-400">
        No portal account is linked to this student yet. Enroll the student via the Admission module to auto-create an account.
    </div>
    @endif

    {{-- Recent Results --}}
    @if($student->results->count())
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">Recent Results</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-2 text-left">Subject</th>
                    <th class="px-5 py-2 text-left">Score</th>
                    <th class="px-5 py-2 text-left">Grade</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($student->results->take(10) as $result)
                <tr>
                    <td class="px-5 py-2 text-slate-800">{{ $result->subject?->name }}</td>
                    <td class="px-5 py-2 text-slate-700">{{ $result->total_score ?? '—' }}</td>
                    <td class="px-5 py-2 font-semibold text-indigo-700">{{ $result->grade ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>

@push('scripts')
<script>
    function togglePw(fieldId, iconId) {
        const field = document.getElementById(fieldId);
        const icon  = document.getElementById(iconId);
        const show  = field.type === 'password';
        field.type  = show ? 'text' : 'password';
        icon.innerHTML = show
            ? '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>'
            : '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>';
    }

    function copyText(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.textContent;
            btn.textContent = 'Copied!';
            btn.classList.add('bg-emerald-100', 'text-emerald-700', 'border-emerald-300');
            setTimeout(() => {
                btn.textContent = orig;
                btn.classList.remove('bg-emerald-100', 'text-emerald-700', 'border-emerald-300');
            }, 2000);
        });
    }
</script>
@endpush
@endsection
