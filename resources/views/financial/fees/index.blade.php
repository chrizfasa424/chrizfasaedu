@extends('layouts.app')
@section('title', 'Fee Structures')
@section('header', 'Fee Structures')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Fee Structures</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage school fees and levies.</p>
        </div>
        <a href="{{ route('financial.fees.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Fee
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Category</th>
                    <th class="px-5 py-3 text-left">Amount</th>
                    <th class="px-5 py-3 text-left">Session</th>
                    <th class="px-5 py-3 text-left">Class</th>
                    <th class="px-5 py-3 text-left">Compulsory</th>
                    <th class="px-5 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($fees as $fee)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $fee->name }}</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600 capitalize">{{ str_replace('_',' ',$fee->category) }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-700 font-semibold">₦{{ number_format($fee->amount, 2) }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $fee->session?->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $fee->schoolClass?->name ?? 'All' }}</td>
                    <td class="px-5 py-3">
                        @if($fee->is_compulsory)
                        <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Yes</span>
                        @else
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">No</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <form method="POST" action="{{ route('financial.fees.destroy', $fee) }}" onsubmit="return confirm('Delete this fee?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">No fee structures found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $fees->links() }}</div>

</div>
@endsection
