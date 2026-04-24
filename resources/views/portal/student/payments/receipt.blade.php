@extends('layouts.app')
@section('title', 'Payment Receipt')
@section('header', 'Payment Receipt')

@section('content')
<div class="w-full space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Receipt {{ $receipt->receipt_number }}</h1>
            <p class="text-sm text-slate-500">Student ID: {{ $student->admission_number ?: $student->registration_number }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('portal.payments.index') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Back</a>
            <a href="{{ route('portal.payments.receipt.pdf', $receipt) }}" class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Download PDF</a>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-4 sm:grid-cols-2 text-sm">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500">School</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $school?->name }}</p>
                <p class="text-slate-500">{{ implode(', ', array_filter([$school?->address, $school?->city, $school?->state])) }}</p>
                <p class="text-slate-500">{{ $school?->phone }} {{ $school?->email ? ' | ' . $school->email : '' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500">Student</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $student->full_name }}</p>
                <p class="text-slate-500">{{ $student->schoolClass?->name }} {{ $student->arm?->name }}</p>
                <p class="text-slate-500">{{ $student->admission_number ?: $student->registration_number }}</p>
            </div>
        </div>

        <div class="mt-5 rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <tbody class="divide-y divide-slate-100">
                    <tr><td class="px-4 py-3 bg-slate-50 text-slate-600 font-medium w-1/3">Receipt Number</td><td class="px-4 py-3 font-semibold">{{ $receipt->receipt_number }}</td></tr>
                    <tr><td class="px-4 py-3 bg-slate-50 text-slate-600 font-medium">Invoice Number</td><td class="px-4 py-3">{{ $payment->invoice?->invoice_number ?: 'N/A' }}</td></tr>
                    <tr><td class="px-4 py-3 bg-slate-50 text-slate-600 font-medium">Session / Term</td><td class="px-4 py-3">{{ $payment->invoice?->session?->name }} / {{ $payment->invoice?->term?->name }}</td></tr>
                    <tr><td class="px-4 py-3 bg-slate-50 text-slate-600 font-medium">Payment Method</td><td class="px-4 py-3">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td></tr>
                    <tr><td class="px-4 py-3 bg-slate-50 text-slate-600 font-medium">Reference</td><td class="px-4 py-3 font-mono text-xs">{{ $payment->gateway_reference ?: $payment->receipt_number ?: $payment->payment_reference }}</td></tr>
                    <tr><td class="px-4 py-3 bg-slate-50 text-slate-600 font-medium">Date Paid</td><td class="px-4 py-3">{{ optional($payment->payment_date ?: $payment->paid_at)->format('d M Y') ?: '—' }}</td></tr>
                    <tr><td class="px-4 py-3 bg-slate-50 text-slate-600 font-medium">Amount Paid</td><td class="px-4 py-3 text-emerald-700 text-lg font-extrabold">NGN {{ number_format((float) $payment->amount, 2) }}</td></tr>
                    <tr><td class="px-4 py-3 bg-slate-50 text-slate-600 font-medium">Status</td><td class="px-4 py-3"><span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">APPROVED</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

