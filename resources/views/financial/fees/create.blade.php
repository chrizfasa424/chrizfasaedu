@extends('layouts.app')
@section('title', 'Add Fee Structure')
@section('header', 'Add Fee Structure')

@section('content')
<div class="w-full space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('financial.fees.index') }}" class="text-sm text-slate-500 hover:text-slate-700">&larr; Fee Structures</a>
        <span class="text-slate-300">/</span>
        <span class="text-sm font-medium text-slate-800">New Fee</span>
    </div>

    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('financial.fees.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Fee Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Category <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Select category</option>
                        @foreach(($categories ?? ['tuition','development_levy','ict','uniform','exam','pta','transport','hostel','other']) as $cat)
                            <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $cat)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Amount (NGN) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" required min="0" step="0.01"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    @php
                        $oldClassIds = collect(old('class_ids', []))
                            ->filter(fn ($id) => (string) $id !== '')
                            ->map(fn ($id) => (string) $id)
                            ->values()
                            ->all();
                    @endphp
                    <label class="mb-1 block text-xs font-medium text-slate-600">Classes (optional, multi-select)</label>
                    <select name="class_ids[]" multiple size="7" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @foreach(($classes ?? collect()) as $c)
                            <option value="{{ $c->id }}" {{ in_array((string) $c->id, $oldClassIds, true) ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">
                        Select one or more classes. Leave empty to apply to all classes.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Session <span class="text-red-500">*</span></label>
                    <select name="session_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Select session</option>
                        @foreach(($sessions ?? collect()) as $session)
                            <option value="{{ $session->id }}" {{ (string) old('session_id') === (string) $session->id ? 'selected' : '' }}>
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Term (optional)</label>
                    <select name="term_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">All terms</option>
                        @foreach(($terms ?? collect()) as $term)
                            <option value="{{ $term->id }}" {{ (string) old('term_id') === (string) $term->id ? 'selected' : '' }}>
                                {{ $term->name }}{{ $term->session?->name ? ' - ' . $term->session->name : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Late Fee Amount</label>
                    <input type="number" name="late_fee_amount" value="{{ old('late_fee_amount', 0) }}" min="0" step="0.01"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Late Fee After (Days)</label>
                    <input type="number" name="late_fee_after_days" value="{{ old('late_fee_after_days', 30) }}" min="1" step="1"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Description</label>
                <textarea name="description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_compulsory" value="1" {{ old('is_compulsory', '1') ? 'checked' : '' }}
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-400">
                    <span>Compulsory fee</span>
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-400">
                    <span>Active fee</span>
                </label>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-2">
                <a href="{{ route('financial.fees.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Create Fee</button>
            </div>
        </form>
    </div>
</div>
@endsection
