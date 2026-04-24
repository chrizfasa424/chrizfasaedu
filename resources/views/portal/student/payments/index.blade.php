@extends('layouts.app')
@section('title', 'My Payments')
@section('header', 'My Payments')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Payments Center</h1>
            <p class="text-sm text-slate-500">Pay school fees, upload proof, and track approval with your student ID.</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-xs text-indigo-700">
                Student ID: <span class="font-semibold">{{ $student->admission_number ?: $student->registration_number }}</span>
            </div>
            <a href="{{ route('portal.invoices.index') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                Full Invoice Page
            </a>
        </div>
    </div>

    @if(!empty($paymentSyncNotice))
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-900 shadow-sm">
            <p class="text-sm font-semibold">{{ $paymentSyncNotice['title'] }}</p>
            <p class="mt-1 text-xs text-amber-800">{{ $paymentSyncNotice['message'] }}</p>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">Invoices</h2>
            </div>
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Invoice</th>
                        <th class="px-4 py-3 text-left">Session / Term</th>
                        <th class="px-4 py-3 text-right">Net</th>
                        <th class="px-4 py-3 text-right">Balance</th>
                        <th class="px-4 py-3 text-left">Fee State</th>
                        <th class="px-4 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $invoice)
                        @php $feeState = $invoice->fee_state; @endphp
                        <tr class="{{ $selectedInvoice && $selectedInvoice->id === $invoice->id ? 'bg-indigo-50' : '' }}">
                            <td class="px-4 py-3 font-mono text-xs text-slate-700">{{ $invoice->invoice_number }}</td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $invoice->session?->name }} / {{ $invoice->term?->name }}</td>
                            <td class="px-4 py-3 text-right text-slate-700">NGN {{ number_format((float) $invoice->net_amount, 2) }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ (float) $invoice->balance > 0 ? 'text-red-600' : 'text-green-600' }}">NGN {{ number_format((float) $invoice->balance, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $feeState === 'active' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ ucfirst($feeState) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($feeState === 'active')
                                    <a href="{{ route('portal.payments.index', ['invoice_id' => $invoice->id]) }}" class="rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Select</a>
                                @else
                                    <span class="text-xs font-medium text-slate-400">Settled</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-slate-400">No invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-700">Submit Payment</h2>
            @if($selectedInvoice && (float) $selectedInvoice->balance > 0)
                <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-600">
                    <div><span class="font-semibold text-slate-700">Invoice:</span> {{ $selectedInvoice->invoice_number }}</div>
                    <div><span class="font-semibold text-slate-700">Balance:</span> NGN {{ number_format((float) $selectedInvoice->balance, 2) }}</div>
                </div>

                <div class="mt-4">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Offline (Bank Transfer / POS)</h3>
                    <form method="POST" action="{{ route('portal.payments.offline.store') }}" enctype="multipart/form-data" class="mt-2 space-y-3">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{ $selectedInvoice->id }}">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Method</label>
                            <select name="payment_method" id="offline-method" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                @forelse($offlineMethods as $code => $name)
                                    <option value="{{ $code }}" {{ old('payment_method') === $code ? 'selected' : '' }}>{{ $name }}</option>
                                @empty
                                    <option value="">No offline method available</option>
                                @endforelse
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Amount</label>
                            <input type="number" step="0.01" min="0.01" max="{{ $selectedInvoice->balance }}" name="amount" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Payment Date</label>
                            <input type="date" name="payment_date" value="{{ now()->toDateString() }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Transaction / POS Reference</label>
                            <input type="text" name="payment_reference" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Sender Account Name (Optional)</label>
                            <input type="text" name="sender_account_name" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Sender Bank (Optional)</label>
                            <input type="text" name="sender_bank" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Upload Teller / Slip</label>
                            <input type="file" name="proof_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
                            <textarea name="notes" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
                        </div>
                        <button class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700" {{ empty($offlineMethods) ? 'disabled' : '' }}>Submit for Verification</button>
                    </form>
                </div>

                <div class="mt-5 border-t border-slate-200 pt-4">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Online Payment (Instant Approval)</h3>
                    <form method="POST" action="{{ route('portal.payments.online.initiate') }}" class="mt-2 space-y-3">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{ $selectedInvoice->id }}">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Gateway</label>
                            <select name="payment_method" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                @forelse($onlineMethods as $code => $name)
                                    <option value="{{ $code }}" {{ old('payment_method') === $code ? 'selected' : '' }}>{{ $name }}</option>
                                @empty
                                    <option value="">No online gateway available</option>
                                @endforelse
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Amount</label>
                            <input type="number" step="0.01" min="0.01" max="{{ $selectedInvoice->balance }}" name="amount" value="{{ number_format((float) $selectedInvoice->balance, 2, '.', '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        </div>
                        <button class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700" {{ empty($onlineMethods) ? 'disabled' : '' }}>Pay Online</button>
                    </form>
                </div>
            @elseif($selectedInvoice)
                <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">
                    This invoice is already settled and now marked as <strong>Inactive</strong>.
                </div>
                <p class="mt-3 text-sm text-slate-500">Select another active invoice to make payment.</p>
            @else
                <p class="mt-3 text-sm text-slate-500">Select an invoice with outstanding balance to continue.</p>
            @endif
        </div>
    </div>

    @if($bankAccounts->count())
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-slate-700">Bank Transfer Details</h2>
            <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @foreach($bankAccounts as $account)
                    <div class="rounded-xl border {{ $account->is_default ? 'border-indigo-300 bg-indigo-50' : 'border-slate-200 bg-slate-50' }} p-3 text-sm">
                        <div class="font-semibold text-slate-800">{{ $account->bank_name }} @if($account->is_default)<span class="ml-1 rounded bg-indigo-600 px-1.5 py-0.5 text-[10px] text-white">Default</span>@endif</div>
                        <div class="mt-1 text-slate-600">{{ $account->account_name }}</div>
                        <div class="font-mono text-slate-800">{{ $account->account_number }}</div>
                        @if($account->instruction_note)
                            <p class="mt-2 text-xs text-slate-500">{{ $account->instruction_note }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-700">Payment History</h2>
        </div>
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Reference</th>
                    <th class="px-4 py-3 text-left">Invoice</th>
                    <th class="px-4 py-3 text-left">Method</th>
                    <th class="px-4 py-3 text-right">Amount</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Receipt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($payments as $payment)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $payment->payment_reference }}</td>
                        <td class="px-4 py-3 text-xs text-slate-600">{{ $payment->invoice?->invoice_number ?: 'N/A' }}</td>
                        <td class="px-4 py-3 capitalize text-slate-600">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-slate-800">NGN {{ number_format((float) $payment->amount, 2) }}</td>
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
                        <td class="px-4 py-3 text-xs text-slate-500">{{ optional($payment->payment_date ?: $payment->paid_at)->format('d M Y') ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @if($payment->receipt && $payment->isSuccessful())
                                <a href="{{ route('portal.payments.receipt', $payment->receipt) }}" class="text-xs text-indigo-600 hover:underline">View</a>
                            @else
                                <span class="text-xs text-slate-400">Unavailable</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-slate-400">No payment history yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">{{ $payments->links() }}</div>
    </div>
</div>
@endsection

