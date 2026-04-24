@extends('layouts.app')
@section('title', 'Payment Review')
@section('header', 'Payment Review')

@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Payment Review</h1>
            <p class="text-sm text-slate-500 mt-0.5">Reference: <span class="font-mono">{{ $payment->payment_reference }}</span></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('financial.payments.index') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Back</a>
            @if($payment->isSuccessful())
                <a href="{{ route('financial.payments.receipt', $payment) }}" class="rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">Download Receipt</a>
            @endif
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
            <h2 class="text-sm font-semibold text-slate-700">Payment Details</h2>
            <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Student</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $payment->student?->full_name }}</dd>
                    <p class="text-xs text-slate-500">{{ $payment->student?->admission_number ?: $payment->student?->registration_number }}</p>
                    <p class="text-xs text-slate-500">{{ $payment->student?->schoolClass?->name }} {{ $payment->student?->arm?->name }}</p>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Invoice</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ $payment->invoice?->invoice_number ?: 'N/A' }}</dd>
                    <p class="text-xs text-slate-500">{{ $payment->invoice?->session?->name }} / {{ $payment->invoice?->term?->name }}</p>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Payment Method</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Amount Paid</dt>
                    <dd class="mt-1 font-semibold text-slate-900">NGN {{ number_format((float) $payment->amount, 2) }}</dd>
                    <p class="text-xs text-slate-500">Expected: NGN {{ number_format((float) ($payment->amount_expected ?? 0), 2) }}</p>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Status</dt>
                    <dd class="mt-1">
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
                    </dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Payment Date</dt>
                    <dd class="mt-1 font-semibold text-slate-900">{{ optional($payment->payment_date ?: $payment->paid_at)->format('d M Y') ?: '—' }}</dd>
                </div>
            </dl>

            @if($payment->notes)
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700">
                    <p class="text-xs uppercase tracking-wide text-slate-500 mb-1">Student Note</p>
                    {{ $payment->notes }}
                </div>
            @endif

            @if($payment->rejection_reason)
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    <p class="text-xs uppercase tracking-wide text-red-500 mb-1">Rejection Reason</p>
                    {{ $payment->rejection_reason }}
                </div>
            @endif

            @if($payment->verification_note)
                <div class="mt-4 rounded-xl border border-indigo-200 bg-indigo-50 p-3 text-sm text-indigo-700">
                    <p class="text-xs uppercase tracking-wide text-indigo-500 mb-1">Verification Note</p>
                    {{ $payment->verification_note }}
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700">Proof Attachment</h3>
                @if($payment->proof_file_path)
                    <p class="mt-3 text-xs text-slate-500">{{ $payment->proof_original_name ?: basename($payment->proof_file_path) }}</p>
                    <a href="{{ route('financial.payments.proof', $payment) }}" class="mt-3 inline-flex rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700">Download Proof</a>
                @else
                    <p class="mt-3 text-sm text-slate-500">No proof file was uploaded.</p>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700">Verification Action</h3>
                <form method="POST" action="{{ route('financial.payments.verify', $payment) }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Action</label>
                        <select name="action" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            <option value="under_review">Mark Under Review</option>
                            <option value="approve">Approve Payment</option>
                            <option value="reject">Reject Payment</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Verification Note</label>
                        <textarea name="verification_note" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Internal verification note..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Rejection Reason (If rejecting)</label>
                        <textarea name="rejection_reason" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Reason visible to student if rejected..."></textarea>
                    </div>
                    <button class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Save Verification</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
