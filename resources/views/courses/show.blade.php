@extends('layouts.app')

@section('title', 'Course Title')

@section('content')
    {{-- Dark Hero Section --}}
    <section class="bg-ink py-20 px-6">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-12 text-white items-center">
            <div class="flex-1">
                <div class="flex items-center gap-3 text-[13px] text-[#8899FF] font-medium mb-6">
                    <span>Design</span>
                    <span class="text-[#444444]">/</span>
                    <span>User Interface</span>
                </div>

                <h1 class="font-display font-extrabold text-[28px] md:text-[36px] leading-tight tracking-tight mb-6">
                    Advanced Interface Design Systems for Modern Web Applications
                </h1>

                <p class="max-w-[480px] text-[14px] text-[#AAAAAA] font-body leading-relaxed mb-10">
                    Master the art of building scalable, maintainable design systems that empower your team to build faster and with higher quality.
                </p>

                <div class="flex flex-wrap items-center gap-x-8 gap-y-4 text-[12px] font-medium">
                    <span class="flex items-center gap-2">
                        <span class="text-[#F0A500]">★ 4.8</span>
                        <span class="text-[#666666]">(2.4k students)</span>
                    </span>
                    <span class="text-[#666666]">Instructor: <span class="text-white">Sarah Connor</span></span>
                    <span class="text-[#666666]">32 lessons (12 hours)</span>
                    <x-status-badge status="Advanced" class="!bg-accent-bg !text-accent border-0" />
                </div>
            </div>
        </div>
    </section>

    {{-- Content Area --}}
    <section class="py-16 px-6 max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_260px] gap-16">
            {{-- Main Content --}}
            <div>
                {{-- Tabs --}}
                <div class="flex gap-8 border-b border-rule mb-12" x-data="{ tab: 'curriculum' }">
                    <button 
                        @click="tab = 'curriculum'"
                        :class="tab === 'curriculum' ? 'border-accent text-ink' : 'border-transparent text-ink3 hover:text-ink2'"
                        class="pb-4 font-display font-bold text-sm border-b-2 transition-colors"
                    >Curriculum</button>
                    <button 
                        @click="tab = 'overview'"
                        :class="tab === 'overview' ? 'border-accent text-ink' : 'border-transparent text-ink3 hover:text-ink2'"
                        class="pb-4 font-display font-bold text-sm border-b-2 transition-colors"
                    >Overview</button>
                    <button 
                        @click="tab = 'reviews'"
                        :class="tab === 'reviews' ? 'border-accent text-ink' : 'border-transparent text-ink3 hover:text-ink2'"
                        class="pb-4 font-display font-bold text-sm border-b-2 transition-colors"
                    >Reviews</button>
                </div>

                {{-- Curriculum Accordion --}}
                <div class="space-y-6">
                    @foreach(['Introduction', 'Foundation & Tokens', 'Component Library'] as $section)
                    <div class="border border-rule rounded-card overflow-hidden">
                        <div class="bg-bg px-6 py-4 flex justify-between items-center cursor-pointer">
                            <h3 class="font-display font-bold text-sm text-ink">{{ $section }}</h3>
                            <span class="text-[11px] font-bold text-ink2 uppercase tracking-widest">4 lessons</span>
                        </div>
                        <div class="px-0">
                            @foreach(range(1, 4) as $l)
                            <div class="px-6 py-3.5 border-t border-rule flex items-center justify-between group hover:bg-bg transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-card {{ $l == 1 ? 'bg-accent-bg text-accent' : 'bg-bg text-ink3' }} flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path></svg>
                                    </div>
                                    <span class="text-[13px] font-body text-ink2 group-hover:text-ink transition-colors">Lesson {{ $l }}: System Architecture Overview</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    @if($l == 1)
                                        <span class="px-2 py-0.5 bg-success-bg text-success text-[10px] font-bold uppercase tracking-widest rounded-pill">Free Preview</span>
                                    @endif
                                    <span class="text-[11px] font-medium text-ink3">12:40</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Sticky Sidebar --}}
            <aside class="relative">
                <div class="lg:sticky lg:top-24 space-y-8 p-8 bg-surface border border-rule rounded-card">
                    <div class="space-y-2">
                        <div class="flex items-baseline gap-3">
                            <span class="font-display font-extrabold text-3xl text-ink">$29</span>
                            <span class="text-ink3 text-sm line-through">$89</span>
                            <span class="px-2 py-0.5 bg-accent-bg text-accent text-[10px] font-bold uppercase tracking-widest rounded-pill">60% OFF</span>
                        </div>
                        <p class="text-[11px] font-bold text-warn flex items-center gap-2">
                            <span>⏱</span> 2 days left at this price!
                        </p>
                    </div>

                    <div class="space-y-3">
                        <button class="w-full py-3.5 bg-ink text-white font-display font-bold text-sm rounded-card hover:opacity-90 transition-opacity">Enroll Now</button>
                        <button class="w-full py-3.5 border border-rule text-ink font-display font-bold text-sm rounded-card hover:border-ink transition-colors">Add to wishlist</button>
                    </div>

                    <div class="pt-8 border-t border-rule">
                        <h4 class="font-display font-bold text-[11px] uppercase tracking-widest text-ink mb-4">Course includes:</h4>
                        <ul class="space-y-3">
                            @foreach(['Full lifetime access', '32 on-demand video lessons', '12 downloadable resources', 'Certificate of completion'] as $feat)
                            <li class="flex items-center gap-3 text-[13px] text-ink2 font-body">
                                <span class="text-accent text-lg leading-none">✦</span>
                                {{ $feat }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection
