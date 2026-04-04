@extends('layouts.app')
@section('title', 'Payments')
@section('header', 'Payments')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-900">Payments</h1>
        <p class="text-sm text-slate-500 mt-0.5">All payment transactions.</p>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Reference</th>
                    <th class="px-5 py-3 text-left">Student</th>
                    <th class="px-5 py-3 text-left">Invoice</th>
                    <th class="px-5 py-3 text-left">Method</th>
                    <th class="px-5 py-3 text-right">Amount</th>
                    <th class="px-5 py-3 text-left">Date</th>
                    <th class="px-5 py-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($payments as $payment)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-mono text-xs text-slate-600">{{ $payment->payment_reference }}</td>
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $payment->student?->full_name }}</td>
                    <td class="px-5 py-3">
                        <a href="{{ route('financial.invoices.show', $payment->invoice_id) }}" class="text-xs text-indigo-600 hover:underline font-mono">
                            {{ $payment->invoice?->invoice_number }}
                        </a>
                    </td>
                    <td class="px-5 py-3 capitalize text-slate-600">{{ str_replace('_',' ',$payment->payment_method) }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-slate-800">₦{{ number_format($payment->amount, 2) }}</td>
                    <td class="px-5 py-3 text-slate-500 text-xs">{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y') : '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium
                            @if($payment->status === 'confirmed') bg-green-100 text-green-700
                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">No payments recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $payments->links() }}</div>

</div>
@endsection
