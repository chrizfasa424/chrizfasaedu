@extends('layouts.app')
@section('title', 'Bank Accounts')
@section('header', 'Bank Accounts')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">School Bank Accounts</h1>
        <p class="text-sm text-slate-500 mt-0.5">Manage bank transfer details shown to students.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-1">
            <h2 class="text-sm font-semibold text-slate-700">Add Bank Account</h2>
            <form method="POST" action="{{ route('financial.bank-accounts.store') }}" class="mt-4 space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Bank Name</label>
                    <input name="bank_name" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" value="{{ old('bank_name') }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Account Name</label>
                    <input name="account_name" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" value="{{ old('account_name') }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Account Number</label>
                    <input name="account_number" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" value="{{ old('account_number') }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Branch (Optional)</label>
                    <input name="branch" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" value="{{ old('branch') }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Instruction</label>
                    <textarea name="instruction_note" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('instruction_note') }}</textarea>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> Active</label>
                    <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_default" value="1"> Set Default</label>
                </div>
                <button class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Save Bank Account</button>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden lg:col-span-2">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Bank</th>
                        <th class="px-4 py-3 text-left">Account Details</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($accounts as $account)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $account->bank_name }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                <div>{{ $account->account_name }}</div>
                                <div class="font-mono text-xs">{{ $account->account_number }}</div>
                                @if($account->branch)
                                    <div class="text-xs text-slate-500">{{ $account->branch }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $account->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $account->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($account->is_default)
                                        <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-semibold text-indigo-700">Default</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <form method="POST" action="{{ route('financial.bank-accounts.default', $account) }}">
                                        @csrf
                                        <button class="rounded-lg border border-indigo-200 px-2.5 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-50">Set Default</button>
                                    </form>

                                    <button type="button" onclick="document.getElementById('edit-bank-{{ $account->id }}').classList.toggle('hidden')" class="rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50">Edit</button>

                                    <form method="POST" action="{{ route('financial.bank-accounts.destroy', $account) }}" onsubmit="return confirm('Delete this bank account?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-red-200 px-2.5 py-1 text-xs font-medium text-red-700 hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr id="edit-bank-{{ $account->id }}" class="hidden bg-slate-50">
                            <td colspan="4" class="px-4 py-3">
                                <form method="POST" action="{{ route('financial.bank-accounts.update', $account) }}" class="grid gap-3 md:grid-cols-4">
                                    @csrf
                                    @method('PUT')
                                    <input name="bank_name" value="{{ $account->bank_name }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    <input name="account_name" value="{{ $account->account_name }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    <input name="account_number" value="{{ $account->account_number }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    <input name="branch" value="{{ $account->branch }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Branch">
                                    <textarea name="instruction_note" rows="2" class="md:col-span-4 rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $account->instruction_note }}</textarea>
                                    <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" {{ $account->is_active ? 'checked' : '' }}> Active</label>
                                    <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_default" value="1" {{ $account->is_default ? 'checked' : '' }}> Default</label>
                                    <div class="md:col-span-2 text-right">
                                        <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Update</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-10 text-center text-slate-400">No bank accounts created yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3">{{ $accounts->links() }}</div>
        </div>
    </div>
</div>
@endsection
