@extends('layouts.app')

@section('title', 'Hero Slides')
@section('header', 'Hero Slider CMS')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Homepage Hero Slider</h3>
                <p class="mt-1 text-sm text-gray-600">Manage up to 4 premium hero slides. Every public-facing text, image, button, and right-side card is controlled here.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm">
                    {{ $slides->count() }}/{{ $maxSlides }} configured
                </span>
                @if($slides->count() < $maxSlides)
                    <a href="{{ route('system.hero-slides.create') }}" class="inline-flex items-center rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-black">
                        Add Slide
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if($slides->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
            <h3 class="text-lg font-semibold text-gray-900">No hero slides yet</h3>
            <p class="mt-2 text-sm text-gray-500">Create your first slide to power the homepage hero carousel.</p>
            <a href="{{ route('system.hero-slides.create') }}" class="mt-5 inline-flex items-center rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-black">
                Create First Slide
            </a>
        </div>
    @else
        <div class="grid gap-5 xl:grid-cols-2">
            @foreach($slides as $slide)
                <article class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                    <div class="grid gap-0 lg:grid-cols-[0.9fr_1.1fr]">
                        <div class="h-full border-b border-gray-200 bg-gray-50 lg:border-b-0 lg:border-r">
                            <img src="{{ asset('storage/' . ltrim($slide->image_path, '/')) }}" alt="{{ $slide->title }}" class="h-full min-h-64 w-full object-cover">
                        </div>
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-700">{{ $slide->badge_text }}</p>
                                    <h3 class="mt-2 text-2xl font-semibold leading-tight text-gray-900">{{ $slide->title }}</h3>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $slide->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $slide->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <p class="mt-2 text-xs font-semibold uppercase tracking-[0.16em] text-gray-400">Order {{ $slide->order }}</p>
                                </div>
                            </div>

                            <p class="mt-4 text-sm leading-relaxed text-gray-600">{{ \Illuminate\Support\Str::limit($slide->subtitle, 170) }}</p>

                            <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $slide->school_name }}</p>
                                <h4 class="mt-2 text-lg font-semibold text-slate-900">{{ $slide->right_card_title }}</h4>
                                <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ \Illuminate\Support\Str::limit($slide->right_card_text, 120) }}</p>
                            </div>

                            <div class="mt-5 grid gap-2 text-sm text-gray-600">
                                <div><span class="font-semibold text-gray-900">Button 1:</span> {{ $slide->button_1_text }} <span class="text-gray-400">({{ $slide->button_1_link }})</span></div>
                                <div><span class="font-semibold text-gray-900">Button 2:</span> {{ $slide->button_2_text }} <span class="text-gray-400">({{ $slide->button_2_link }})</span></div>
                            </div>

                            <div class="mt-6 flex flex-wrap gap-2">
                                <a href="{{ route('system.hero-slides.edit', $slide->id) }}" class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:border-gray-400 hover:text-gray-900">
                                    Edit
                                </a>

                                <form action="{{ route('system.hero-slides.toggle', $slide->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $slide->is_active ? 0 : 1 }}">
                                    <button type="submit" class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-semibold text-white transition {{ $slide->is_active ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                                        {{ $slide->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <form action="{{ route('system.hero-slides.destroy', $slide->id) }}" method="POST" onsubmit="return confirm('Delete this hero slide permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection
