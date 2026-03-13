@extends('layouts.dashboard')

@section('title', 'Edit Course')

@prepend('sidebar')
    @php
        $instructorNav = [
            ['label' => 'Overview', 'url' => url('/instructor/dashboard'), 'match' => 'instructor/dashboard'],
            ['label' => 'Courses', 'url' => url('/instructor/courses'), 'match' => 'instructor/courses*'],
            ['label' => 'Payments', 'url' => '#', 'match' => 'instructor/payments*'],
            ['label' => 'Settings', 'url' => '#', 'match' => 'instructor/settings*'],
        ];
    @endphp

    @foreach($instructorNav as $item)
        <a href="{{ $item['url'] }}" 
           class="flex items-center px-4 py-2.5 text-[13px] font-medium transition-all duration-150 {{ request()->is($item['match']) ? 'bg-accent-bg text-accent border-r-2 border-accent' : 'text-ink2 hover:bg-bg hover:text-ink' }}">
            {{ $item['label'] }}
        </a>
    @endforeach
@endprepend

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ section: 'basic' }">
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h1 class="font-display font-extrabold text-2xl text-ink">Course Builder</h1>
            <p class="text-[13px] font-body text-ink2 mt-1">Design your curriculum and manage publishing.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-5 py-2.5 border border-rule rounded-card font-display font-bold text-[12px] text-ink hover:border-ink transition-colors">Preview</button>
            <button class="px-5 py-2.5 bg-accent text-white font-display font-bold text-[12px] rounded-card hover:opacity-90 transition-opacity">Submit for Review</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[200px_1fr] gap-12">
        {{-- Builder Tabs --}}
        <aside class="space-y-1">
            <button @click="section = 'basic'" :class="section === 'basic' ? 'bg-bg text-ink border-l-2 border-accent' : 'text-ink3 hover:text-ink2'" class="w-full text-left px-4 py-2.5 font-display font-bold text-[11px] uppercase tracking-widest transition-all">Basic Info</button>
            <button @click="section = 'curriculum'" :class="section === 'curriculum' ? 'bg-bg text-ink border-l-2 border-accent' : 'text-ink3 hover:text-ink2'" class="w-full text-left px-4 py-2.5 font-display font-bold text-[11px] uppercase tracking-widest transition-all">Curriculum</button>
            <button @click="section = 'pricing'" :class="section === 'pricing' ? 'bg-bg text-ink border-l-2 border-accent' : 'text-ink3 hover:text-ink2'" class="w-full text-left px-4 py-2.5 font-display font-bold text-[11px] uppercase tracking-widest transition-all">Pricing</button>
            <button @click="section = 'settings'" :class="section === 'settings' ? 'bg-bg text-ink border-l-2 border-accent' : 'text-ink3 hover:text-ink2'" class="w-full text-left px-4 py-2.5 font-display font-bold text-[11px] uppercase tracking-widest transition-all">Publishing</button>
        </aside>

        {{-- Form Content --}}
        <div class="bg-surface border border-rule rounded-card p-8">
            {{-- Basic Info --}}
            <div x-show="section === 'basic'" class="space-y-8">
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Course Title</label>
                    <input type="text" placeholder="e.g. Advanced Web Architecture" class="w-full h-11 bg-bg border border-rule rounded-card px-4 font-body text-sm focus:outline-none focus:border-accent">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Subtitle</label>
                    <input type="text" placeholder="Brief elevator pitch" class="w-full h-11 bg-bg border border-rule rounded-card px-4 font-body text-sm focus:outline-none focus:border-accent">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Description</label>
                    <textarea rows="6" class="w-full bg-bg border border-rule rounded-card p-4 font-body text-sm focus:outline-none focus:border-accent"></textarea>
                </div>
            </div>

            {{-- Curriculum (Simplified placeholder) --}}
            <div x-show="section === 'curriculum'" class="space-y-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-display font-bold text-sm text-ink">Sections & Lessons</h3>
                    <button class="text-[11px] font-bold text-accent uppercase tracking-widest">+ Add Section</button>
                </div>
                
                <div class="border border-rule rounded-card p-5 bg-bg">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-ink3">Section 1: Introduction</span>
                        <div class="flex gap-4">
                            <button class="text-ink3 hover:text-ink text-[12px]">Edit</button>
                            <button class="text-ink3 hover:text-warn text-[12px]">Remove</button>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="bg-surface border border-rule p-3 rounded-card flex items-center justify-between">
                            <span class="text-[13px] font-body text-ink2">1. Welcome to the course</span>
                            <button class="text-ink3 hover:text-accent font-bold text-[10px] uppercase">Edit Lesson</button>
                        </div>
                        <button class="w-full py-2 border border-dashed border-rule rounded-card text-[11px] font-bold text-ink3 hover:border-accent hover:text-accent">+ New Lesson</button>
                    </div>
                </div>
            </div>

            {{-- Pricing --}}
            <div x-show="section === 'pricing'" class="space-y-8">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Currency</label>
                        <select class="w-full h-11 bg-bg border border-rule rounded-card px-4 font-body text-sm focus:outline-none focus:border-accent appearance-none">
                            <option>USD ($)</option>
                            <option>EUR (€)</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">List Price</label>
                        <input type="number" placeholder="49" class="w-full h-11 bg-bg border border-rule rounded-card px-4 font-body text-sm focus:outline-none focus:border-accent">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
