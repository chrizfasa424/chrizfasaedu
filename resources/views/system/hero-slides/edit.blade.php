@extends('layouts.app')

@section('title', 'Edit Hero Slide')
@section('header', 'Edit Hero Slide')

@section('content')
<div class="space-y-6">
    <section class="rounded-[2rem] border border-[#DDD8ED] bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.26em] text-[#2D1D5C]/55">Hero Slider CMS</p>
                <h2 class="mt-3 text-2xl font-black text-slate-900">Edit homepage hero slide</h2>
                <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-600">Refine the message, improve the image quality, and keep each slide focused on one strong promise for parents and learners.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('public.home') }}" target="_blank" class="inline-flex items-center rounded-full border border-[#2D1D5C]/15 bg-white px-5 py-2.5 text-sm font-semibold text-[#2D1D5C] transition duration-200 hover:-translate-y-0.5 hover:border-[#DFE753] hover:bg-[#DFE753]">
                    Preview Homepage
                </a>
                <a href="{{ route('system.hero-slides.index') }}" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition duration-200 hover:-translate-y-0.5 hover:border-[#2D1D5C] hover:text-[#2D1D5C]">
                    Back to Hero Slides
                </a>
            </div>
        </div>
    </section>

    <form action="{{ route('system.hero-slides.update', $slide->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        @include('system.hero-slides._form', ['slide' => $slide, 'maxSlides' => $maxSlides])
    </form>
</div>
@endsection
