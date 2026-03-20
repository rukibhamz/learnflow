@extends('layouts.admin')

@section('title', $course->title)

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div class="space-y-1">
            <a href="{{ route('admin.courses.index') }}" class="text-[11px] font-bold uppercase tracking-widest text-primary hover:opacity-80 transition-opacity flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Back to Courses
            </a>
            <h2 class="font-poppins font-bold text-lg tracking-tight text-ink mt-2">{{ $course->title }}</h2>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.edit', $course) }}" class="h-10 px-4 bg-background-light border border-rule rounded-lg text-[13px] font-body font-medium text-ink2 hover:bg-bg transition-colors inline-flex items-center gap-2">
                Edit Course
            </a>
            <a href="{{ route('admin.courses.curriculum', $course) }}" class="h-10 px-4 bg-primary/10 text-primary border border-primary/20 rounded-lg text-[13px] font-body font-medium hover:bg-primary/20 transition-colors inline-flex items-center gap-2">
                Edit Curriculum
            </a>
            <form method="POST" action="{{ route('admin.courses.update-status', $course) }}">
                @csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="h-10 bg-background-light border border-rule rounded-lg px-3 text-[13px] font-body focus:ring-1 focus:ring-primary/30 outline-none">
                    @foreach(['draft', 'pending', 'published', 'rejected', 'archived'] as $s)
                        <option value="{{ $s }}" {{ strtolower($course->status->value ?? $course->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </form>
            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Are you sure you want to delete this course?')">
                @csrf @method('DELETE')
                <button type="submit" class="h-10 px-4 bg-red-50 text-red-600 border border-red-100 rounded-lg text-[13px] font-bold font-body hover:bg-red-100 transition-colors">
                    Delete Course
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-surface border border-rule p-8 space-y-4">
                @if($course->thumbnail)
                    <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-48 object-cover rounded-lg border border-rule" alt="{{ $course->title }}">
                @endif
                <div class="space-y-2">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-ink3">Description</p>
                    <p class="text-[13px] text-ink2 font-body leading-relaxed">{{ $course->description ?? 'No description provided.' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-rule">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-ink3">Instructor</p>
                        <p class="text-[13px] text-ink font-body mt-1">{{ $course->instructor->name ?? 'System' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-ink3">Category</p>
                        <p class="text-[13px] text-ink font-body mt-1 capitalize">{{ $course->category ?? 'Uncategorized' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-ink3">Price</p>
                        <p class="text-[13px] text-ink font-body mt-1">{{ format_price($course->price) }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-ink3">Level</p>
                        <p class="text-[13px] text-ink font-body mt-1 capitalize">{{ $course->level ?? 'All Levels' }}</p>
                    </div>
                </div>
            </div>

            {{-- Curriculum --}}
            <div class="bg-surface border border-rule p-8 space-y-4">
                <h3 class="font-poppins font-bold text-sm text-ink">Curriculum</h3>
                @forelse($course->sections as $section)
                    <div class="border border-rule rounded-lg overflow-hidden">
                        <div class="bg-background-light px-4 py-3 border-b border-rule">
                            <p class="text-[13px] font-bold text-ink font-body">{{ $section->title }}</p>
                        </div>
                        @foreach($section->lessons as $lesson)
                            @php
                                $type = is_object($lesson->type) ? $lesson->type->value : $lesson->type;
                                $icon = match($type ?? '') {
                                    'video' => 'play_circle',
                                    'text' => 'article',
                                    'pdf' => 'picture_as_pdf',
                                    'embed' => 'code',
                                    default => 'description',
                                };
                            @endphp
                            <div class="px-4 py-2.5 flex items-center gap-3 border-b border-rule last:border-0">
                                <span class="material-symbols-outlined text-ink3 text-[16px]">{{ $icon }}</span>
                                <span class="text-[13px] text-ink2 font-body">{{ $lesson->title }}</span>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <p class="text-[13px] text-ink3 font-body">No sections added yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Sidebar Stats --}}
        <div class="space-y-6">
            <div class="bg-surface border border-rule p-6 space-y-5">
                <h3 class="font-poppins font-bold text-sm text-ink">Statistics</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-ink3 font-body">Enrollments</span>
                        <span class="text-[13px] font-bold text-ink font-body">{{ number_format($course->enrollments->count()) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-ink3 font-body">Sections</span>
                        <span class="text-[13px] font-bold text-ink font-body">{{ $course->sections->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-ink3 font-body">Lessons</span>
                        <span class="text-[13px] font-bold text-ink font-body">{{ $course->sections->sum(fn($s) => $s->lessons->count()) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-ink3 font-body">Reviews</span>
                        <span class="text-[13px] font-bold text-ink font-body">{{ $course->reviews->count() }}</span>
                    </div>
                    @if($course->reviews->count())
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] text-ink3 font-body">Avg Rating</span>
                            <span class="text-[13px] font-bold text-ink font-body">{{ number_format($course->reviews->avg('rating'), 1) }} / 5</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-[13px] text-ink3 font-body">Created</span>
                        <span class="text-[13px] text-ink2 font-body">{{ $course->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Recent Reviews --}}
            @if($course->reviews->count())
            <div class="bg-surface border border-rule p-6 space-y-4">
                <h3 class="font-poppins font-bold text-sm text-ink">Recent Reviews</h3>
                @foreach($course->reviews->take(5) as $review)
                    <div class="border-b border-rule pb-3 last:border-0 last:pb-0">
                        <div class="flex items-center justify-between">
                            <span class="text-[13px] font-bold text-ink font-body">{{ $review->user->name ?? 'User' }}</span>
                            <span class="text-[11px] text-amber-500 font-bold">{{ $review->rating }}/5</span>
                        </div>
                        @if($review->comment)
                            <p class="text-[12px] text-ink3 font-body mt-1 line-clamp-2">{{ $review->comment }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
