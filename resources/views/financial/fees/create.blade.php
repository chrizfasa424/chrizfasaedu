@extends('layouts.app')
@section('title', 'Add Fee Structure')
@section('header', 'Add Fee Structure')

@section('content')
<div class="space-y-6 max-w-2xl">

    <div class="flex items-center gap-3">
        <a href="{{ route('financial.fees.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Fee Structures</a>
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

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Fee Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Category <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Select category</option>
                        @foreach(['tuition','development_levy','ict','uniform','exam','pta','transport','hostel','other'] as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$cat)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Amount (₦) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" required min="0" step="0.01"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Class (optional)</label>
                    <select name="class_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">All classes</option>
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Session ID <span class="text-red-500">*</span></label>
                    <input type="number" name="session_id" value="{{ old('session_id') }}" required placeholder="Session ID"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_compulsory" id="is_compulsory" value="1" {{ old('is_compulsory') ? 'checked' : '' }}
                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-400">
                <label for="is_compulsory" class="text-sm text-slate-700">Compulsory fee</label>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('financial.fees.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Create Fee</button>
            </div>
        </form>
    </div>

</div>
@endsection
