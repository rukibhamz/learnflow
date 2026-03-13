@extends('layouts.dashboard')

@section('title', 'My Courses')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h1 class="font-display font-extrabold text-2xl text-ink">My Courses</h1>
            <p class="text-[13px] font-body text-ink2 mt-1">Continue learning from where you left off.</p>
        </div>
        <a href="{{ route('courses.index') }}" class="px-5 py-2.5 bg-accent text-white font-display font-bold text-[12px] rounded-card hover:opacity-90 transition-opacity">Browse Courses</a>
    </div>

    <div class="space-y-4">
        @forelse(auth()->user()->enrollments()->with(['course.instructor'])->get() as $enrollment)
            @php($course = $enrollment->course)
            <div class="bg-surface border border-rule rounded-card p-5 group hover:border-ink transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="font-display font-bold text-sm text-ink mb-1 group-hover:text-accent transition-colors">{{ $course->title }}</h3>
                        <p class="text-[11px] text-ink3 font-body mb-4">{{ $course->instructor->name ?? 'Instructor' }}</p>
                        <x-progress-bar :percentage="$enrollment->progress_percentage" color="#1A43E0" />
                    </div>
                    <a href="{{ route('learn.show', $course) }}" class="mt-1 px-5 py-2.5 bg-ink text-white font-display font-bold text-[12px] rounded-card hover:opacity-90 transition-opacity">Resume</a>
                </div>
            </div>
        @empty
            <div class="bg-surface border border-dashed border-rule rounded-card p-12 text-center">
                <p class="text-ink3 text-sm">No courses yet. <a href="{{ route('courses.index') }}" class="text-accent font-bold">Browse courses</a> to get started.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
