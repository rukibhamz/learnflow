<div>
    {{-- Welcome Header --}}
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h1 class="font-display font-extrabold text-2xl text-ink">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="text-[13px] font-body text-ink2 mt-1">Continue your learning journey.</p>
        </div>
        <a href="{{ route('courses.index') }}" class="px-5 py-2.5 bg-accent text-white font-display font-bold text-[12px] rounded-card hover:opacity-90 transition-opacity">Browse Courses</a>
    </div>

    {{-- Stats Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-surface border border-rule rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary">school</span>
                </div>
                <div>
                    <p class="text-2xl font-display font-bold text-ink">{{ $inProgressEnrollments->count() }}</p>
                    <p class="text-xs text-ink3">Courses in Progress</p>
                </div>
            </div>
        </div>
        <div class="bg-surface border border-rule rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-600">verified</span>
                </div>
                <div>
                    <p class="text-2xl font-display font-bold text-ink">{{ $completedEnrollments->count() }}</p>
                    <p class="text-xs text-ink3">Completed Courses</p>
                </div>
            </div>
        </div>
        <div class="bg-surface border border-rule rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-600">bookmark</span>
                </div>
                <div>
                    <p class="text-2xl font-display font-bold text-ink">{{ $wishlistCourses->count() }}</p>
                    <p class="text-xs text-ink3">Wishlist Items</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-rule mb-10">
        <nav class="flex gap-8">
            <button wire:click="setTab('in_progress')" 
                class="pb-4 text-sm font-medium transition-colors relative {{ $activeTab === 'in_progress' ? 'text-primary' : 'text-ink3 hover:text-ink' }}">
                In Progress
                @if($activeTab === 'in_progress')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary"></span>
                @endif
            </button>
            <button wire:click="setTab('completed')" 
                class="pb-4 text-sm font-medium transition-colors relative {{ $activeTab === 'completed' ? 'text-primary' : 'text-ink3 hover:text-ink' }}">
                Completed
                @if($activeTab === 'completed')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary"></span>
                @endif
            </button>
            <button wire:click="setTab('wishlist')" 
                class="pb-4 text-sm font-medium transition-colors relative {{ $activeTab === 'wishlist' ? 'text-primary' : 'text-ink3 hover:text-ink' }}">
                Wishlist
                @if($activeTab === 'wishlist')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary"></span>
                @endif
            </button>
        </nav>
    </div>

    {{-- Tab Content --}}
    @if($activeTab === 'in_progress')
        @if($inProgressEnrollments->isEmpty())
            <div class="text-center py-16">
                <span class="material-symbols-outlined text-[64px] text-ink3 mb-4 block">school</span>
                <h3 class="font-display font-bold text-lg text-ink mb-2">No courses in progress</h3>
                <p class="text-sm text-ink3 mb-6">Start learning by enrolling in a course</p>
                <a href="{{ route('courses.index') }}" class="inline-block px-6 py-3 bg-ink text-white font-display font-bold text-sm rounded-lg hover:opacity-90 transition-opacity">
                    Browse Courses
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($inProgressEnrollments as $enrollment)
                    <div wire:key="in-progress-enrollment-{{ $enrollment->id }}" class="bg-surface border border-rule rounded-xl overflow-hidden hover:shadow-lg transition-shadow">
                        {{-- Thumbnail --}}
                        <div class="aspect-video bg-bg relative">
                            @if($enrollment->course->getFirstMediaUrl('thumbnail'))
                                <img src="{{ $enrollment->course->getFirstMediaUrl('thumbnail', 'thumb') }}" 
                                    alt="{{ $enrollment->course->title }}" 
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-primary/10">
                                    <span class="material-symbols-outlined text-[48px] text-primary/30">school</span>
                                </div>
                            @endif
                        </div>

                        <div class="p-5">
                            <h3 class="font-display font-bold text-base text-ink leading-tight mb-2 line-clamp-2">
                                {{ $enrollment->course->title }}
                            </h3>
                            <p class="text-xs text-ink3 mb-4">by {{ $enrollment->course->instructor?->name }}</p>

                            {{-- Progress Bar --}}
                            <div class="mb-4">
                                <div class="flex items-center justify-between text-xs mb-2">
                                    <span class="text-ink3">Progress</span>
                                    <span class="font-medium text-ink">{{ number_format($enrollment->progress_percentage, 0) }}%</span>
                                </div>
                                <div class="h-2 bg-bg rounded-full overflow-hidden">
                                    <div class="h-full bg-primary rounded-full transition-all" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                </div>
                            </div>

                            {{-- Continue Button --}}
                            <a href="{{ route('learn.show', $enrollment->course->slug) }}{{ isset($nextLessonIdByEnrollmentId[$enrollment->id]) ? '?lesson=' . $nextLessonIdByEnrollmentId[$enrollment->id] : '' }}" 
                                class="block w-full py-3 bg-ink text-white font-display font-bold text-sm text-center rounded-lg hover:opacity-90 transition-opacity">
                                Continue Learning
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    @elseif($activeTab === 'completed')
        @if($completedEnrollments->isEmpty())
            <div class="text-center py-16">
                <span class="material-symbols-outlined text-[64px] text-ink3 mb-4 block">verified</span>
                <h3 class="font-display font-bold text-lg text-ink mb-2">No completed courses yet</h3>
                <p class="text-sm text-ink3 mb-6">Keep learning to complete your first course!</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($completedEnrollments as $enrollment)
                    <div wire:key="completed-enrollment-{{ $enrollment->id }}" class="bg-surface border border-rule rounded-xl overflow-hidden hover:shadow-lg transition-shadow">
                        {{-- Thumbnail with Completion Badge --}}
                        <div class="aspect-video bg-bg relative">
                            @if($enrollment->course->getFirstMediaUrl('thumbnail'))
                                <img src="{{ $enrollment->course->getFirstMediaUrl('thumbnail', 'thumb') }}" 
                                    alt="{{ $enrollment->course->title }}" 
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-primary/10">
                                    <span class="material-symbols-outlined text-[48px] text-primary/30">school</span>
                                </div>
                            @endif
                            <div class="absolute top-3 right-3 px-3 py-1 bg-green-500 text-white text-[10px] font-bold uppercase rounded-full flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">verified</span>
                                Completed
                            </div>
                        </div>

                        <div class="p-5">
                            <h3 class="font-display font-bold text-base text-ink leading-tight mb-2 line-clamp-2">
                                {{ $enrollment->course->title }}
                            </h3>
                            <p class="text-xs text-ink3 mb-4">by {{ $enrollment->course->instructor?->name }}</p>

                            <p class="text-xs text-ink3 mb-4">
                                Completed on {{ $enrollment->completed_at->format('M j, Y') }}
                            </p>

                            <div class="flex gap-2">
                                <a href="{{ route('learn.show', $enrollment->course->slug) }}" 
                                    class="flex-1 py-2.5 border border-rule text-ink2 font-medium text-sm text-center rounded-lg hover:bg-bg transition-colors">
                                    Review
                                </a>
                                <a href="{{ route('student.certificates') }}" 
                                    class="flex-1 py-2.5 bg-ink text-white font-display font-bold text-sm text-center rounded-lg hover:opacity-90 transition-opacity">
                                    Certificate
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    @elseif($activeTab === 'wishlist')
        @if($wishlistCourses->isEmpty())
            <div class="text-center py-16">
                <span class="material-symbols-outlined text-[64px] text-ink3 mb-4 block">bookmark</span>
                <h3 class="font-display font-bold text-lg text-ink mb-2">Your wishlist is empty</h3>
                <p class="text-sm text-ink3 mb-6">Save courses you're interested in for later</p>
                <a href="{{ route('courses.index') }}" class="inline-block px-6 py-3 bg-ink text-white font-display font-bold text-sm rounded-lg hover:opacity-90 transition-opacity">
                    Browse Courses
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($wishlistCourses as $course)
                    <div wire:key="wishlist-course-{{ $course->id }}" class="bg-surface border border-rule rounded-xl overflow-hidden hover:shadow-lg transition-shadow">
                        {{-- Thumbnail --}}
                        <div class="aspect-video bg-bg relative">
                            @if($course->getFirstMediaUrl('thumbnail'))
                                <img src="{{ $course->getFirstMediaUrl('thumbnail', 'thumb') }}" 
                                    alt="{{ $course->title }}" 
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-primary/10">
                                    <span class="material-symbols-outlined text-[48px] text-primary/30">school</span>
                                </div>
                            @endif
                            <button wire:click="removeFromWishlist({{ $course->id }})" 
                                class="absolute top-3 right-3 p-2 bg-white/90 hover:bg-white rounded-full shadow transition-colors"
                                title="Remove from wishlist">
                                <span class="material-symbols-outlined text-[18px] text-red-500" style="font-variation-settings: 'FILL' 1">bookmark</span>
                            </button>
                        </div>

                        <div class="p-5">
                            <h3 class="font-display font-bold text-base text-ink leading-tight mb-2 line-clamp-2">
                                {{ $course->title }}
                            </h3>
                            <p class="text-xs text-ink3 mb-3">by {{ $course->instructor?->name }}</p>

                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs text-ink3">{{ number_format($course->enrollments_count) }} students</span>
                                <span class="font-display font-bold text-lg {{ $course->price > 0 ? 'text-ink' : 'text-green-600' }}">
                                    {{ $course->price > 0 ? '$' . number_format($course->price, 2) : 'Free' }}
                                </span>
                            </div>

                            <a href="{{ route('courses.show', $course->slug) }}" 
                                class="block w-full py-3 bg-ink text-white font-display font-bold text-sm text-center rounded-lg hover:opacity-90 transition-opacity">
                                View Course
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
