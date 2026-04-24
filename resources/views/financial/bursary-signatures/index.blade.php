@extends('layouts.app')
@section('title', 'Assigned Signatures')
@section('header', 'Assigned Signatures')

@section('content')
@php
    $roleLabels = [
        'principal' => 'Principal',
        'vice_principal' => 'Vice Principal',
        'bursar' => 'Bursar',
    ];
@endphp
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Assigned Signatures</h1>
        <p class="text-sm text-slate-500 mt-0.5">Upload and assign signatures for Principal, Vice Principal, and Bursar.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-1">
            <h2 class="text-sm font-semibold text-slate-700">Add Signature</h2>
            <p class="mt-1 text-xs text-slate-500">Set one default active signature per role.</p>

            <form method="POST" action="{{ route('financial.bursary-signatures.store') }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Role</label>
                    <select name="signature_role" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        @foreach($signatureRoles as $role)
                            <option value="{{ $role }}" {{ old('signature_role', 'bursar') === $role ? 'selected' : '' }}>{{ $roleLabels[$role] ?? ucwords(str_replace('_', ' ', $role)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Name</label>
                    <input name="name" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" value="{{ old('name') }}" placeholder="e.g. Adeyemi Johnson">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Title</label>
                    <input name="title" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" value="{{ old('title') }}" placeholder="e.g. School Principal">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Signature Image</label>
                    <input type="file" name="signature" required accept=".png,.jpg,.jpeg" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>

                <div class="flex items-center gap-4 text-sm">
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> Active</label>
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_default" value="1"> Default for role</label>
                </div>

                <button class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Save Signature</button>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden lg:col-span-2">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Signature</th>
                        <th class="px-4 py-3 text-left">Profile</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($signatures as $signature)
                        @php
                            $role = $signature->signature_role ?: 'bursar';
                        @endphp
                        <tr>
                            <td class="px-4 py-3">
                                <img src="{{ asset('storage/' . ltrim($signature->signature_path, '/')) }}" alt="Signature" class="h-14 w-36 rounded border border-slate-200 object-contain bg-white">
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <div class="font-semibold">{{ $signature->name }}</div>
                                <div class="text-xs text-slate-500">{{ $signature->title ?: '-' }}</div>
                                <div class="mt-1 inline-flex rounded-full bg-indigo-100 px-2 py-0.5 text-[11px] font-semibold text-indigo-700">{{ $roleLabels[$role] ?? ucwords(str_replace('_', ' ', $role)) }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $signature->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $signature->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($signature->is_default)
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700">Role Default</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('financial.bursary-signatures.default', $signature) }}">
                                        @csrf
                                        <button class="rounded-lg border border-indigo-200 px-2.5 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-50">Set Role Default</button>
                                    </form>
                                    <form method="POST" action="{{ route('financial.bursary-signatures.destroy', $signature) }}" onsubmit="return confirm('Delete this signature?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-red-200 px-2.5 py-1 text-xs font-medium text-red-700 hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <tr class="bg-slate-50">
                            <td colspan="4" class="px-4 py-3">
                                <form method="POST" action="{{ route('financial.bursary-signatures.update', $signature) }}" enctype="multipart/form-data" class="grid gap-3 md:grid-cols-5">
                                    @csrf
                                    @method('PUT')

                                    <select name="signature_role" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                        @foreach($signatureRoles as $roleOption)
                                            <option value="{{ $roleOption }}" {{ old('signature_role', $signature->signature_role ?: 'bursar') === $roleOption ? 'selected' : '' }}>{{ $roleLabels[$roleOption] ?? ucwords(str_replace('_', ' ', $roleOption)) }}</option>
                                        @endforeach
                                    </select>

                                    <input name="name" value="{{ $signature->name }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Name">
                                    <input name="title" value="{{ $signature->title }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Title">
                                    <input type="file" name="signature" accept=".png,.jpg,.jpeg" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">

                                    <div class="flex items-center gap-3 text-sm">
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_active" value="1" {{ $signature->is_active ? 'checked' : '' }}> Active</label>
                                        <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_default" value="1" {{ $signature->is_default ? 'checked' : '' }}> Role Default</label>
                                    </div>

                                    <div class="md:col-span-5 text-right">
                                        <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Update</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-10 text-center text-slate-400">No signatures uploaded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3">{{ $signatures->links() }}</div>
        </div>
    </div>
</div>
@endsection
