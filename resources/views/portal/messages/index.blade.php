@extends('layouts.app')
@section('title', 'My Inbox')
@section('header', 'My Inbox')

@section('content')
<div class="w-full space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">Your messages from the school administration.</p>
        @php $unread = auth()->user()->unreadMessagesCount() @endphp
        @if($unread > 0)
        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-600 border border-red-100">
            <span class="h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
            {{ $unread }} unread
        </span>
        @endif
    </div>

    {{-- Message list --}}
    @if($recipients->isEmpty())
    <div class="rounded-2xl border border-slate-100 bg-white px-6 py-16 text-center shadow-sm">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-[#2D1D5C]/8">
            <svg class="h-8 w-8 text-[#2D1D5C]/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
            </svg>
        </div>
        <h3 class="text-base font-semibold text-slate-700">Your inbox is empty</h3>
        <p class="mt-1 text-sm text-slate-400">Messages from the school will appear here.</p>
    </div>
    @else
    <div class="space-y-2">
        @foreach($recipients as $recipient)
        @php $msg = $recipient->message; $isUnread = !$recipient->isRead(); @endphp
        <a href="{{ route('portal.messages.show', $msg) }}"
           class="group flex items-start gap-4 rounded-2xl border bg-white px-5 py-4 shadow-sm transition hover:shadow-md hover:border-[#2D1D5C]/20
                  {{ $isUnread ? 'border-[#2D1D5C]/20 bg-[#2D1D5C]/[0.02]' : 'border-slate-100' }}">

            {{-- Unread indicator --}}
            <div class="mt-1.5 flex h-5 w-5 shrink-0 items-center justify-center">
                @if($isUnread)
                    <span class="h-2.5 w-2.5 rounded-full bg-[#2D1D5C] shadow-sm shadow-[#2D1D5C]/30"></span>
                @else
                    <span class="h-2 w-2 rounded-full bg-slate-200"></span>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-{{ $isUnread ? 'bold' : 'semibold' }} text-slate-{{ $isUnread ? '900' : '700' }}">
                            {{ $msg->subject }}
                        </p>
                        <p class="mt-0.5 text-xs text-slate-400">
                            From <span class="font-medium text-slate-500">{{ $msg->sender->full_name }}</span>
                        </p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-xs text-slate-400 whitespace-nowrap">{{ $msg->created_at->format('d M Y') }}</p>
                        <p class="text-[10px] text-slate-300">{{ $msg->created_at->format('g:i A') }}</p>
                    </div>
                </div>
                <p class="mt-2 line-clamp-2 text-xs text-slate-500">{{ Str::limit(strip_tags($msg->body), 120) }}</p>
            </div>

            {{-- Arrow --}}
            <div class="mt-1 shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-[#2D1D5C]">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                </svg>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($recipients->hasPages())
    <div class="flex justify-end">{{ $recipients->links() }}</div>
    @endif
    @endif

</div>
@endsection
