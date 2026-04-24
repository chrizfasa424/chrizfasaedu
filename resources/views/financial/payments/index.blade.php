@extends('layouts.app')
@section('title', 'Payments')
@section('header', 'Payments')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Payments & Verification</h1>
            <p class="text-sm text-slate-500 mt-0.5">Review submissions, verify payments, and issue receipts.</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm text-amber-700">
            Pending Review: <span class="font-semibold">{{ $pendingCount }}</span>
        </div>
    </div>

    <form method="GET" action="{{ route('financial.payments.index') }}" class="grid gap-3 md:grid-cols-5">
        <input name="student" value="{{ request('student') }}" placeholder="Student ID / Name" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <select name="method" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All Methods</option>
            @foreach($methods as $method)
                <option value="{{ $method }}" {{ request('method') === $method ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $method)) }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All Statuses</option>
            @foreach($statuses as $status)
                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Filter</button>
        <a href="{{ route('financial.payments.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
    </form>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Reference</th>
                    <th class="px-4 py-3 text-left">Student</th>
                    <th class="px-4 py-3 text-left">Invoice</th>
                    <th class="px-4 py-3 text-left">Method</th>
                    <th class="px-4 py-3 text-right">Amount</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($payments as $payment)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $payment->payment_reference }}</td>
                        <td class="px-4 py-3 text-slate-700">
                            <div class="font-semibold">{{ $payment->student?->full_name }}</div>
                            <div class="text-xs text-slate-500">{{ $payment->student?->admission_number ?: $payment->student?->registration_number }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($payment->invoice)
                                <a href="{{ route('financial.invoices.show', $payment->invoice) }}" class="text-xs text-indigo-600 hover:underline font-mono">{{ $payment->invoice->invoice_number }}</a>
                            @else
                                <span class="text-xs text-slate-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 capitalize text-slate-600">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-slate-800">NGN {{ number_format((float) $payment->amount, 2) }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ optional($payment->payment_date ?: $payment->paid_at)->format('d M Y') ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $status = strtolower((string) $payment->status);
                                $statusClass = match($status) {
                                    'approved', 'confirmed' => 'bg-green-100 text-green-700',
                                    'pending', 'under_review' => 'bg-yellow-100 text-yellow-700',
                                    'rejected', 'failed', 'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $payment->status)) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('financial.payments.review', $payment) }}" class="rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-slate-400">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $payments->links() }}</div>
</div>
@endsection
