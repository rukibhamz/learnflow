@extends('layouts.app')

@php
    $couponCode = session('coupon_code');
    $couponValidation = null;
    $discountAmount = 0.0;
    if (auth()->check() && $couponCode && $course->price > 0) {
        $couponValidation = app(\App\Services\CouponService::class)->validate($couponCode, auth()->user(), (float) $course->price);
        if (! $couponValidation->valid) {
            session()->forget('coupon_code');
            $couponValidation = null;
        } else {
            $discountAmount = (float) $couponValidation->discount_amount;
        }
    }
@endphp

@section('title', $course->title . ' - LearnFlow')

@section('content')
<div x-data="{ showPreviewModal: false, previewLesson: null }">
    {{-- Hero Section --}}
    <div class="bg-ink text-white">
        <div class="max-w-7xl mx-auto px-6 py-12 lg:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                {{-- Left Content --}}
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-3 py-1 bg-white/20 text-white text-xs font-bold uppercase rounded-full">{{ ucfirst($course->level?->value ?? 'All Levels') }}</span>
                        @if($course->category)
                            <span class="text-white/60 text-sm">{{ $course->category }}</span>
                        @endif
                    </div>

                    <h1 class="font-display font-extrabold text-3xl md:text-4xl lg:text-5xl leading-tight mb-6">{{ $course->title }}</h1>
                    
                    @if($course->short_description)
                        <p class="text-lg text-white/80 mb-6 max-w-2xl">{{ $course->short_description }}</p>
                    @endif

                    {{-- Meta Info --}}
                    <div class="flex flex-wrap items-center gap-6 mb-6">
                        @if($course->reviews_avg_rating)
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-bold text-amber-400">{{ number_format($course->reviews_avg_rating, 1) }}</span>
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="material-symbols-outlined text-[18px] {{ $i <= round($course->reviews_avg_rating) ? 'text-amber-400' : 'text-white/30' }}" style="font-variation-settings: 'FILL' 1">star</span>
                                    @endfor
                                </div>
                                <span class="text-white/60">({{ number_format($course->reviews_count) }} reviews)</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-2 text-white/80">
                            <span class="material-symbols-outlined text-[20px]">group</span>
                            {{ number_format($course->enrollments_count) }} students enrolled
                        </div>
                    </div>

                    {{-- Instructor --}}
                    <div class="flex items-center gap-4">
                        <img src="{{ $course->instructor?->avatar_url }}" alt="{{ $course->instructor?->name }}" class="w-12 h-12 rounded-full border-2 border-white/30">
                        <div>
                            <p class="text-sm text-white/60">Created by</p>
                            <p class="font-medium text-white">{{ $course->instructor?->name ?? 'Unknown Instructor' }}</p>
                        </div>
                    </div>

                    {{-- Course Stats --}}
                    <div class="flex flex-wrap gap-6 mt-8 pt-6 border-t border-white/20">
                        <div class="flex items-center gap-2 text-white/80">
                            <span class="material-symbols-outlined text-[20px]">schedule</span>
                            {{ floor($totalDuration / 3600) }}h {{ floor(($totalDuration % 3600) / 60) }}m total
                        </div>
                        <div class="flex items-center gap-2 text-white/80">
                            <span class="material-symbols-outlined text-[20px]">play_lesson</span>
                            {{ $totalLessons }} lessons
                        </div>
                        <div class="flex items-center gap-2 text-white/80">
                            <span class="material-symbols-outlined text-[20px]">folder</span>
                            {{ $curriculumSections->count() }} sections
                        </div>
                        @if($course->language)
                            <div class="flex items-center gap-2 text-white/80">
                                <span class="material-symbols-outlined text-[20px]">language</span>
                                {{ strtoupper($course->language) }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Sidebar Card (Desktop) --}}
                <div class="hidden lg:block">
                    <div class="bg-surface text-ink rounded-2xl shadow-2xl overflow-hidden sticky top-24">
                        {{-- Thumbnail --}}
                        <div class="aspect-video bg-bg relative">
                            @if($course->getFirstMediaUrl('thumbnail'))
                                <img src="{{ $course->getFirstMediaUrl('thumbnail') }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-primary/10">
                                    <span class="material-symbols-outlined text-[64px] text-primary/30">school</span>
                                </div>
                            @endif
                        </div>

                        <div class="p-6">
                            {{-- Price --}}
                            <div class="flex items-center gap-3 mb-6">
                                <span class="font-display font-extrabold text-4xl {{ $course->price > 0 ? 'text-ink' : 'text-green-600' }}">
                                    {{ format_price($course->price) }}
                                </span>
                            </div>

                            {{-- CTA Button --}}
                            @auth
                                @if($isEnrolled)
                                    <a href="{{ route('learn.show', $course->slug) }}" class="block w-full py-4 bg-primary text-white font-display font-bold text-center rounded-xl hover:opacity-90 transition-opacity">
                                        Continue Learning
                                    </a>
                                @elseif(! $prerequisitesMet)
                                    <div class="w-full py-4 bg-gray-300 text-gray-500 font-display font-bold text-center rounded-xl cursor-not-allowed">
                                        Complete Prerequisites First
                                    </div>
                                @else
                                    @if($course->price > 0)
                                        <div class="space-y-4">
                                            @livewire('coupon-field', ['orderAmount' => (float) $course->price], key('coupon-field-'.$course->id))

                                            @if($couponValidation && $discountAmount > 0)
                                                <div class="text-xs text-ink2 border border-rule rounded-lg p-3 bg-bg">
                                                    <div class="flex items-center justify-between">
                                                        <span>Subtotal</span>
                                                        <span>{{ format_price((float) $course->price) }}</span>
                                                    </div>
                                                    <div class="flex items-center justify-between mt-1">
                                                        <span>Discount ({{ strtoupper($couponValidation->code) }})</span>
                                                        <span class="text-green-700 font-medium">-${{ number_format($discountAmount, 2) }}</span>
                                                    </div>
                                                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-rule">
                                                        <span class="font-bold">Total</span>
                                                        <span class="font-bold">{{ format_price(max(0, (float) $course->price - $discountAmount)) }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            <form action="{{ route('checkout.course', $course) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full py-4 bg-ink text-white font-display font-bold rounded-xl hover:opacity-90 transition-opacity">
                                                    Checkout
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <form action="{{ route('enrolments.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                                            <button type="submit" class="w-full py-4 bg-ink text-white font-display font-bold rounded-xl hover:opacity-90 transition-opacity">
                                                Enrol for Free
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            @else
                                <a href="{{ route('login') }}?redirect={{ urlencode(request()->url()) }}" class="block w-full py-4 bg-ink text-white font-display font-bold text-center rounded-xl hover:opacity-90 transition-opacity">
                                    Login to Enrol
                                </a>
                            @endauth

                            {{-- Features --}}
                            <div class="mt-6 pt-6 border-t border-rule space-y-3">
                                <div class="flex items-center gap-3 text-sm text-ink2">
                                    <span class="material-symbols-outlined text-[20px] text-green-500">check_circle</span>
                                    Full lifetime access
                                </div>
                                <div class="flex items-center gap-3 text-sm text-ink2">
                                    <span class="material-symbols-outlined text-[20px] text-green-500">check_circle</span>
                                    Access on mobile and desktop
                                </div>
                                <div class="flex items-center gap-3 text-sm text-ink2">
                                    <span class="material-symbols-outlined text-[20px] text-green-500">check_circle</span>
                                    Certificate of completion
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile Sticky CTA --}}
    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-surface border-t border-rule p-4 z-40">
        <div class="flex items-center justify-between gap-4">
            <div>
                <span class="font-display font-extrabold text-2xl {{ $course->price > 0 ? 'text-ink' : 'text-green-600' }}">
                    {{ format_price($course->price) }}
                </span>
            </div>
            @auth
                @if($isEnrolled)
                    <a href="{{ route('learn.show', $course->slug) }}" class="flex-1 py-3 bg-primary text-white font-display font-bold text-center rounded-xl">
                        Continue Learning
                    </a>
                @elseif(! $prerequisitesMet)
                    <span class="flex-1 py-3 bg-gray-300 text-gray-500 font-display font-bold text-center rounded-xl cursor-not-allowed">
                        Prerequisites Required
                    </span>
                @else
                    @if($course->price > 0)
                        <form action="{{ route('checkout.course', $course) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-3 bg-ink text-white font-display font-bold rounded-xl">
                                Checkout
                            </button>
                        </form>
                    @else
                        <form action="{{ route('enrolments.store') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                            <button type="submit" class="w-full py-3 bg-ink text-white font-display font-bold rounded-xl">
                                Enrol Free
                            </button>
                        </form>
                    @endif
                @endif
            @else
                <a href="{{ route('login') }}" class="flex-1 py-3 bg-ink text-white font-display font-bold text-center rounded-xl">
                    Login to Enrol
                </a>
            @endauth
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-6 py-12 lg:pb-12 pb-32">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-12">
                {{-- What You'll Learn --}}
                @if($course->outcomes && count($course->outcomes) > 0)
                <section>
                    <h2 class="font-display font-bold text-2xl text-ink mb-6">What you'll learn</h2>
                    <div class="bg-surface border border-rule rounded-xl p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($course->outcomes as $outcome)
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-[20px] text-green-500 shrink-0 mt-0.5">check_circle</span>
                                    <span class="text-sm text-ink2">{{ $outcome }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
                @endif

                {{-- Requirements --}}
                @if($course->requirements && count($course->requirements) > 0)
                <section>
                    <h2 class="font-display font-bold text-2xl text-ink mb-6">Requirements</h2>
                    <ul class="space-y-3">
                        @foreach($course->requirements as $requirement)
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[18px] text-ink3 shrink-0 mt-0.5">arrow_right</span>
                                <span class="text-sm text-ink2">{{ $requirement }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
                @endif

                {{-- Prerequisites --}}
                @if($prerequisites->isNotEmpty())
                <section>
                    <h2 class="font-display font-bold text-2xl text-ink mb-6">Prerequisites</h2>
                    <div class="bg-surface border border-rule rounded-xl p-6 space-y-3">
                        <p class="text-sm text-ink2 mb-4">Complete these courses before enrolling:</p>
                        @foreach($prerequisites as $prereq)
                            @php
                                $completed = auth()->check()
                                    ? \App\Models\Enrollment::where('user_id', auth()->id())
                                        ->where('course_id', $prereq->id)
                                        ->whereNotNull('completed_at')
                                        ->exists()
                                    : false;
                            @endphp
                            <a href="{{ route('courses.show', $prereq->slug) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-bg transition-colors">
                                <span class="material-symbols-outlined text-[20px] {{ $completed ? 'text-green-500' : 'text-ink3' }}" style="font-variation-settings: 'FILL' {{ $completed ? '1' : '0' }}">
                                    {{ $completed ? 'check_circle' : 'radio_button_unchecked' }}
                                </span>
                                <span class="text-sm {{ $completed ? 'text-ink line-through' : 'text-primary font-medium' }}">{{ $prereq->title }}</span>
                                @if($completed)
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-green-600 ml-auto">Completed</span>
                                @endif
                            </a>
                        @endforeach

                        @auth
                            @if(! $prerequisitesMet)
                                <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                    <p class="text-xs text-amber-800 font-medium">You must complete all prerequisites before you can enrol in this course.</p>
                                </div>
                            @endif
                        @endauth
                    </div>
                </section>
                @endif

                {{-- Course Curriculum --}}
                <section>
                    <h2 class="font-display font-bold text-2xl text-ink mb-6">Course Curriculum</h2>
                    <div class="bg-surface border border-rule rounded-xl overflow-hidden divide-y divide-rule" x-data="{ openSection: 0 }">
                        @foreach($curriculumSections as $index => $section)
                            <div>
                                <button @click="openSection = openSection === {{ $index }} ? null : {{ $index }}" 
                                    class="w-full flex items-center justify-between p-5 text-left hover:bg-bg transition-colors">
                                    <div class="flex items-center gap-4">
                                        <span class="material-symbols-outlined text-[20px] text-ink3 transition-transform" :class="{ 'rotate-90': openSection === {{ $index }} }">chevron_right</span>
                                        <div>
                                            <h3 class="font-display font-bold text-sm text-ink">{{ $section['title'] ?? '' }}</h3>
                                            <p class="text-xs text-ink3 mt-1">{{ count($section['lessons'] ?? []) }} lessons · {{ floor(collect($section['lessons'] ?? [])->sum('duration_seconds') / 60) }} min</p>
                                        </div>
                                    </div>
                                </button>
                                <div x-show="openSection === {{ $index }}" x-collapse class="border-t border-rule bg-bg">
                                    @foreach(($section['lessons'] ?? []) as $lesson)
                                        <div class="flex items-center gap-4 px-5 py-3 {{ !$loop->last ? 'border-b border-rule' : '' }}">
                                            @php
                                                $typeIcons = ['video' => 'play_circle', 'text' => 'article', 'pdf' => 'picture_as_pdf', 'embed' => 'code'];
                                            @endphp
                                            <span class="material-symbols-outlined text-[20px] text-ink3">{{ $typeIcons[$lesson['type'] ?? 'video'] ?? 'description' }}</span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-ink truncate">{{ $lesson['title'] ?? '' }}</p>
                                            </div>
                                            <div class="flex items-center gap-3 shrink-0">
                                                @if(!empty($lesson['is_preview']))
                                                    <button @click="showPreviewModal = true; previewLesson = { id: {{ $lesson['id'] ?? 0 }}, title: '{{ addslashes($lesson['title'] ?? '') }}', type: '{{ $lesson['type'] ?? '' }}', url: '{{ $lesson['content_url'] ?? '' }}', body: {{ json_encode($lesson['content_body'] ?? null) }} }" 
                                                        class="px-2 py-1 bg-primary/10 text-primary text-[10px] font-bold uppercase rounded hover:bg-primary/20 transition-colors">
                                                        Preview
                                                    </button>
                                                @endif
                                                @if(!empty($lesson['duration_seconds']))
                                                    <span class="text-xs text-ink3">{{ gmdate('i:s', $lesson['duration_seconds']) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Description --}}
                @if($course->description)
                <section>
                    <h2 class="font-display font-bold text-2xl text-ink mb-6">Description</h2>
                    <div class="prose prose-sm max-w-none text-ink2">
                        {!! $course->description !!}
                    </div>
                </section>
                @endif

                {{-- Instructor --}}
                <section>
                    <h2 class="font-display font-bold text-2xl text-ink mb-6">Your Instructor</h2>
                    <div class="bg-surface border border-rule rounded-xl p-6">
                        <div class="flex items-start gap-6">
                            <img src="{{ $course->instructor?->avatar_url }}" alt="{{ $course->instructor?->name }}" class="w-24 h-24 rounded-full">
                            <div class="flex-1">
                                <h3 class="font-display font-bold text-lg text-ink">{{ $course->instructor?->name }}</h3>
                                @if($course->instructor?->bio)
                                    <p class="text-sm text-ink2 mt-2">{{ $course->instructor->bio }}</p>
                                @endif
                                <div class="flex items-center gap-6 mt-4 text-sm text-ink3">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[18px]">school</span>
                                        {{ $instructorCourseCount ?? 0 }} courses
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[18px]">group</span>
                                        {{ number_format($instructorStudentCount ?? 0) }} students
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Reviews --}}
                @if($course->reviews->count() > 0)
                <section>
                    <h2 class="font-display font-bold text-2xl text-ink mb-6">Student Reviews</h2>
                    <div class="space-y-6">
                        @foreach($course->reviews as $review)
                            <div class="bg-surface border border-rule rounded-xl p-6">
                                <div class="flex items-start gap-4">
                                    <img src="{{ $review->user?->avatar_url }}" alt="{{ $review->user?->name }}" class="w-12 h-12 rounded-full">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-medium text-ink">{{ $review->user?->name }}</h4>
                                            <span class="text-xs text-ink3">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="flex mb-3">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="material-symbols-outlined text-[16px] {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}" style="font-variation-settings: 'FILL' 1">star</span>
                                            @endfor
                                        </div>
                                        @if($review->comment)
                                            <p class="text-sm text-ink2">{{ $review->comment }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
                @endif
            </div>

            {{-- Empty space for sticky sidebar on desktop --}}
            <div class="hidden lg:block"></div>
        </div>
    </div>

    {{-- Preview Modal --}}
    <div x-show="showPreviewModal" x-cloak 
        class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4"
        @click.self="showPreviewModal = false"
        @keydown.escape.window="showPreviewModal = false">
        <div class="bg-surface rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden" @click.stop>
            <div class="flex items-center justify-between p-4 border-b border-rule">
                <h3 class="font-display font-bold text-lg text-ink" x-text="previewLesson?.title"></h3>
                <button @click="showPreviewModal = false" class="p-2 hover:bg-bg rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                <template x-if="previewLesson?.type === 'video' && previewLesson?.url">
                    <div class="aspect-video bg-black rounded-lg overflow-hidden">
                        <iframe :src="previewLesson.url.includes('youtube') ? previewLesson.url.replace('watch?v=', 'embed/') : previewLesson.url" 
                            class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                    </div>
                </template>
                <template x-if="previewLesson?.type === 'text' && previewLesson?.body">
                    <div class="prose prose-sm max-w-none" x-html="previewLesson.body"></div>
                </template>
                <template x-if="previewLesson?.type === 'pdf'">
                    <p class="text-center text-ink3 py-12">PDF preview not available. Enrol to access.</p>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection
