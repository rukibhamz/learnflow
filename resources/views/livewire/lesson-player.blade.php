@extends('layouts.learn')

@section('title', 'Lesson Player')

@section('content')
<div class="flex flex-col h-[calc(100vh-52px)]">
    {{-- Video Stage --}}
    <div class="flex-1 bg-black flex flex-col items-center justify-center p-8 relative group">
        {{-- Video Placeholder --}}
        <div class="w-full max-w-4xl aspect-video bg-[#111111] border border-[#222222] rounded-card flex items-center justify-center">
            <button class="w-20 h-20 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-md hover:bg-white/20 transition-all">
                <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/></svg>
            </button>
        </div>

        {{-- Overlay info --}}
        <div class="absolute bottom-12 left-12 opacity-0 group-hover:opacity-100 transition-opacity">
            <span class="text-[11px] font-bold uppercase tracking-widest text-[#888888] mb-2 block">Next Up</span>
            <h3 class="font-display font-bold text-lg text-white">Scaling Database Architectures</h3>
        </div>
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
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                <div class="w-16 h-[2px] bg-[#222222] rounded-full overflow-hidden self-center">
                    <div class="h-full bg-white" style="width: 80%"></div>
                </div>
            </div>
            <button class="text-[11px] font-bold text-[#888888] hover:text-white">1.0x</button>
            <button class="text-[#888888] hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg></button>
            <button class="text-[#888888] hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg></button>
        </div>
    </div>
</div>
@endsection

@section('sidebar')
<div class="p-6">
    <h3 class="font-display font-bold text-sm text-ink mb-6">Course Content</h3>
    
    <div class="space-y-4">
        @foreach($sections as $section)
        <div>
            <div class="flex items-center justify-between mb-3 text-[11px] font-bold uppercase tracking-widest text-ink3">
                <span>{{ $section->title }}</span>
            </div>
            <div class="space-y-1">
                @foreach($section->lessons as $lesson)
                @php $active = ($currentLesson && $currentLesson->id == $lesson->id); @endphp
                <button wire:click="selectLesson({{ $lesson->id }})" 
                        class="w-full text-left px-3 py-2.5 rounded-card flex items-start gap-3 transition-colors {{ $active ? 'bg-accent-bg text-accent' : 'hover:bg-bg text-ink2 hover:text-ink' }}">
                    <div class="w-4 h-4 mt-0.5 border {{ $active ? 'bg-accent border-accent text-white' : 'border-rule' }} flex items-center justify-center">
                        @if($active)
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="text-[12px] font-medium leading-tight">{{ $lesson->order }}. {{ $lesson->title }}</p>
                        @if($lesson->duration_seconds)
                        <span class="text-[10px] text-ink3 mt-1 block">{{ floor($lesson->duration_seconds / 60) }}:{{ str_pad($lesson->duration_seconds % 60, 2, '0', STR_PAD_LEFT) }}</span>
                        @endif
                    </div>
                </button>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
