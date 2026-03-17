@extends('layouts.learn')

@section('title', $course->title ?? 'Lesson')

@section('content')
@livewire('lesson-player', ['course' => $course])
@endsection

@section('sidebar')
@php
    $curriculum = $course->cachedCurriculum();
    $userId = auth()->id();
    $allLessonIds = collect($curriculum['sections'] ?? [])->flatMap(fn ($s) => collect($s['lessons'] ?? [])->pluck('id'));
    $completedIds = $userId
        ? \App\Models\LessonProgress::where('user_id', $userId)->whereIn('lesson_id', $allLessonIds)->pluck('lesson_id')->toArray()
        : [];
    $totalLessons = $allLessonIds->count();
    $completedCount = count($completedIds);
    $progressPct = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
@endphp
<div class="p-6">
    <h3 class="font-display font-bold text-sm text-ink mb-2">Course Content</h3>
    <div class="flex items-center gap-3 mb-6">
        <div class="flex-1 h-1.5 bg-rule rounded-full overflow-hidden">
            <div class="h-full bg-accent rounded-full transition-all" style="width: {{ $progressPct }}%"></div>
        </div>
        <span class="text-[11px] font-bold text-ink3 tabular-nums">{{ $completedCount }}/{{ $totalLessons }}</span>
    </div>

    <div class="space-y-4">
        @foreach(($curriculum['sections'] ?? []) as $section)
        <div>
            <div class="mb-3 text-[11px] font-bold uppercase tracking-widest text-ink3">
                {{ $section['title'] ?? '' }}
            </div>
            <div class="space-y-1">
                @foreach(($section['lessons'] ?? []) as $lesson)
                @php $isCompleted = in_array($lesson['id'] ?? 0, $completedIds); @endphp
                <button
                    onclick="Livewire.dispatch('selectLesson', { id: {{ $lesson['id'] ?? 0 }} })"
                    class="w-full text-left px-3 py-2.5 rounded-card flex items-start gap-3 hover:bg-surface transition-colors {{ $isCompleted ? 'text-ink' : 'text-ink2' }}"
                >
                    <div class="w-4 h-4 mt-0.5 rounded border flex items-center justify-center shrink-0 {{ $isCompleted ? 'bg-accent border-accent' : 'border-rule' }}">
                        @if($isCompleted)
                            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-medium leading-tight">{{ $lesson['order'] ?? '' }}. {{ $lesson['title'] ?? '' }}</p>
                        @if(!empty($lesson['duration_seconds']))
                        <span class="text-[10px] text-ink3 mt-1 block">
                            {{ floor($lesson['duration_seconds'] / 60) }}:{{ str_pad($lesson['duration_seconds'] % 60, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        @endif
                    </div>
                </button>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
