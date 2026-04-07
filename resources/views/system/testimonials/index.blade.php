@extends('layouts.app')

@section('title', 'Testimonials Moderation')
@section('header', 'Testimonials Moderation')

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        Public testimonials are not shown until approved here. Use bulk actions to manage multiple submissions at once.
    </div>

    {{-- ═══════════════════════════════════════
         PENDING
    ═══════════════════════════════════════ --}}
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800">Pending Testimonials</h3>
                <p class="text-xs text-slate-400 mt-0.5">Review and approve or reject before they appear publicly.</p>
            </div>
            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">{{ $pendingTestimonials->total() }}</span>
        </div>

        <form method="POST" action="{{ route('system.testimonials.bulk') }}" id="form-pending">
            @csrf
            <input type="hidden" name="action" value="" id="bulk-action-pending">

            <div id="bulk-bar-pending" style="display:none;" class="items-center gap-3 bg-slate-50 px-6 py-3 border-b border-slate-100 text-sm flex">
                <span class="font-semibold text-slate-600" id="bulk-count-pending">0 selected</span>
                <button type="button" onclick="submitBulk('pending','approve')"
                    class="rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 text-xs font-semibold">Approve Selected</button>
                <button type="button" onclick="submitBulk('pending','reject')"
                    class="rounded-lg bg-slate-600 hover:bg-slate-700 text-white px-3 py-1.5 text-xs font-semibold">Reject Selected</button>
                <button type="button" onclick="submitBulk('pending','delete')"
                    class="rounded-lg bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 text-xs font-semibold">Delete Selected</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" class="select-all rounded border-slate-300" data-group="pending" title="Select all">
                            </th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-left">Rating</th>
                            <th class="px-4 py-3 text-left">Message</th>
                            <th class="px-4 py-3 text-left">Submitted</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pendingTestimonials as $t)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="ids[]" value="{{ $t->id }}" class="row-check rounded border-slate-300" data-group="pending">
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $t->full_name }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $t->role_title ?: '—' }}</td>
                                <td class="px-4 py-3 font-semibold text-amber-600">{{ max(1,min(5,(int)$t->rating)) }}/5</td>
                                <td class="px-4 py-3 text-slate-700">{{ \Illuminate\Support\Str::limit($t->message, 110) }}</td>
                                <td class="px-4 py-3 text-slate-400 whitespace-nowrap">{{ $t->created_at?->diffForHumans() }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button"
                                            onclick="doAction('{{ route('system.testimonials.approve', $t->id) }}')"
                                            class="rounded-lg bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white">Approve</button>
                                        <button type="button"
                                            onclick="doAction('{{ route('system.testimonials.reject', $t->id) }}')"
                                            class="rounded-lg bg-slate-500 hover:bg-slate-600 px-3 py-1.5 text-xs font-semibold text-white">Reject</button>
                                        <button type="button"
                                            onclick="doAction('{{ route('system.testimonials.destroy', $t->id) }}',true)"
                                            class="rounded-lg bg-red-600 hover:bg-red-700 px-3 py-1.5 text-xs font-semibold text-white">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-slate-400">No pending testimonials.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
        <div class="px-6 py-4 border-t border-slate-100">{{ $pendingTestimonials->links() }}</div>
    </section>

    {{-- ═══════════════════════════════════════
         APPROVED
    ═══════════════════════════════════════ --}}
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800">Approved Testimonials</h3>
                <p class="text-xs text-slate-400 mt-0.5">These are live on the public homepage.</p>
            </div>
            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">{{ $approvedTestimonials->total() }}</span>
        </div>

        <form method="POST" action="{{ route('system.testimonials.bulk') }}" id="form-approved">
            @csrf
            <input type="hidden" name="action" value="" id="bulk-action-approved">

            <div id="bulk-bar-approved" style="display:none;" class="items-center gap-3 bg-slate-50 px-6 py-3 border-b border-slate-100 text-sm flex">
                <span class="font-semibold text-slate-600" id="bulk-count-approved">0 selected</span>
                <button type="button" onclick="submitBulk('approved','reject')"
                    class="rounded-lg bg-slate-600 hover:bg-slate-700 text-white px-3 py-1.5 text-xs font-semibold">Reject Selected</button>
                <button type="button" onclick="submitBulk('approved','delete')"
                    class="rounded-lg bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 text-xs font-semibold">Delete Selected</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" class="select-all rounded border-slate-300" data-group="approved" title="Select all">
                            </th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Rating</th>
                            <th class="px-4 py-3 text-left">Message</th>
                            <th class="px-4 py-3 text-left">Approved</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($approvedTestimonials as $t)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="ids[]" value="{{ $t->id }}" class="row-check rounded border-slate-300" data-group="approved">
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $t->full_name }}</td>
                                <td class="px-4 py-3 font-semibold text-amber-600">{{ max(1,min(5,(int)$t->rating)) }}/5</td>
                                <td class="px-4 py-3 text-slate-700">{{ \Illuminate\Support\Str::limit($t->message, 110) }}</td>
                                <td class="px-4 py-3 text-slate-400 whitespace-nowrap">{{ $t->reviewed_at?->diffForHumans() ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button"
                                            onclick="doAction('{{ route('system.testimonials.reject', $t->id) }}')"
                                            class="rounded-lg bg-slate-500 hover:bg-slate-600 px-3 py-1.5 text-xs font-semibold text-white">Reject</button>
                                        <button type="button"
                                            onclick="doAction('{{ route('system.testimonials.destroy', $t->id) }}',true)"
                                            class="rounded-lg bg-red-600 hover:bg-red-700 px-3 py-1.5 text-xs font-semibold text-white">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400">No approved testimonials yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
        <div class="px-6 py-4 border-t border-slate-100">{{ $approvedTestimonials->links() }}</div>
    </section>

    {{-- ═══════════════════════════════════════
         REJECTED
    ═══════════════════════════════════════ --}}
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-800">Rejected Testimonials</h3>
                <p class="text-xs text-slate-400 mt-0.5">Dismissed submissions. Approve them if needed or delete permanently.</p>
            </div>
            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">{{ $rejectedTestimonials->total() }}</span>
        </div>

        <form method="POST" action="{{ route('system.testimonials.bulk') }}" id="form-rejected">
            @csrf
            <input type="hidden" name="action" value="" id="bulk-action-rejected">

            <div id="bulk-bar-rejected" style="display:none;" class="items-center gap-3 bg-slate-50 px-6 py-3 border-b border-slate-100 text-sm flex">
                <span class="font-semibold text-slate-600" id="bulk-count-rejected">0 selected</span>
                <button type="button" onclick="submitBulk('rejected','approve')"
                    class="rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 text-xs font-semibold">Approve Selected</button>
                <button type="button" onclick="submitBulk('rejected','delete')"
                    class="rounded-lg bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 text-xs font-semibold">Delete Selected</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" class="select-all rounded border-slate-300" data-group="rejected" title="Select all">
                            </th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Message</th>
                            <th class="px-4 py-3 text-left">Reviewed</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rejectedTestimonials as $t)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="ids[]" value="{{ $t->id }}" class="row-check rounded border-slate-300" data-group="rejected">
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-900">{{ $t->full_name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ \Illuminate\Support\Str::limit($t->message, 110) }}</td>
                                <td class="px-4 py-3 text-slate-400 whitespace-nowrap">{{ $t->reviewed_at?->diffForHumans() ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button"
                                            onclick="doAction('{{ route('system.testimonials.approve', $t->id) }}')"
                                            class="rounded-lg bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white">Approve</button>
                                        <button type="button"
                                            onclick="doAction('{{ route('system.testimonials.destroy', $t->id) }}',true)"
                                            class="rounded-lg bg-red-600 hover:bg-red-700 px-3 py-1.5 text-xs font-semibold text-white">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">No rejected testimonials.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
        <div class="px-6 py-4 border-t border-slate-100">{{ $rejectedTestimonials->links() }}</div>
    </section>

</div>

<script>
var CSRF = '{{ csrf_token() }}';

// Single-row action — creates a temporary form and submits it
function doAction(url, confirmFirst) {
    if (confirmFirst && !confirm('Delete this testimonial? This cannot be undone.')) return;
    var f = document.createElement('form');
    f.method = 'POST';
    f.action = url;
    f.innerHTML = '<input type="hidden" name="_token" value="' + CSRF + '">';
    document.body.appendChild(f);
    f.submit();
}

// Checkbox: select-all
document.querySelectorAll('.select-all').forEach(function (sa) {
    sa.addEventListener('change', function () {
        var g = this.dataset.group;
        document.querySelectorAll('.row-check[data-group="' + g + '"]').forEach(function (cb) {
            cb.checked = sa.checked;
        });
        updateBar(g);
    });
});

// Checkbox: individual rows
document.querySelectorAll('.row-check').forEach(function (cb) {
    cb.addEventListener('change', function () { updateBar(this.dataset.group); });
});

function updateBar(group) {
    var checked  = document.querySelectorAll('.row-check[data-group="' + group + '"]:checked').length;
    var total    = document.querySelectorAll('.row-check[data-group="' + group + '"]').length;
    var bar      = document.getElementById('bulk-bar-' + group);
    var counter  = document.getElementById('bulk-count-' + group);
    var sa       = document.querySelector('.select-all[data-group="' + group + '"]');

    bar.style.display = checked > 0 ? 'flex' : 'none';
    if (counter) counter.textContent = checked + ' selected';
    if (sa) {
        sa.indeterminate = checked > 0 && checked < total;
        sa.checked = total > 0 && checked === total;
    }
}

function submitBulk(group, action) {
    var checked = document.querySelectorAll('.row-check[data-group="' + group + '"]:checked');
    if (!checked.length) return;
    if (!confirm('Are you sure you want to ' + action + ' ' + checked.length + ' testimonial(s)?')) return;
    document.getElementById('bulk-action-' + group).value = action;
    document.getElementById('form-' + group).submit();
}
</script>
@endsection
