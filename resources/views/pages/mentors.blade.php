@extends('layouts.app')

@section('title', 'Our Mentors')

@section('content')
    <!-- Hero -->
    <section class="bg-ink text-white">
        <div class="max-w-7xl mx-auto px-6 py-20 lg:py-28 text-center">
            <span class="text-accent font-bold tracking-[0.2em] text-xs uppercase">Meet Our Experts</span>
            <h1 class="text-5xl lg:text-7xl font-extrabold leading-[1.1] mt-4 mb-6">
                Learn from the <span class="text-accent">best.</span>
            </h1>
            <p class="text-lg text-white/70 max-w-2xl mx-auto leading-relaxed">
                Our instructors are industry professionals and thought leaders who bring real-world experience into every lesson.
            </p>
        </div>
    </section>

    <!-- Stats -->
    <section class="border-b border-rule bg-surface">
        <div class="max-w-7xl mx-auto grid grid-cols-3 divide-x divide-rule">
            <div class="py-8 px-8 flex flex-col gap-1 items-center">
                <span class="text-3xl font-bold font-display">{{ $instructors->count() }}</span>
                <span class="text-sm text-ink3 uppercase tracking-widest">Instructors</span>
            </div>
            <div class="py-8 px-8 flex flex-col gap-1 items-center">
                <span class="text-3xl font-bold font-display">{{ $totalCourses }}</span>
                <span class="text-sm text-ink3 uppercase tracking-widest">Courses</span>
            </div>
            <div class="py-8 px-8 flex flex-col gap-1 items-center">
                <span class="text-3xl font-bold font-display">{{ number_format($totalStudents) }}+</span>
                <span class="text-sm text-ink3 uppercase tracking-widest">Students Taught</span>
            </div>
        </div>
    </section>

    <!-- Instructors Grid -->
    <section class="max-w-7xl mx-auto px-6 py-20">
        @if($instructors->isEmpty())
            <div class="text-center py-20">
                <span class="material-symbols-outlined text-ink3 text-[48px] mb-4">school</span>
                <h3 class="font-display font-bold text-xl text-ink mb-2">No mentors yet</h3>
                <p class="text-sm text-ink3 max-w-md mx-auto">Our instructor program is launching soon. Check back later or apply to become an instructor.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($instructors as $instructor)
                    <div class="group bg-surface border border-rule hover:border-accent/30 transition-all duration-300 overflow-hidden">
                        <!-- Avatar -->
                        <div class="aspect-[4/3] bg-bg relative overflow-hidden">
                            <img src="{{ $instructor->avatar_url }}"
                                 alt="{{ $instructor->name }}"
                                 class="w-full h-full object-cover grayscale-[0.3] group-hover:grayscale-0 transition-all duration-500">
                            @if($instructor->courses_count > 0)
                                <div class="absolute bottom-4 left-4 bg-ink/80 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-full">
                                    {{ $instructor->courses_count }} {{ Str::plural('Course', $instructor->courses_count) }}
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="p-6 space-y-3">
                            <div>
                                <h3 class="font-display font-bold text-lg text-ink group-hover:text-accent transition-colors">{{ $instructor->name }}</h3>
                                @if($instructor->website)
                                    <p class="text-xs text-accent font-medium mt-0.5 truncate">{{ parse_url($instructor->website, PHP_URL_HOST) ?? $instructor->website }}</p>
                                @endif
                            </div>

                            @if($instructor->bio)
                                <p class="text-sm text-ink2 leading-relaxed line-clamp-3">{{ $instructor->bio }}</p>
                            @else
                                <p class="text-sm text-ink3 italic">Instructor at LearnFlow</p>
                            @endif

                            <!-- Stats row -->
                            <div class="flex items-center gap-4 pt-3 border-t border-rule/50 text-xs text-ink3">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">menu_book</span>
                                    {{ $instructor->courses_count }} {{ Str::plural('course', $instructor->courses_count) }}
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">group</span>
                                    {{ number_format($instructor->students_count ?? 0) }} {{ Str::plural('student', $instructor->students_count ?? 0) }}
                                </div>
                                @if($instructor->avg_rating)
                                    <div class="flex items-center gap-1 ml-auto">
                                        <span class="material-symbols-outlined text-amber-500 text-[14px]" style="font-variation-settings: 'FILL' 1">star</span>
                                        <span class="font-bold text-ink">{{ number_format($instructor->avg_rating, 1) }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Course list preview -->
                            @if($instructor->courses->isNotEmpty())
                                <div class="pt-3 space-y-2">
                                    @foreach($instructor->courses->take(2) as $course)
                                        <a href="{{ route('courses.show', $course->slug) }}" class="flex items-center gap-3 p-2 -mx-2 rounded-lg hover:bg-bg transition-colors">
                                            @if($course->thumbnail)
                                                <img src="{{ $course->thumbnail }}" class="w-10 h-10 rounded object-cover shrink-0" alt="">
                                            @else
                                                <div class="w-10 h-10 rounded bg-accent/10 flex items-center justify-center shrink-0">
                                                    <span class="material-symbols-outlined text-accent text-[16px]">play_circle</span>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-ink truncate">{{ $course->title }}</p>
                                                <p class="text-[10px] text-ink3 uppercase tracking-wider">{{ $course->level ?? 'All Levels' }}</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- CTA -->
    <section class="max-w-7xl mx-auto px-6 pb-20">
        <div class="bg-ink p-12 lg:p-20 flex flex-col lg:flex-row items-center gap-12">
            <div class="flex-1 flex flex-col gap-4">
                <h2 class="text-4xl lg:text-5xl font-bold font-display text-white leading-tight">Want to teach on LearnFlow?</h2>
                <p class="text-white/80 text-lg">Share your expertise with thousands of students. Apply to become an instructor and start creating courses today.</p>
            </div>
            <a href="{{ route('register') }}" class="bg-accent text-white font-bold px-10 py-4 rounded-card whitespace-nowrap hover:opacity-90 transition-opacity text-base">
                Apply Now
            </a>
        </div>
    </section>
@endsection
