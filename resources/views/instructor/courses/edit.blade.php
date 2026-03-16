@extends('layouts.dashboard')

@section('title', 'Edit Course')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="font-display font-extrabold text-2xl text-ink">Edit Course</h1>
        <p class="text-[13px] font-body text-ink2 mt-1">Update your course details and settings.</p>
    </div>

    @livewire('course-form', ['course' => $course])
</div>
@endsection
