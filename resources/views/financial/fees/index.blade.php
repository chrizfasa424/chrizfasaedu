@extends('layouts.app')
@section('title', 'Fee Structures')
@section('header', 'Fee Structures')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Fee Structures</h1>
            <p class="mt-0.5 text-sm text-slate-500">Manage school fees and levies.</p>
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

    <div class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between">
        <p class="text-slate-600">
            Showing
            <span class="font-semibold text-slate-800">{{ number_format($fees->firstItem() ?? 0) }}</span>
            to
            <span class="font-semibold text-slate-800">{{ number_format($fees->lastItem() ?? 0) }}</span>
            of
            <span class="font-semibold text-slate-800">{{ number_format($fees->total()) }}</span>
            fee structures
        </p>

        <form method="GET" action="{{ route('financial.fees.index') }}" class="inline-flex items-center gap-2">
            <label for="per_page" class="text-slate-600">Per page:</label>
            <select name="per_page" id="per_page" onchange="this.form.submit()"
                class="rounded-lg border border-slate-300 px-2 py-1.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                @foreach(($allowedPerPage ?? [10, 25, 50, 100]) as $option)
                    <option value="{{ $option }}" @selected((int) ($perPage ?? 10) === (int) $option)>{{ $option }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Name</th>
                        <th class="px-5 py-3 text-left">Category</th>
                        <th class="px-5 py-3 text-left">Amount</th>
                        <th class="px-5 py-3 text-left">Session / Term</th>
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
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium capitalize text-slate-600">{{ str_replace('_', ' ', $fee->category) }}</span>
                        </td>
                        <td class="px-5 py-3 font-semibold text-slate-700">NGN {{ number_format((float) $fee->amount, 2) }}</td>
                        <td class="px-5 py-3 text-slate-600">
                            {{ $fee->session?->name ?? '-' }} / {{ $fee->term?->name ?? 'All Terms' }}
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $fee->schoolClass?->name ?? 'All Classes' }}</td>
                        <td class="px-5 py-3">
                            @if($fee->is_compulsory)
                                <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Yes</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">No</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    class="js-edit-fee text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline"
                                    data-action="{{ route('financial.fees.update', $fee) }}"
                                    data-name="{{ $fee->name }}"
                                    data-category="{{ $fee->category }}"
                                    data-amount="{{ number_format((float) $fee->amount, 2, '.', '') }}"
                                    data-session-id="{{ $fee->session_id }}"
                                    data-term-id="{{ $fee->term_id }}"
                                    data-class-id="{{ $fee->class_id }}"
                                    data-due-date="{{ optional($fee->due_date)->format('Y-m-d') }}"
                                    data-late-fee-amount="{{ number_format((float) $fee->late_fee_amount, 2, '.', '') }}"
                                    data-late-fee-after-days="{{ $fee->late_fee_after_days }}"
                                    data-description="{{ $fee->description }}"
                                    data-is-compulsory="{{ $fee->is_compulsory ? '1' : '0' }}"
                                    data-is-active="{{ $fee->is_active ? '1' : '0' }}"
                                >Edit</button>

                                <form method="POST" action="{{ route('financial.fees.destroy', $fee) }}" onsubmit="return confirm('Delete this fee?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">No fee structures found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $fees->onEachSide(1)->links() }}</div>
</div>

<div id="fee-edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
    <div class="w-full max-w-3xl rounded-2xl border border-slate-200 bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="text-lg font-bold text-slate-900">Edit Fee Structure</h3>
            <button type="button" id="fee-edit-close" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-50">Close</button>
        </div>

        <form id="fee-edit-form" method="POST" class="space-y-4 p-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Fee Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit-name" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Category <span class="text-red-500">*</span></label>
                    <select name="category" id="edit-category" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ ucwords(str_replace('_', ' ', $cat)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Amount (NGN) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="amount" id="edit-amount" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Class (optional)</label>
                    <select name="class_id" id="edit-class-id"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">All classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Session <span class="text-red-500">*</span></label>
                    <select name="session_id" id="edit-session-id" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Select session</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}">{{ $session->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Term (optional)</label>
                    <select name="term_id" id="edit-term-id"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">All terms</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}">{{ $term->name }}{{ $term->session?->name ? ' - ' . $term->session->name : '' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Due Date</label>
                    <input type="date" name="due_date" id="edit-due-date"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Late Fee Amount</label>
                    <input type="number" name="late_fee_amount" id="edit-late-fee-amount" step="0.01" min="0"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-600">Late Fee After (Days)</label>
                    <input type="number" name="late_fee_after_days" id="edit-late-fee-after-days" step="1" min="1"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Description</label>
                <textarea name="description" id="edit-description" rows="2"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"></textarea>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_compulsory" id="edit-is-compulsory" value="1"
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-400">
                    <span>Compulsory fee</span>
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" id="edit-is-active" value="1"
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-400">
                    <span>Active fee</span>
                </label>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-3">
                <button type="button" id="fee-edit-cancel" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const modal = document.getElementById('fee-edit-modal');
    const form = document.getElementById('fee-edit-form');
    const closeBtn = document.getElementById('fee-edit-close');
    const cancelBtn = document.getElementById('fee-edit-cancel');
    const triggers = document.querySelectorAll('.js-edit-fee');

    if (!modal || !form || triggers.length === 0) {
        return;
    }

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    const openModal = () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', () => {
            form.setAttribute('action', trigger.dataset.action || '');
            document.getElementById('edit-name').value = trigger.dataset.name || '';
            document.getElementById('edit-category').value = trigger.dataset.category || '';
            document.getElementById('edit-amount').value = trigger.dataset.amount || '';
            document.getElementById('edit-session-id').value = trigger.dataset.sessionId || '';
            document.getElementById('edit-term-id').value = trigger.dataset.termId || '';
            document.getElementById('edit-class-id').value = trigger.dataset.classId || '';
            document.getElementById('edit-due-date').value = trigger.dataset.dueDate || '';
            document.getElementById('edit-late-fee-amount').value = trigger.dataset.lateFeeAmount || '0';
            document.getElementById('edit-late-fee-after-days').value = trigger.dataset.lateFeeAfterDays || '30';
            document.getElementById('edit-description').value = trigger.dataset.description || '';
            document.getElementById('edit-is-compulsory').checked = trigger.dataset.isCompulsory === '1';
            document.getElementById('edit-is-active').checked = trigger.dataset.isActive === '1';
            openModal();
        });
    });

    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });
    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
})();
</script>
@endpush
