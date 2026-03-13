@props(['course'])
@php
    $abbr = strtoupper(substr($course->title ?? 'C', 0, 2));
    $colors = ['#EEF1FF', '#E8F5EE', '#FFF8E6', '#F5F4F0'];
    $textColors = ['#1A43E0', '#1B7A3E', '#96650A', '#5A5A56'];
    $colorIndex = ($course->id ?? 0) % 4;
@endphp

<a href="{{ $course->url ?? '#' }}" class="group block bg-surface border border-rule transition-all duration-150 hover:border-ink rounded-card overflow-hidden">
    <div class="h-[110px] w-full flex items-center justify-center font-display font-bold text-2xl" 
         style="background-color: {{ $colors[$colorIndex] }}; color: {{ $textColors[$colorIndex] }};">
        {{ $abbr }}
    </div>
    <div class="p-4">
        <h3 class="font-display font-bold text-[13px] text-ink line-clamp-2 leading-tight mb-2 group-hover:text-accent transition-colors">{{ $course->title ?? 'Course Title' }}</h3>
        
        <div class="flex items-center gap-1.5 mb-2">
            <x-icon name="star-filled" class="w-4 h-4 text-amber-500 shrink-0" />
            <span class="text-xs text-ink3 font-body">{{ number_format($course->rating ?? 4.8, 1) }}</span>
            <span class="text-[11px] text-ink3 font-body">({{ $course->reviews_count ?? 120 }})</span>
        </div>

        <p class="text-[11px] text-ink3 font-body truncate mb-3">{{ $course->instructor->name ?? 'Instructor Name' }}</p>

        <div class="flex items-center justify-between">
            <span class="font-display font-bold text-sm {{ ($course->price ?? 0) == 0 ? 'text-success' : 'text-ink' }}">
                {{ ($course->price ?? 0) == 0 ? 'Free' : '$' . number_format($course->price ?? 19, 2) }}
            </span>
        </div>
    </div>
</a>
