<div class="flex flex-col h-[calc(100vh-52px)]" data-content-protected>
    @if (session('success'))
        <div class="px-6 py-3 bg-green-50 border-b border-green-200 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Content Stage --}}
    <div class="flex-1 bg-black flex flex-col items-center justify-center p-8 relative group content-protected lesson-content-area">

        {{-- Dynamic watermark overlay --}}
        @auth
        <div class="watermark-overlay text-white/80" aria-hidden="true">
            @php
                $wm = auth()->user()->email . ' · ' . auth()->id();
                $positions = [
                    ['top' => '8%', 'left' => '5%'],
                    ['top' => '8%', 'left' => '55%'],
                    ['top' => '30%', 'left' => '20%'],
                    ['top' => '30%', 'left' => '70%'],
                    ['top' => '52%', 'left' => '5%'],
                    ['top' => '52%', 'left' => '55%'],
                    ['top' => '74%', 'left' => '20%'],
                    ['top' => '74%', 'left' => '70%'],
                ];
            @endphp
            @foreach($positions as $pos)
                <span class="watermark-text" style="top: {{ $pos['top'] }}; left: {{ $pos['left'] }};">{{ $wm }}</span>
            @endforeach
        </div>
        @endauth

        @if($currentLesson?->type?->value === 'video' && $currentLesson->content_url)
            {{-- Video content --}}
            @if(str_contains($currentLesson->content_url, 'youtube.com') || str_contains($currentLesson->content_url, 'youtu.be') || str_contains($currentLesson->content_url, 'vimeo.com'))
                {{-- External embed (YouTube/Vimeo) with overlay shield --}}
                <div class="w-full max-w-4xl aspect-video relative video-shield">
                    <iframe
                        src="{{ $currentLesson->content_url }}"
                        class="w-full h-full rounded-card"
                        allowfullscreen
                        sandbox="allow-scripts allow-same-origin allow-presentation"
                        referrerpolicy="no-referrer"
                    ></iframe>
                </div>
            @else
                {{-- Self-hosted video via signed URL --}}
                <div class="w-full max-w-4xl aspect-video relative video-shield">
                    <video
                        src="{{ route('media.lesson.video', $currentLesson) }}"
                        class="w-full h-full rounded-card bg-black"
                        controls
                        controlslist="nodownload noremoteplayback"
                        disablepictureinpicture
                        oncontextmenu="return false;"
                        crossorigin="use-credentials"
                    >
                        Your browser does not support the video tag.
                    </video>
                </div>
            @endif

        @elseif($currentLesson?->type?->value === 'embed' && $currentLesson->content_url)
            {{-- Embed content --}}
            <div class="w-full max-w-4xl aspect-video relative video-shield">
                <iframe
                    src="{{ $currentLesson->content_url }}"
                    class="w-full h-full rounded-card"
                    allowfullscreen
                    sandbox="allow-scripts allow-same-origin allow-presentation"
                    referrerpolicy="no-referrer"
                ></iframe>
            </div>

        @elseif($currentLesson?->type?->value === 'text' && $currentLesson->content_body)
            {{-- Text content — protected from selection/copy --}}
            <div class="w-full max-w-4xl bg-white rounded-card p-8 overflow-y-auto max-h-full prose prose-sm content-protected relative"
                 oncontextmenu="return false;"
                 ondragstart="return false;"
                 onselectstart="return false;"
                 oncopy="return false;">
                {!! $currentLesson->content_body !!}
            </div>

        @elseif($currentLesson?->type?->value === 'pdf')
            {{-- PDF content — rendered in protected inline viewer --}}
            @php $pdfMedia = $currentLesson->getFirstMedia('pdf'); @endphp
            @if($pdfMedia)
                <div class="w-full max-w-4xl bg-white rounded-card overflow-hidden relative" style="height: 75vh;">
                    <div class="absolute top-0 left-0 right-0 bg-gray-100 border-b border-gray-200 px-4 py-2 flex items-center justify-between z-20">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
                            <span class="font-medium">{{ $currentLesson->title }}</span>
                        </div>
                        <span class="text-[10px] text-gray-400 uppercase tracking-widest font-bold flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">lock</span>
                            Protected
                        </span>
                    </div>
                    <iframe
                        src="{{ route('media.lesson.pdf', $currentLesson) }}#toolbar=0&navpanes=0&scrollbar=1"
                        class="w-full h-full pt-10"
                        style="border: none;"
                        sandbox="allow-same-origin"
                    ></iframe>
                    {{-- Transparent overlay to block right-click on PDF --}}
                    <div class="absolute inset-0 pt-10" oncontextmenu="return false;" style="z-index: 10; pointer-events: auto; background: transparent;"></div>
                </div>
            @else
                <div class="w-full max-w-4xl bg-white/5 border border-white/10 rounded-card p-8 text-center">
                    <span class="material-symbols-outlined text-[48px] text-white/30 mb-4 block">picture_as_pdf</span>
                    <p class="text-white/50 text-sm">PDF document not available.</p>
                </div>
            @endif

        @else
            {{-- No content / placeholder --}}
            <div class="w-full max-w-4xl aspect-video bg-[#111111] border border-[#222222] rounded-card flex items-center justify-center">
                <div class="text-center">
                    <span class="material-symbols-outlined text-[48px] text-white/20 mb-3 block">play_circle</span>
                    <p class="text-white/30 text-sm">Select a lesson to begin</p>
                </div>
            </div>
        @endif

        {{-- Lesson title overlay --}}
        @if($currentLesson)
        <div class="absolute bottom-12 left-12 opacity-0 group-hover:opacity-100 transition-opacity z-20">
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
            <span class="flex items-center gap-1 text-[10px] text-[#555] uppercase tracking-widest font-bold">
                <span class="material-symbols-outlined text-[14px]">shield</span>
                Protected
            </span>
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

    {{-- Sidebar placeholder (content rendered via learn/show.blade.php @section('sidebar')) --}}
    <div class="hidden"></div>
</div>
