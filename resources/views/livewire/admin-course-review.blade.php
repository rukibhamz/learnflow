<div>
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex gap-4 mb-8">
        <div class="relative flex-1 max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-ink3">search</span>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search courses or instructors..." 
                class="w-full h-11 bg-surface border border-rule rounded-lg pl-10 pr-4 font-body text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all">
        </div>
    </div>

    @if($courses->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($courses as $course)
        <div class="bg-surface border border-rule rounded-lg overflow-hidden">
            <div class="h-24 bg-primary/10 flex items-center justify-center">
                @if($course->thumbnail)
                    <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover">
                @else
                    <span class="font-poppins font-extrabold text-primary text-2xl">{{ strtoupper(substr($course->title, 0, 2)) }}</span>
                @endif
            </div>
            <div class="p-5">
                <h3 class="font-poppins font-bold text-ink text-sm leading-tight mb-2">{{ $course->title }}</h3>
                <p class="text-[12px] text-ink3 mb-1">
                    <span class="font-medium">{{ $course->instructor->name ?? 'Unknown' }}</span>
                </p>
                <p class="text-[11px] text-ink3 mb-3">
                    {{ $course->sections_count }} sections • {{ $course->lessons_count }} lessons • {{ format_price($course->price) }}
                </p>
                <p class="text-[11px] text-ink3">
                    Submitted {{ $course->updated_at->diffForHumans() }}
                </p>
            </div>
            <div class="p-4 flex gap-3 border-t border-rule">
                <button wire:click="approveCourse({{ $course->id }})" wire:confirm="Approve and publish this course?" 
                    class="flex-1 py-2.5 bg-green-500 text-white font-poppins font-bold text-[12px] rounded-lg hover:bg-green-600 transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                    Approve
                </button>
                <button wire:click="rejectCourse({{ $course->id }})" wire:confirm="Reject this course and return it to draft?" 
                    class="flex-1 py-2.5 border border-rule text-ink2 font-poppins font-bold text-[12px] rounded-lg hover:border-red-300 hover:text-red-500 transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                    Reject
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $courses->links() }}
    </div>
    @else
    <div class="text-center py-16 bg-surface border border-rule rounded-lg">
        <span class="material-symbols-outlined text-[48px] text-ink3 mb-4">inbox</span>
        <p class="text-ink3 font-body">No courses pending review</p>
    </div>
    @endif
</div>
