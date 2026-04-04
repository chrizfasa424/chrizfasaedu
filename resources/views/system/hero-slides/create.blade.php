@extends('layouts.app')

@section('title', 'Create Hero Slide')
@section('header', 'Create Hero Slide')

@section('content')
<div class="space-y-6">
    <section class="rounded-[2rem] border border-[#DDD8ED] bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.26em] text-[#2D1D5C]/55">Hero Slider CMS</p>
                <h2 class="mt-3 text-2xl font-black text-slate-900">Create a new homepage hero slide</h2>
                <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-600">Build one focused message for families, attach a strong wide image, and guide visitors toward the next action.</p>
            </div>
            <a href="{{ route('system.hero-slides.index') }}" class="inline-flex items-center rounded-full border border-[#2D1D5C]/15 bg-white px-5 py-2.5 text-sm font-semibold text-[#2D1D5C] transition duration-200 hover:-translate-y-0.5 hover:border-[#DFE753] hover:bg-[#DFE753]">
                Back to Hero Slides
            </a>
        </div>
    </section>

    <form action="{{ route('system.hero-slides.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @include('system.hero-slides._form', ['slide' => $slide, 'maxSlides' => $maxSlides])
    </form>
</div>
@endsection
