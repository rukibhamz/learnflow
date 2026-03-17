<div class="flex flex-col h-[calc(100vh-52px)]">
    @if (session('success'))
        <div class="px-6 py-3 bg-green-50 border-b border-green-200 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Video Stage --}}
    <div class="flex-1 bg-black flex flex-col items-center justify-center p-8 relative group">
        @if($currentLesson?->video_url)
            <div class="w-full max-w-4xl aspect-video">
                <iframe src="{{ $currentLesson->video_url }}" class="w-full h-full rounded-card" allowfullscreen></iframe>
            </div>
        @elseif($currentLesson?->type === 'text' || $currentLesson?->body)
            <div class="w-full max-w-4xl bg-white rounded-card p-8 overflow-y-auto max-h-full prose prose-sm">
                {!! $currentLesson->body !!}
            </div>
        @else
            <div class="w-full max-w-4xl aspect-video bg-[#111111] border border-[#222222] rounded-card flex items-center justify-center">
                <button class="w-20 h-20 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-md hover:bg-white/20 transition-all">
                    <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/></svg>
                </button>
            </div>
        @endif

        @if($currentLesson)
        <div class="absolute bottom-12 left-12 opacity-0 group-hover:opacity-100 transition-opacity">
            <span class="text-[11px] font-bold uppercase tracking-widest text-[#888888] mb-2 block">Now Playing</span>
            <h3 class="font-display font-bold text-lg text-white">{{ $currentLesson->title }}</h3>
        </div>
        @endif
    </div>

    {{-- Control Bar --}}
    <div class="h-14 bg-ink border-t border-[#222222] flex items-center px-6 gap-6">
        <div class="flex items-center gap-4">
            <button class="text-[#888888] hover:text-white transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M8.445 14.832A1 1 0 0010 14v-2.798l5.445 3.63A1 1 0 0017 15V5a1 1 0 00-1.555-.832L10 7.798V5a1 1 0 00-1.555-.832l-6 4a1 1 0 000 1.664l6 4z"/></svg></button>
            <button class="w-10 h-10 rounded-full bg-white text-ink flex items-center justify-center hover:opacity-90 transition-opacity"><svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/></svg></button>
            <button class="text-[#888888] hover:text-white transition-colors"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M4.555 5.168A1 1 0 003 6v8a1 1 0 001.555.832L10 11.202V14a1 1 0 001.555.832l6-4a1 1 0 000-1.664l-6-4A1 1 0 0010 6v2.798l-5.445-3.63z"/></svg></button>
        </div>

        <div class="flex-1 flex items-center gap-4">
            <span class="text-[11px] font-bold text-[#666666] tabular-nums">{{ $currentTime }}</span>
            <div class="flex-1 h-[2px] bg-[#222222] rounded-full overflow-hidden self-center">
                <div class="h-full bg-accent" style="width: 37%"></div>
            </div>
            <span class="text-[11px] font-bold text-[#666666] tabular-nums">{{ $duration }}</span>
        </div>

        <div class="flex items-center gap-6">
            @if($currentLesson)
                @if(in_array($currentLesson->id, $completedLessonIds))
                    <span class="px-3 py-1.5 rounded bg-green-800/30 text-green-400 text-[11px] font-bold uppercase tracking-widest flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Completed
                    </span>
                @else
                    <button wire:click="markComplete" wire:loading.attr="disabled" class="px-3 py-1.5 rounded bg-green-600 text-white text-[11px] font-bold uppercase tracking-widest hover:bg-green-700 transition-colors disabled:opacity-50">
                        <span wire:loading.remove wire:target="markComplete">Mark Complete</span>
                        <span wire:loading wire:target="markComplete">Saving...</span>
                    </button>
                @endif
            @endif
            <button class="text-[11px] font-bold text-[#888888] hover:text-white">1.0x</button>
            <button class="text-[#888888] hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg></button>
        </div>
    </div>

    {{-- Quiz Banner --}}
    @if($currentLesson?->quiz)
        <div class="bg-primary/10 border-t border-primary/20 px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">quiz</span>
                <span class="text-sm font-medium text-ink">This lesson has a quiz: <strong>{{ $currentLesson->quiz->title }}</strong></span>
            </div>
            <a href="{{ route('learn.quiz', ['course' => $course->slug, 'quiz' => $currentLesson->quiz->id]) }}"
               class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-card hover:opacity-90 transition-opacity">
                Take Quiz
            </a>
        </div>
    @endif

    {{-- Sidebar (inline for Livewire single-root) --}}
    <div class="hidden">
        {{-- Sidebar content is rendered via learn/show.blade.php @section('sidebar') --}}
    </div>
</div>
