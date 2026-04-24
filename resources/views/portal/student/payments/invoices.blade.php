@extends('layouts.app')
@section('title', 'My Invoices')
@section('header', 'My Invoices')

@section('content')
<div class="w-full space-y-6">
    <section class="overflow-hidden rounded-3xl border border-indigo-200/80 bg-gradient-to-r from-indigo-600 via-[#3F37C9] to-indigo-700 p-6 text-white shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight">Invoice Center</h1>
                <p class="mt-2 text-sm text-indigo-100">Track all billed fees, due balances, and invoice payment progress in one place.</p>
            </div>
            <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm">
                <div class="text-xs uppercase tracking-wide text-indigo-100/90">Student ID</div>
                <div class="mt-1 font-semibold">{{ $student->admission_number ?: $student->registration_number ?: ('STD-' . $student->id) }}</div>
            </div>
        </div>
    </section>

    @if(!empty($paymentSyncNotice))
        <section class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900 shadow-sm">
            <p class="text-sm font-semibold">{{ $paymentSyncNotice['title'] }}</p>
            <p class="mt-1 text-xs text-amber-800">{{ $paymentSyncNotice['message'] }}</p>
        </section>
    @endif

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Invoices</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ number_format($totalInvoices) }}</p>
        </div>
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-red-600">Outstanding</p>
            <p class="mt-2 text-3xl font-extrabold text-red-700">{{ number_format($outstandingCount) }}</p>
            <p class="mt-1 text-xs text-red-600/80">NGN {{ number_format($totalOutstandingAmount, 2) }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Cleared</p>
            <p class="mt-2 text-3xl font-extrabold text-emerald-700">{{ number_format($paidCount) }}</p>
            <p class="mt-1 text-xs text-emerald-600/80">Paid so far</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Net Vs Paid</p>
            <p class="mt-2 text-lg font-extrabold text-slate-900">NGN {{ number_format($totalPaidAmount, 2) }}</p>
            <p class="mt-1 text-xs text-slate-500">of NGN {{ number_format($totalNetAmount, 2) }}</p>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('portal.invoices.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="xl:col-span-2">
                <label for="q" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Search</label>
                <input id="q" name="q" type="text" value="{{ $search }}" placeholder="Invoice number"
                    class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            </div>
            <div>
                <label for="status" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                <select id="status" name="status" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    <option value="all" @selected($statusFilter === 'all' || $statusFilter === '')>All Statuses</option>
                    @foreach(['pending', 'partial', 'paid', 'overdue', 'waived', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected($statusFilter === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="session_id" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Session</label>
                <select id="session_id" name="session_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    <option value="">All Sessions</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}" @selected((int) $sessionFilter === (int) $session->id)>{{ $session->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="term_id" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Term</label>
                <select id="term_id" name="term_id" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    <option value="">All Terms</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" @selected((int) $termFilter === (int) $term->id)>{{ $term->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2 md:col-span-2 xl:col-span-5">
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Filter
                </button>
                <a href="{{ route('portal.invoices.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
                <a href="{{ route('portal.payments.index') }}" class="ml-auto inline-flex items-center justify-center rounded-xl border border-indigo-200 bg-indigo-50 px-5 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                    Open Payments
                </a>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Invoice</th>
                        <th class="px-5 py-3 text-left">Session / Term</th>
                        <th class="px-5 py-3 text-right">Net Amount</th>
                        <th class="px-5 py-3 text-right">Amount Paid</th>
                        <th class="px-5 py-3 text-right">Balance</th>
                        <th class="px-5 py-3 text-center">Fee State</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $invoice)
                        @php
                            $status = strtolower((string) ($invoice->status?->value ?? $invoice->status));
                            $feeState = $invoice->fee_state;
                            $statusBadge = match($status) {
                                'paid' => 'bg-emerald-100 text-emerald-700',
                                'partial' => 'bg-blue-100 text-blue-700',
                                'overdue' => 'bg-red-100 text-red-700',
                                'waived' => 'bg-violet-100 text-violet-700',
                                'cancelled' => 'bg-slate-200 text-slate-700',
                                default => 'bg-amber-100 text-amber-700',
                            };
                            $hasOutstanding = (float) $invoice->balance > 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-5 py-3.5">
                                <p class="font-mono text-xs font-semibold text-slate-700">{{ $invoice->invoice_number }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $invoice->items_count }} fee item(s)</p>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-600">
                                {{ $invoice->session?->name ?: 'N/A' }} / {{ $invoice->term?->name ?: 'N/A' }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-semibold text-slate-800">NGN {{ number_format((float) $invoice->net_amount, 2) }}</td>
                            <td class="px-5 py-3.5 text-right text-slate-700">NGN {{ number_format((float) $invoice->amount_paid, 2) }}</td>
                            <td class="px-5 py-3.5 text-right font-semibold {{ $hasOutstanding ? 'text-red-600' : 'text-emerald-700' }}">
                                NGN {{ number_format((float) $invoice->balance, 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $feeState === 'active' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ ucfirst($feeState) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusBadge }}">{{ ucfirst($status) }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('portal.invoices.show', $invoice) }}" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">View</a>
                                    @if($hasOutstanding)
                                        <a href="{{ route('portal.payments.index', ['invoice_id' => $invoice->id]) }}" class="rounded-lg bg-indigo-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-indigo-700">Pay Now</a>
                                    @endif
                                    <a href="{{ route('portal.invoices.print', $invoice) }}" target="_blank" class="rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">Print</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <p class="text-sm font-semibold text-slate-700">No invoices found in this filter.</p>
                                <p class="mt-1 text-xs text-slate-400">Try another status, session, term, or clear search.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-4 py-3">
            {{ $invoices->links() }}
        </div>
    </section>
</div>
@endsection
