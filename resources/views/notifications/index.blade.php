@extends('layouts.app')
@section('title', 'Notifications')
@section('header', 'Notifications')

@section('content')
<div class="space-y-6">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Notification Center</h1>
                <p class="mt-1 text-sm text-slate-500">Choose what you want to read.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700">
                    Unread: {{ $bellCount }}
                </span>
                @if($databaseUnreadCount > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-2xl border border-[#2D1D5C]/20 bg-[#2D1D5C]/5 px-4 py-2 text-sm font-semibold text-[#2D1D5C] transition hover:bg-[#2D1D5C] hover:text-white">
                            Mark All Read
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($quickLinks as $link)
            <a href="{{ $link['route'] }}" class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-[#2D1D5C]/30">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $link['label'] }}</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $link['count'] }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $link['description'] }}</p>
            </a>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-500">
                No notification channels available for your role.
            </div>
        @endforelse
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-base font-bold text-slate-900">Recent Notifications</h2>
            <p class="mt-1 text-sm text-slate-500">Open any item to read it.</p>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($recentNotifications as $item)
                <div class="flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-slate-900">{{ $item['title'] }}</p>
                            @if($item['is_unread'])
                                <span class="inline-flex rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-rose-700">
                                    Unread
                                </span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-slate-600">{{ $item['message'] }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ $item['created_at']?->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('notifications.open', $item['id']) }}" class="inline-flex items-center rounded-xl border border-[#2D1D5C]/20 px-3 py-1.5 text-sm font-semibold text-[#2D1D5C] transition hover:bg-[#2D1D5C] hover:text-white">
                        Open
                    </a>
                </div>
            @empty
                <div class="px-6 py-10 text-sm text-slate-500">
                    No recent notifications yet.
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
