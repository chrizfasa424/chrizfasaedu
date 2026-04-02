@extends('layouts.app')

@section('title', 'Edit Hero Slide')
@section('header', 'Edit Hero Slide')

@section('content')
<form action="{{ route('system.hero-slides.update', $slide->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')
    @include('system.hero-slides._form', ['slide' => $slide, 'maxSlides' => $maxSlides])
</form>
@endsection
