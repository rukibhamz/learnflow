@extends('layouts.app')

@section('title', 'Question Bank - ' . $course->title)

@section('content')
<div class="max-w-4xl mx-auto py-8 px-6">
    <div class="mb-8">
        <a href="{{ route('instructor.courses.curriculum', $course) }}" class="text-sm text-ink2 hover:text-ink transition-colors">← Back to Curriculum</a>
        <h1 class="font-display font-extrabold text-2xl text-ink mt-2">Question Bank</h1>
        <p class="text-sm text-ink2 mt-1">{{ $course->title }} — Reusable questions that can be imported into any quiz.</p>
    </div>

    @livewire('question-bank-manager', ['courseId' => $course->id])
</div>
@endsection
