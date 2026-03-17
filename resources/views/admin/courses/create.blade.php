@extends('layouts.admin')

@section('title', 'Create Course')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <div class="mb-4">
            <a href="{{ route('admin.courses.index') }}" class="text-[11px] font-bold uppercase tracking-widest text-primary hover:opacity-80 transition-opacity flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Back to Courses
            </a>
        </div>
        <h1 class="font-poppins font-bold text-lg tracking-tight text-ink">Create New Course</h1>
        <p class="text-[13px] font-body text-ink2 mt-1">Fill in the details to create your course.</p>
    </div>

    @livewire('course-form')
</div>
@endsection
