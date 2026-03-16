@extends('layouts.learn')

@section('title', $course->title ?? 'Lesson')

@section('content')
@livewire('lesson-player', ['course' => $course])
@endsection

@section('sidebar')
<div class="p-6">
    <h3 class="font-display font-bold text-sm text-ink mb-6">Course Content</h3>

    <div class="space-y-4">
        @foreach($course->sections()->with('lessons')->orderBy('order')->get() as $section)
        <div>
            <div class="mb-3 text-[11px] font-bold uppercase tracking-widest text-ink3">
                {{ $section->title }}
            </div>
            <div class="space-y-1">
                @foreach($section->lessons as $lesson)
                <div class="w-full text-left px-3 py-2.5 rounded-card flex items-start gap-3 text-ink2">
                    <div class="w-4 h-4 mt-0.5 border border-rule flex items-center justify-center shrink-0"></div>
                    <div class="flex-1">
                        <p class="text-[12px] font-medium leading-tight">{{ $lesson->order }}. {{ $lesson->title }}</p>
                        @if($lesson->duration_seconds)
                        <span class="text-[10px] text-ink3 mt-1 block">
                            {{ floor($lesson->duration_seconds / 60) }}:{{ str_pad($lesson->duration_seconds % 60, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
