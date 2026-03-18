@extends('layouts.admin')

@section('title', 'Blog Management')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold font-display text-ink">Blog Posts</h1>
    </div>

    @livewire('admin-blogs')
@endsection
