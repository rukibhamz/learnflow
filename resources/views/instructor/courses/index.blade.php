@extends('layouts.dashboard')

@section('title', 'My Courses')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-10">
        <h1 class="font-display font-extrabold text-2xl text-ink">My Courses</h1>
        <p class="text-[13px] font-body text-ink2 mt-1">Manage and track your course content.</p>
    </div>

    @livewire('instructor-course-index')
</div>
@endsection
