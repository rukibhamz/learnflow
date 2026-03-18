@extends('layouts.admin')

@section('title', 'Hero Section Management')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold font-display text-ink">Hero Slider</h1>
    </div>

    @livewire('admin-hero-slides')
@endsection
