@extends('layouts.app')
@section('title', 'Inventory')
@section('header', 'Inventory & Assets')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Inventory & Assets</h1>
            <p class="text-sm text-slate-500 mt-0.5">Track school assets and equipment.</p>
        </div>
        <button onclick="document.getElementById('add-asset-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Record Asset
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Code</th>
                    <th class="px-5 py-3 text-left">Category</th>
                    <th class="px-5 py-3 text-left">Location</th>
                    <th class="px-5 py-3 text-center">Qty</th>
                    <th class="px-5 py-3 text-left">Condition</th>
                    <th class="px-5 py-3 text-right">Value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($assets as $asset)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $asset->name }}</td>
                    <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ $asset->asset_code ?? '—' }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $asset->category ?? '—' }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $asset->location ?? '—' }}</td>
                    <td class="px-5 py-3 text-center text-slate-700">{{ $asset->quantity }}</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium capitalize
                            @if($asset->condition === 'new') bg-green-100 text-green-700
                            @elseif($asset->condition === 'good') bg-blue-100 text-blue-700
                            @elseif($asset->condition === 'fair') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ $asset->condition ?? '—' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right text-slate-700">{{ $asset->purchase_price ? '₦'.number_format($asset->purchase_price, 2) : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">No assets recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $assets->links() }}</div>

</div>

{{-- Add Asset Modal --}}
<div id="add-asset-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-900">Record Asset</h2>
            <button onclick="document.getElementById('add-asset-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('assets.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Asset Code</label>
                    <input type="text" name="asset_code" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Category</label>
                    <input type="text" name="category" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Location</label>
                    <input type="text" name="location" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Quantity</label>
                    <input type="number" name="quantity" min="1" value="1" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Condition</label>
                    <select name="condition" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @foreach(['new','good','fair','poor','damaged'] as $c)
                        <option value="{{ $c }}">{{ ucfirst($c) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Purchase Price (₦)</label>
                    <input type="number" name="purchase_price" min="0" step="0.01" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Purchase Date</label>
                    <input type="date" name="purchase_date" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Supplier</label>
                <input type="text" name="supplier" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('add-asset-modal').classList.add('hidden')"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
