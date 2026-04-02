@extends('layouts.app')

@section('title', 'Multi-School Dashboard')
@section('header', 'Multi-School Dashboard')

@section('content')
<div class="space-y-8">
    <section class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total Schools</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($summary['totalSchools']) }}</p>
            <p class="mt-2 text-sm text-slate-500">All registered schools across the platform.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Active Schools</p>
            <p class="mt-3 text-3xl font-semibold text-emerald-600">{{ number_format($summary['activeSchools']) }}</p>
            <p class="mt-2 text-sm text-slate-500">Schools currently active and visible in the system.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">This Page</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($summary['schoolsOnPage']) }}</p>
            <p class="mt-2 text-sm text-slate-500">Records loaded on the current dashboard page.</p>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.1fr,1.6fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-violet-700">School Onboarding</p>
                <h3 class="mt-2 text-2xl font-semibold text-slate-900">Add a new school tenant</h3>
                <p class="mt-2 text-sm text-slate-500">Create a school, assign its first admin, and provision the subscription in one workflow.</p>
            </div>

            <form action="{{ route('multi-school.onboard') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">School name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                    </div>
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">School email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-medium text-slate-700">Phone</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                    </div>
                    <div>
                        <label for="school_type" class="mb-2 block text-sm font-medium text-slate-700">School type</label>
                        <select id="school_type" name="school_type" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                            <option value="">Select type</option>
                            <option value="primary" @selected(old('school_type') === 'primary')>Primary</option>
                            <option value="secondary" @selected(old('school_type') === 'secondary')>Secondary</option>
                            <option value="combined" @selected(old('school_type') === 'combined')>Combined</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="address" class="mb-2 block text-sm font-medium text-slate-700">Address</label>
                    <input id="address" name="address" type="text" value="{{ old('address') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="city" class="mb-2 block text-sm font-medium text-slate-700">City</label>
                        <input id="city" name="city" type="text" value="{{ old('city') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                    </div>
                    <div>
                        <label for="state" class="mb-2 block text-sm font-medium text-slate-700">State</label>
                        <input id="state" name="state" type="text" value="{{ old('state') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="plan" class="mb-2 block text-sm font-medium text-slate-700">Subscription plan</label>
                        <select id="plan" name="plan" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                            <option value="">Select plan</option>
                            <option value="basic" @selected(old('plan') === 'basic')>Basic</option>
                            <option value="standard" @selected(old('plan') === 'standard')>Standard</option>
                            <option value="premium" @selected(old('plan') === 'premium')>Premium</option>
                            <option value="enterprise" @selected(old('plan') === 'enterprise')>Enterprise</option>
                        </select>
                    </div>
                    <div>
                        <label for="admin_name" class="mb-2 block text-sm font-medium text-slate-700">Admin full name</label>
                        <input id="admin_name" name="admin_name" type="text" value="{{ old('admin_name') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                    </div>
                </div>

                <div>
                    <label for="admin_email" class="mb-2 block text-sm font-medium text-slate-700">Admin email</label>
                    <input id="admin_email" name="admin_email" type="email" value="{{ old('admin_email') }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-100" required>
                </div>

                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    The first school admin account will be created with a temporary password of <span class="font-semibold">changeme123</span>.
                </div>

                <button type="submit" class="inline-flex items-center rounded-full bg-[#2D1D5C] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#DFE753] hover:text-[#2D1D5C] focus:outline-none focus:ring-2 focus:ring-[#DFE753] focus:ring-offset-2">
                    Create School Tenant
                </button>
            </form>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-200 px-6 py-5 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400">Tenant Directory</p>
                    <h3 class="mt-2 text-2xl font-semibold text-slate-900">Registered schools</h3>
                </div>
                <div class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600">
                    {{ number_format($schools->total()) }} schools
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">School</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Students</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Staff</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Plan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($schools as $school)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-6 py-5">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $school->name }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $school->email ?: 'No email provided' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-600">{{ ucfirst($school->school_type ?? 'n/a') }}</td>
                                <td class="px-6 py-5 text-sm font-medium text-slate-900">{{ number_format($school->students_count) }}</td>
                                <td class="px-6 py-5 text-sm font-medium text-slate-900">{{ number_format($school->staff_count) }}</td>
                                <td class="px-6 py-5 text-sm text-slate-600">{{ ucfirst($school->subscription_plan ?? 'n/a') }}</td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $school->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $school->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                                    No schools have been onboarded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200 px-6 py-4">
                {{ $schools->links() }}
            </div>
        </div>
    </section>
</div>
@endsection
