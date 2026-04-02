@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">Total Students</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_students']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">Total Staff</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_staff']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">Pending Admissions</p>
        <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($stats['pending_admissions']) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">Outstanding Fees</p>
        <p class="text-2xl font-bold text-red-600 mt-1">₦{{ number_format($stats['outstanding_fees'], 2) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
        <p class="text-sm text-gray-500">This Month Revenue</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">₦{{ number_format($stats['recent_payments'], 2) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Admissions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Recent Admissions</h3>
        </div>
        <div class="p-5">
            <div class="space-y-3">
                @forelse($recentAdmissions as $admission)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $admission->first_name }} {{ $admission->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ $admission->application_number }} · {{ $admission->class_applied_for }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $admission->status->color() }}-100 text-{{ $admission->status->color() }}-700">
                        {{ $admission->status->label() }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-gray-500">No recent admissions</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Recent Payments</h3>
        </div>
        <div class="p-5">
            <div class="space-y-3">
                @forelse($recentPayments as $payment)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $payment->student?->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ $payment->payment_reference }}</p>
                    </div>
                    <span class="text-sm font-semibold text-emerald-600">₦{{ number_format($payment->amount, 2) }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-500">No recent payments</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
