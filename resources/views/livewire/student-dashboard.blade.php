<div>
    <div class="mb-10">
        <h1 class="font-display font-extrabold text-2xl text-ink">Welcome back, {{ explode(' ', auth()->user()->name ?? 'Learner')[0] }}.</h1>
        <p class="text-[13px] font-body text-ink2 mt-1">You've completed 4 lessons this week. Keep it up!</p>
    </div>

    {{-- KPI Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Courses in Progress</span>
            <div class="flex items-end gap-2">
                <span class="font-display font-extrabold text-3xl text-ink leading-none">{{ count($this->continueLearning) }}</span>
            </div>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Completed</span>
            <div class="flex items-end gap-2">
                <span class="font-display font-extrabold text-3xl text-ink leading-none">{{ count($this->completedCourses) }}</span>
            </div>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Available Courses</span>
            <div class="flex items-end gap-2">
                <span class="font-display font-extrabold text-3xl text-ink leading-none">{{ \App\Models\Course::published()->count() }}</span>
            </div>
        </div>
    </div>

    {{-- Continue Learning --}}
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Continue Learning</h2>
            <a href="{{ route('courses.index') }}" class="text-[11px] font-bold text-accent uppercase tracking-widest hover:underline">View all</a>
        </div>

        <div class="space-y-4">
            @forelse($this->continueLearning as $course)
                <div class="bg-surface border border-rule rounded-card p-5 group hover:border-ink transition-colors">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h3 class="font-display font-bold text-sm text-ink mb-1 group-hover:text-accent transition-colors">{{ $course->title }}</h3>
                            <p class="text-[11px] text-ink3 font-body mb-4">{{ $course->instructor->name }}</p>
                            
                            <div class="flex items-center gap-3">
                                <div class="flex-1">
                                    <x-progress-bar :percentage="45" color="#1A43E0" />
                                </div>
                                <span class="text-[11px] font-bold text-ink2 font-body">45%</span>
                            </div>
                        </div>
                        <a href="{{ $course->url }}" class="mt-1 px-5 py-2.5 bg-ink text-white font-display font-bold text-[12px] rounded-card hover:opacity-90 transition-opacity">Resume</a>
                    </div>
                </div>
            @empty
                <div class="bg-surface border border-dashed border-rule rounded-card p-12 text-center">
                    <p class="text-ink3 text-sm">No courses in progress. <a href="{{ route('courses.index') }}" class="text-accent font-bold">Browse courses</a></p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Recommended for you --}}
    <section>
        <h2 class="font-display font-bold text-sm text-ink uppercase tracking-widest mb-6">Recommended for you</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $recommended = \App\Models\Course::published()->with('instructor')->take(2)->get()->map(function($c) {
                    $c->url = route('courses.show', $c->slug);
                    return $c;
                });
            @endphp
            @foreach($recommended as $course)
                <x-course-card :course="$course" />
            @endforeach
        </div>
    </section>
</div>
