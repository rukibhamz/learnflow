@extends('layouts.learn', [
    'courseTitle' => 'Introduction to Web Development',
    'progress' => 38,
    'currentLesson' => 16,
    'totalLessons' => 42,
])

@section('content')
@livewire('lesson-player')
@endsection

@section('sidebar')
<div class="p-4 border-b border-rule">
    <h2 class="font-display font-bold text-[13px] text-ink">Introduction to Web Development</h2>
</div>
<div class="p-4 border-b border-rule">
    <p class="text-[12px] font-body text-ink2">38% complete</p>
    <div class="mt-2 h-[3px] bg-rule rounded-full overflow-hidden">
        <div class="h-full bg-accent rounded-full" style="width: 38%"></div>
    </div>
</div>
<div class="p-4 overflow-y-auto flex-1">
    <p class="font-display font-bold text-[10px] uppercase tracking-wider text-ink3 mb-3">Section 1</p>
    <div class="space-y-0">
        @foreach(['Getting Started' => true, 'HTML Basics' => true, 'CSS Fundamentals' => true, 'JavaScript Intro' => false] as $title => $done)
            <div class="py-2 px-4 flex items-center gap-3 {{ $title === 'JavaScript Intro' ? 'border-l-2 border-accent' : '' }}">
                @if($done)
                    <span class="w-4 h-4 rounded-full bg-success flex items-center justify-center flex-shrink-0">
                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </span>
                    <span class="text-[12px] font-body text-ink3">{{ $title }}</span>
                @else
                    <span class="w-4 h-4 rounded-full border-2 border-accent flex-shrink-0"></span>
                    <span class="text-[12px] font-body font-medium text-ink">{{ $title }}</span>
                @endif
            </div>
        @endforeach
    </div>
</div>
<div class="p-4 border-t border-rule">
    <button class="w-full py-2.5 bg-ink text-white font-display font-bold text-sm rounded-card hover:opacity-90 transition-opacity duration-150">Mark complete</button>
</div>
@endsection
