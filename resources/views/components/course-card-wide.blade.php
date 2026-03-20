@props(['course'])
@php
    $abbr = strtoupper(substr($course->title ?? 'C', 0, 2));
    $colors = ['#1A43E0', '#1B7A3E', '#96650A', '#5A5A56'];
    $color = $colors[($course->id ?? 0) % 4];
@endphp

<a href="{{ $course->url ?? '#' }}" class="group flex bg-surface border border-rule rounded-card overflow-hidden hover:border-ink transition-all duration-150">
    <div class="w-1 h-full shrink-0" style="background-color: {{ $color }}"></div>
    
    <div class="p-4 flex gap-4 w-full">
        <div class="w-16 h-16 shrink-0 rounded-card flex items-center justify-center font-display font-bold text-white text-lg" style="background-color: {{ $color }}">
            {{ $abbr }}
        </div>
        
        <div class="flex-1 min-w-0">
            <div class="flex justify-between items-start gap-2 mb-1">
                <h3 class="font-display font-bold text-sm text-ink truncate group-hover:text-accent transition-colors">{{ $course->title ?? 'Course Title' }}</h3>
                <span class="font-display font-bold text-sm shrink-0 {{ ($course->price ?? 0) == 0 ? 'text-success' : 'text-ink' }}">
                    {{ format_price($course->price ?? 19) }}
                </span>
            </div>
            
            <p class="text-[11px] text-ink3 font-body mb-2">{{ $course->instructor->name ?? 'Instructor Name' }}</p>
            
            <div class="flex items-center gap-4 text-[11px] text-ink2 font-body">
                <span class="flex items-center gap-1">★ {{ number_format($course->rating ?? 4.8, 1) }}</span>
                <span>{{ $course->lessons_count ?? 12 }} lessons</span>
                <span>{{ $course->duration_hours ?? 4 }} hours</span>
            </div>
        </div>
    </div>
</a>
