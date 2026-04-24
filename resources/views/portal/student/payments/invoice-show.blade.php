@extends('layouts.app')
@section('title', 'Invoice Details')
@section('header', 'Invoice Details')

@section('content')
@php
    $invoiceStatus = strtolower((string) ($invoice->status?->value ?? $invoice->status));
    $feeState = $invoice->fee_state;
    $statusBadge = match($invoiceStatus) {
        'paid' => 'bg-emerald-100 text-emerald-700',
        'partial' => 'bg-blue-100 text-blue-700',
        'overdue' => 'bg-red-100 text-red-700',
        'waived' => 'bg-violet-100 text-violet-700',
        'cancelled' => 'bg-slate-200 text-slate-700',
        default => 'bg-amber-100 text-amber-700',
    };
    $hasOutstanding = (float) $invoice->balance > 0;
@endphp

<div class="w-full space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('portal.invoices.index') }}" class="text-sm text-slate-500 hover:text-slate-700">&larr; Back to My Invoices</a>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-900">{{ $invoice->invoice_number }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $invoice->session?->name ?: 'N/A' }} / {{ $invoice->term?->name ?: 'N/A' }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $feeState === 'active' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                Fee {{ ucfirst($feeState) }}
            </span>
            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusBadge }}">{{ ucfirst($invoiceStatus) }}</span>
            <a href="{{ route('portal.invoices.print', $invoice) }}" target="_blank" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Print</a>
            <a href="{{ route('portal.invoices.pdf', $invoice) }}" class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">Download PDF</a>
            @if($hasOutstanding)
                <a href="{{ route('portal.payments.index', ['invoice_id' => $invoice->id]) }}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Pay This Invoice</a>
            @endif
        </div>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Net Amount</p>
            <p class="mt-2 text-2xl font-extrabold text-slate-900">NGN {{ number_format((float) $invoice->net_amount, 2) }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Amount Paid</p>
            <p class="mt-2 text-2xl font-extrabold text-emerald-700">NGN {{ number_format((float) $invoice->amount_paid, 2) }}</p>
        </div>
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-red-600">Outstanding</p>
            <p class="mt-2 text-2xl font-extrabold text-red-700">NGN {{ number_format((float) $invoice->balance, 2) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Due Date</p>
            <p class="mt-2 text-lg font-extrabold text-slate-900">{{ $invoice->due_date?->format('d M Y') ?: 'Not set' }}</p>
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-700">Student</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-slate-500">Full Name</dt>
                    <dd class="font-semibold text-slate-800">{{ $invoice->student?->full_name }}</dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-slate-500">Student ID</dt>
                    <dd class="font-semibold text-slate-800">{{ $invoice->student?->admission_number ?: $invoice->student?->registration_number ?: ('STD-' . $invoice->student_id) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-slate-500">Class</dt>
                    <dd class="font-semibold text-slate-800">{{ $invoice->student?->schoolClass?->name ?: 'Not Assigned' }} {{ $invoice->student?->arm?->name ?: '' }}</dd>
                </div>
            </dl>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-700">Invoice Meta</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-slate-500">Invoice Number</dt>
                    <dd class="font-mono text-xs font-semibold text-slate-800">{{ $invoice->invoice_number }}</dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-slate-500">Session / Term</dt>
                    <dd class="font-semibold text-slate-800">{{ $invoice->session?->name ?: 'N/A' }} / {{ $invoice->term?->name ?: 'N/A' }}</dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-slate-500">Generated</dt>
                    <dd class="font-semibold text-slate-800">{{ $invoice->created_at?->format('d M Y, h:i A') ?: 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-slate-700">Invoice Breakdown</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Description</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                        <th class="px-5 py-3 text-right">Discount</th>
                        <th class="px-5 py-3 text-right">Net</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoice->items as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3.5 text-slate-700">{{ $item->description }}</td>
                            <td class="px-5 py-3.5 text-right text-slate-800">NGN {{ number_format((float) $item->amount, 2) }}</td>
                            <td class="px-5 py-3.5 text-right text-slate-600">NGN {{ number_format((float) $item->discount, 2) }}</td>
                            <td class="px-5 py-3.5 text-right font-semibold text-slate-900">NGN {{ number_format((float) $item->net_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-sm text-slate-400">No invoice items were attached.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 text-sm font-semibold text-slate-800">
                    <tr>
                        <td class="px-5 py-3">Total Amount</td>
                        <td colspan="3" class="px-5 py-3 text-right">NGN {{ number_format((float) $invoice->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3">Amount Paid</td>
                        <td colspan="3" class="px-5 py-3 text-right text-emerald-700">NGN {{ number_format((float) $invoice->amount_paid, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3">Balance</td>
                        <td colspan="3" class="px-5 py-3 text-right {{ $hasOutstanding ? 'text-red-700' : 'text-emerald-700' }}">
                            NGN {{ number_format((float) $invoice->balance, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-slate-700">Payment Records</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Reference</th>
                        <th class="px-5 py-3 text-left">Method</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-left">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoice->payments as $payment)
                        @php
                            $paymentStatus = strtolower((string) $payment->status);
                            $paymentStatusClass = match($paymentStatus) {
                                'approved', 'confirmed' => 'bg-emerald-100 text-emerald-700',
                                'pending', 'under_review' => 'bg-amber-100 text-amber-700',
                                'rejected', 'failed', 'cancelled' => 'bg-red-100 text-red-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3.5 font-mono text-xs text-slate-700">{{ $payment->payment_reference }}</td>
                            <td class="px-5 py-3.5 capitalize text-slate-700">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td class="px-5 py-3.5 text-right font-semibold text-slate-900">NGN {{ number_format((float) $payment->amount, 2) }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $paymentStatusClass }}">{{ ucwords(str_replace('_', ' ', $paymentStatus)) }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">{{ optional($payment->payment_date ?: $payment->paid_at)->format('d M Y') ?: 'N/A' }}</td>
                            <td class="px-5 py-3.5">
                                @if($payment->receipt && $payment->isSuccessful())
                                    <a href="{{ route('portal.payments.receipt', $payment->receipt) }}" class="text-xs font-semibold text-indigo-600 hover:underline">View</a>
                                @else
                                    <span class="text-xs text-slate-400">Unavailable</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">No payments have been posted to this invoice yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
