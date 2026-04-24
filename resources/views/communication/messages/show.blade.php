@extends('layouts.app')
@section('title', $message->subject)
@section('header', 'Message Detail')

@section('content')
<div class="space-y-6">

    {{-- Back + delete --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('messages.index') }}"
           class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-[#2D1D5C]">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
            </svg>
            All Messages
        </a>
        <form action="{{ route('messages.destroy', $message) }}" method="POST"
              onsubmit="return confirm('Delete this message and all replies?')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-red-100 bg-red-50 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-100 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                </svg>
                Delete
            </button>
        </form>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Left: message info --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Message card --}}
            <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 px-5 py-4">
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">Message</p>
                    <h2 class="mt-1 text-base font-bold text-slate-800 leading-snug">{{ $message->subject }}</h2>
                </div>
                <div class="divide-y divide-slate-50 text-sm">
                    <div class="flex items-start justify-between gap-2 px-5 py-3">
                        <span class="text-slate-400 shrink-0">From</span>
                        <span class="font-medium text-slate-700 text-right">{{ $message->sender->full_name }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-2 px-5 py-3">
                        <span class="text-slate-400 shrink-0">Audience</span>
                        <span class="rounded-lg bg-[#2D1D5C]/8 px-2 py-0.5 text-xs font-semibold text-[#2D1D5C] text-right">
                            {{ $message->audienceLabel() }}
                        </span>
                    </div>
                    <div class="flex items-start justify-between gap-2 px-5 py-3">
                        <span class="text-slate-400 shrink-0">Sent</span>
                        <span class="font-medium text-slate-700 text-right">{{ $message->created_at->format('d M Y, g:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Reach stats --}}
            <div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-5 space-y-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Reach</p>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-[#2D1D5C]/6 p-3 text-center">
                        <p class="text-2xl font-extrabold text-[#2D1D5C]">{{ number_format($recipientsCount) }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">Sent To</p>
                    </div>
                    <div class="rounded-xl bg-emerald-50 p-3 text-center">
                        <p class="text-2xl font-extrabold text-emerald-600">{{ number_format($readCount) }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">Read</p>
                    </div>
                </div>
                @if($recipientsCount > 0)
                @php $pct = round(($readCount / $recipientsCount) * 100); @endphp
                <div>
                    <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                        <span>Open rate</span><span class="font-semibold">{{ $pct }}%</span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-emerald-500 transition-all duration-700" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endif
            </div>

        </div>

        {{-- Right: body + replies --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Body --}}
            <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-50 px-5 py-3">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Message Body</p>
                </div>
                <div class="px-5 py-5 prose prose-sm max-w-none text-slate-700 leading-relaxed">
                    {!! \App\Support\RichTextSanitizer::sanitize((string) $message->body) !!}
                </div>
            </div>

            {{-- Replies thread --}}
            <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 px-5 py-4 flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">
                        Replies
                        <span class="ml-1.5 rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-600">{{ $message->replies->count() }}</span>
                    </p>
                </div>

                @if($message->replies->isEmpty())
                <div class="px-5 py-10 text-center">
                    <p class="text-sm text-slate-400">No replies yet.</p>
                </div>
                @else
                <div class="divide-y divide-slate-50">
                    @foreach($message->replies as $reply)
                    <div class="px-5 py-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#2D1D5C] text-xs font-bold text-white">
                                {{ strtoupper(substr($reply->sender->first_name, 0, 1) . substr($reply->sender->last_name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-semibold text-slate-800">{{ $reply->sender->full_name }}</span>
                                    <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-500">
                                        {{ ucfirst($reply->sender->role->value) }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $reply->created_at->format('d M Y, g:i A') }}</span>
                                </div>
                                <div class="mt-2 prose prose-sm max-w-none text-slate-600 leading-relaxed">
                                    {!! \App\Support\RichTextSanitizer::sanitize((string) $reply->body) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
