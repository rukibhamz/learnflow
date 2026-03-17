@extends('layouts.app')

@section('title', $quiz->title . ' - ' . $course->title)

@section('content')
<div class="max-w-4xl mx-auto py-8 px-6">
    <div class="mb-6">
        <a href="{{ route('learn.show', $course->slug) }}" class="text-sm text-ink2 hover:text-ink transition-colors">← Back to Course</a>
    </div>

    @livewire('quiz-player', ['quiz' => $quiz])
</div>
@endsection
