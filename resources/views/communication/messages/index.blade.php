@extends('layouts.app')
@section('title', 'Messages')
@section('header', 'Messages')

@section('content')
<div class="space-y-6">

    {{-- Header row --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-slate-500 mt-1">
                Broadcast messages to students, parents, or specific classes.
            </p>
        </div>
        <a href="{{ route('messages.create') }}"
           class="inline-flex items-center gap-2 rounded-2xl bg-[#2D1D5C] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#3A2872]">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Compose Message
        </a>
    </div>

    {{-- Unread replies banner --}}
    @if($totalUnreadReplies > 0)
    <div class="flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-3 text-sm text-amber-800">
        <svg class="h-5 w-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75v-.7V9a6 6 0 1 0-12 0v.05c0 .232 0 .465-.001.697a8.967 8.967 0 0 1-2.311 6.025 23.848 23.848 0 0 0 5.454 1.31"/>
        </svg>
        You have <strong>{{ $totalUnreadReplies }}</strong> unread {{ Str::plural('reply', $totalUnreadReplies) }} from students/parents.
    </div>
    @endif

    {{-- Messages list --}}
    @if($messages->isEmpty())
    <div class="rounded-2xl border border-slate-100 bg-white px-6 py-16 text-center shadow-sm">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-[#2D1D5C]/8">
            <svg class="h-8 w-8 text-[#2D1D5C]/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
            </svg>
        </div>
        <h3 class="text-base font-semibold text-slate-700">No messages sent yet</h3>
        <p class="mt-1 text-sm text-slate-400">Compose your first message to send to students or parents.</p>
        <a href="{{ route('messages.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-[#2D1D5C] px-4 py-2 text-sm font-semibold text-white hover:bg-[#3A2872]">
            Compose Now
        </a>
    </div>
    @else
    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Subject</th>
                    <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Audience</th>
                    <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Recipients</th>
                    <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Read</th>
                    <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Replies</th>
                    <th class="px-4 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Sent</th>
                    <th class="px-4 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($messages as $msg)
                <tr class="group transition hover:bg-slate-50/60">
                    <td class="px-6 py-4">
                        <a href="{{ route('messages.show', $msg) }}" class="font-semibold text-slate-800 hover:text-[#2D1D5C]">
                            {{ $msg->subject }}
                        </a>
                        <p class="mt-0.5 text-xs text-slate-400 line-clamp-1">{{ strip_tags($msg->body) }}</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center rounded-lg bg-[#2D1D5C]/8 px-2.5 py-1 text-xs font-medium text-[#2D1D5C]">
                            {{ $msg->audienceLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-600">
                        {{ number_format($msg->recipients_count) }}
                    </td>
                    <td class="px-4 py-4">
                        @php $readPct = $msg->recipients_count > 0 ? round(($msg->read_count / $msg->recipients_count) * 100) : 0; @endphp
                        <div class="flex items-center gap-2">
                            <div class="h-1.5 w-16 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-emerald-500" style="width: {{ $readPct }}%"></div>
                            </div>
                            <span class="text-xs text-slate-500">{{ $readPct }}%</span>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        @if($msg->unread_replies_count > 0)
                            <a href="{{ route('messages.show', $msg) }}"
                               class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-600 hover:bg-red-100">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                {{ $msg->unread_replies_count }} new
                            </a>
                        @elseif($msg->replies_count > 0)
                            <span class="text-xs text-slate-400">{{ $msg->replies_count }}</span>
                        @else
                            <span class="text-xs text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-xs text-slate-400 whitespace-nowrap">
                        {{ $msg->created_at->format('d M Y') }}<br>
                        <span class="text-slate-300">{{ $msg->created_at->format('g:i A') }}</span>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition">
                            <a href="{{ route('messages.show', $msg) }}"
                               class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 hover:border-[#2D1D5C] hover:text-[#2D1D5C]">
                                View
                            </a>
                            <form action="{{ route('messages.destroy', $msg) }}" method="POST"
                                  onsubmit="return confirm('Delete this message and all replies?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="rounded-lg border border-red-100 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($messages->hasPages())
    <div class="flex justify-end">
        {{ $messages->links() }}
    </div>
    @endif
    @endif

</div>
@endsection
