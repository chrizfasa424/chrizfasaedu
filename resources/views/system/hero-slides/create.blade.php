@extends('layouts.app')

@section('title', 'Create Hero Slide')
@section('header', 'Create Hero Slide')

@section('content')
<form action="{{ route('system.hero-slides.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @include('system.hero-slides._form', ['slide' => $slide, 'maxSlides' => $maxSlides])
</form>
@endsection
