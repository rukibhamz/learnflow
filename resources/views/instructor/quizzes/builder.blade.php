@extends('layouts.app')

@section('title', 'Quiz Builder - ' . $lesson->title)

@section('content')
<div class="max-w-4xl mx-auto py-8 px-6">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('instructor.lessons.edit', $lesson) }}" class="text-sm text-ink2 hover:text-ink transition-colors">← Back to Lesson</a>
            <h1 class="font-display font-extrabold text-2xl text-ink mt-2">Quiz Builder</h1>
            <p class="text-sm text-ink2 mt-1">{{ $lesson->title }}</p>
        </div>
    </div>

    @livewire('quiz-builder', ['lessonId' => $lesson->id])
</div>
@endsection
