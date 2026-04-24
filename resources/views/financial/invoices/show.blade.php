@extends('layouts.app')
@section('title', 'Invoice '.$invoice->invoice_number)
@section('header', 'Invoice')

@section('content')
<div class="space-y-6 max-w-3xl">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('financial.invoices.index') }}" class="text-sm text-slate-500 hover:text-slate-700">← Invoices</a>
            <span class="text-slate-300">/</span>
            <span class="text-sm font-mono text-slate-800">{{ $invoice->invoice_number }}</span>
        </div>
        @php
            $invoiceStatus = $invoice->status?->value ?? (string) $invoice->status;
            $invoiceStatusLabel = ucfirst(str_replace('_', ' ', $invoiceStatus));
        @endphp
        <div class="flex items-center gap-2">
            <a href="{{ route('financial.invoices.print', $invoice) }}" target="_blank"
                class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                Print Invoice
            </a>
            <a href="{{ route('financial.invoices.pdf', $invoice) }}"
                class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-700">
                Download PDF
            </a>
            <span class="rounded-full px-3 py-1 text-xs font-semibold
                @if($invoiceStatus === 'paid') bg-green-100 text-green-700
                @elseif($invoiceStatus === 'partial') bg-blue-100 text-blue-700
                @elseif($invoiceStatus === 'overdue') bg-red-100 text-red-700
                @else bg-yellow-100 text-yellow-700 @endif">
                {{ $invoiceStatusLabel }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div><dt class="text-xs text-slate-500">Student</dt><dd class="font-semibold text-slate-800 mt-0.5">{{ $invoice->student?->full_name }}</dd></div>
            <div><dt class="text-xs text-slate-500">Student ID</dt><dd class="font-medium text-slate-700 mt-0.5">{{ $invoice->student?->admission_number ?: $invoice->student?->registration_number }}</dd></div>
            <div><dt class="text-xs text-slate-500">Class</dt><dd class="font-medium text-slate-700 mt-0.5">{{ $invoice->student?->schoolClass?->name ?? '—' }}</dd></div>
            <div><dt class="text-xs text-slate-500">Due Date</dt><dd class="font-medium text-slate-700 mt-0.5">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') : '—' }}</dd></div>
        </dl>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">Fee Items</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-2 text-left">Description</th>
                    <th class="px-5 py-2 text-right">Amount</th>
                    <th class="px-5 py-2 text-right">Discount</th>
                    <th class="px-5 py-2 text-right">Net</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($invoice->items as $item)
                <tr>
                    <td class="px-5 py-2 text-slate-800">{{ $item->description }}</td>
                    <td class="px-5 py-2 text-right text-slate-600">NGN {{ number_format($item->amount, 2) }}</td>
                    <td class="px-5 py-2 text-right text-slate-500">NGN {{ number_format($item->discount, 2) }}</td>
                    <td class="px-5 py-2 text-right font-semibold text-slate-800">NGN {{ number_format($item->net_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-50 text-sm font-bold text-slate-800">
                <tr>
                    <td class="px-5 py-2" colspan="3">Total</td>
                    <td class="px-5 py-2 text-right">NGN {{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="px-5 py-1 text-green-700 font-medium" colspan="3">Amount Paid</td>
                    <td class="px-5 py-1 text-right text-green-700">NGN {{ number_format($invoice->total_amount - $invoice->balance, 2) }}</td>
                </tr>
                <tr>
                    <td class="px-5 py-1 {{ $invoice->balance > 0 ? 'text-red-600' : 'text-green-600' }}" colspan="3">Balance Due</td>
                    <td class="px-5 py-1 text-right {{ $invoice->balance > 0 ? 'text-red-600' : 'text-green-600' }}">NGN {{ number_format($invoice->balance, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($invoice->payments->count())
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">Payment History</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-2 text-left">Reference</th>
                    <th class="px-5 py-2 text-left">Method</th>
                    <th class="px-5 py-2 text-left">Date</th>
                    <th class="px-5 py-2 text-right">Amount</th>
                    <th class="px-5 py-2 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($invoice->payments as $payment)
                <tr>
                    <td class="px-5 py-2 font-mono text-xs text-slate-600">{{ $payment->payment_reference }}</td>
                    <td class="px-5 py-2 capitalize text-slate-600">{{ str_replace('_',' ',$payment->payment_method) }}</td>
                    <td class="px-5 py-2 text-slate-600">{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y') : '—' }}</td>
                    <td class="px-5 py-2 text-right font-semibold text-slate-800">NGN {{ number_format($payment->amount, 2) }}</td>
                    <td class="px-5 py-2">
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ in_array(strtolower((string) $payment->status), ['approved', 'confirmed']) ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($invoice->balance > 0)
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Record Payment</h3>
        <form method="POST" action="{{ route('financial.payments.manual') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Amount (NGN) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" required min="1" max="{{ $invoice->balance }}" step="0.01"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Payment Method <span class="text-red-500">*</span></label>
                    <select name="payment_method" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="pos">POS</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Receipt Number / Reference</label>
                <input type="text" name="receipt_number"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700">Record Payment</button>
            </div>
        </form>
    </div>
    @endif

</div>
@endsection
