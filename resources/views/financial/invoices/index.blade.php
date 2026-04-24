@extends('layouts.app')
@section('title', 'Invoices')
@section('header', 'Invoices')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Invoices</h1>
            <p class="text-sm text-slate-500 mt-0.5">Track student fee invoices.</p>
        </div>
        <button onclick="document.getElementById('generate-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Generate Invoices
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('financial.invoices.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
            <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" onchange="this.form.submit()">
                <option value="">All statuses</option>
                @foreach(['pending','partial','paid','overdue'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Invoice No.</th>
                    <th class="px-5 py-3 text-left">Student</th>
                    <th class="px-5 py-3 text-left">Session/Term</th>
                    <th class="px-5 py-3 text-right">Total</th>
                    <th class="px-5 py-3 text-right">Balance</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($invoices as $invoice)
                @php
                    $invoiceStatus = $invoice->status?->value ?? (string) $invoice->status;
                    $invoiceStatusLabel = ucfirst(str_replace('_', ' ', $invoiceStatus));
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-mono text-xs text-slate-600">{{ $invoice->invoice_number }}</td>
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $invoice->student?->full_name }}</td>
                    <td class="px-5 py-3 text-slate-500 text-xs">{{ $invoice->session?->name }} / {{ $invoice->term?->name }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-slate-800">₦{{ number_format($invoice->total_amount, 2) }}</td>
                    <td class="px-5 py-3 text-right {{ $invoice->balance > 0 ? 'text-red-600 font-semibold' : 'text-green-600' }}">₦{{ number_format($invoice->balance, 2) }}</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium
                            @if($invoiceStatus === 'paid') bg-green-100 text-green-700
                            @elseif($invoiceStatus === 'partial') bg-blue-100 text-blue-700
                            @elseif($invoiceStatus === 'overdue') bg-red-100 text-red-700
                            @else bg-yellow-100 text-yellow-700 @endif">
                            {{ $invoiceStatusLabel }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <a href="{{ route('financial.invoices.show', $invoice) }}" class="text-xs text-indigo-600 hover:underline font-medium">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">No invoices found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $invoices->links() }}</div>

</div>

{{-- Generate Modal --}}
<div id="generate-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Generate Invoices</h2>
            <button onclick="document.getElementById('generate-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('financial.invoices.generate') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Session ID <span class="text-red-500">*</span></label>
                <input type="number" name="session_id" required placeholder="Session ID"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Term ID <span class="text-red-500">*</span></label>
                <input type="number" name="term_id" required placeholder="Term ID"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Class ID <span class="text-red-500">*</span></label>
                <input type="number" name="class_id" required placeholder="Class ID"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('generate-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Generate</button>
            </div>
        </form>
    </div>
</div>
@endsection

