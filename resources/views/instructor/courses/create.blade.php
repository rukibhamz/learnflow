@extends('layouts.dashboard')

@section('title', 'Create Course')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="font-display font-extrabold text-2xl text-ink">Create New Course</h1>
        <p class="text-[13px] font-body text-ink2 mt-1">Fill in the details to create your course.</p>
    </div>

    @livewire('course-form')
</div>
@endsection
