@extends('layouts.app')
@section('title', 'Financial Report')
@section('header', 'Financial Report')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-900">Financial Dashboard</h1>
        <p class="text-sm text-slate-500 mt-0.5">Revenue, collections, and outstanding fees.</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Revenue (All Time)</p>
            <p class="mt-2 text-3xl font-bold text-green-600">₦{{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Outstanding Fees</p>
            <p class="mt-2 text-3xl font-bold text-red-600">₦{{ number_format($outstandingFees, 2) }}</p>
        </div>
    </div>

    {{-- Monthly Revenue Table --}}
    @if($monthlyRevenue->count())
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700">Monthly Revenue ({{ now()->year }})</h3>
        </div>
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-2 text-left">Month</th>
                    <th class="px-5 py-2 text-right">Amount</th>
                    <th class="px-5 py-2 text-left w-1/2">Bar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @php $maxMonthly = $monthlyRevenue->max() ?: 1; @endphp
                @foreach(range(1,12) as $m)
                @php $amount = $monthlyRevenue[$m] ?? 0; @endphp
                <tr>
                    <td class="px-5 py-2 text-slate-600">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</td>
                    <td class="px-5 py-2 text-right font-semibold text-slate-800">₦{{ number_format($amount, 2) }}</td>
                    <td class="px-5 py-2">
                        @if($amount > 0)
                        <div class="h-2 rounded-full bg-green-200">
                            <div class="h-2 rounded-full bg-green-500" style="width: {{ ($amount / $maxMonthly) * 100 }}%"></div>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Payments by Method --}}
    @if($paymentsByMethod->count())
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Payments by Method</h3>
        <div class="space-y-3">
            @php $total = $paymentsByMethod->sum() ?: 1; @endphp
            @foreach($paymentsByMethod as $method => $amount)
            <div class="flex items-center gap-3">
                <span class="w-28 text-sm capitalize text-slate-600">{{ str_replace('_',' ',$method) }}</span>
                <div class="flex-1 h-2 rounded-full bg-slate-100">
                    <div class="h-2 rounded-full bg-indigo-500" style="width: {{ ($amount / $total) * 100 }}%"></div>
                </div>
                <span class="text-sm font-semibold text-slate-800 w-28 text-right">₦{{ number_format($amount, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
