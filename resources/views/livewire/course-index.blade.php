@extends('layouts.app')

@section('title', 'Browse Courses')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-[180px_1fr] gap-12">
        
        {{-- Filter Sidebar --}}
        <aside class="space-y-10 border-r border-rule pr-8">
            <div>
                <h3 class="font-display font-bold text-[11px] uppercase tracking-[0.08em] text-ink3 mb-4">Level</h3>
                <div class="space-y-2">
                    @foreach(['All', 'Beginner', 'Intermediate', 'Advanced'] as $level)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="level" class="hidden peer">
                            <div class="w-3.5 h-3.5 border border-rule transition-colors peer-checked:bg-ink peer-checked:border-ink flex items-center justify-center text-white">
                                <svg class="w-2.5 h-2.5 opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-[13px] font-body text-ink2 group-hover:text-ink transition-colors">{{ $level }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="font-display font-bold text-[11px] uppercase tracking-[0.08em] text-ink3 mb-4">Price</h3>
                <div class="space-y-2">
                    @foreach(['All', 'Free', 'Paid'] as $price)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" class="hidden peer">
                            <div class="w-3.5 h-3.5 border border-rule transition-colors peer-checked:bg-ink peer-checked:border-ink flex items-center justify-center text-white">
                                <svg class="w-2.5 h-2.5 opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-[13px] font-body text-ink2 group-hover:text-ink transition-colors">{{ $price }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button class="text-[13px] font-bold text-accent hover:underline">Clear filters</button>
        </aside>

        {{-- Main Area --}}
        <div class="space-y-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="relative w-full max-w-sm">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-ink3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" placeholder="Search courses..." class="w-full h-9 pl-10 pr-4 bg-surface border border-rule rounded-card font-body text-[13px] text-ink placeholder:text-ink3 focus:outline-none focus:border-accent">
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-[12px] font-medium text-ink3">Showing 48 courses</span>
                    <div class="relative">
                        <select class="h-9 pl-4 pr-10 bg-surface border border-rule rounded-card font-body text-[13px] text-ink2 appearance-none focus:outline-none focus:border-accent">
                            <option>Newest first</option>
                            <option>Price: Low to High</option>
                            <option>Highest Rated</option>
                        </select>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-ink3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach(range(1, 12) as $i)
                    <x-course-card-wide :course="(object)[
                        'id' => $i,
                        'title' => 'Web Design Advanced Systems ' . $i,
                        'instructor' => (object)['name' => 'Alex Rivera'],
                        'rating' => 4.8,
                        'lessons_count' => 32,
                        'duration_hours' => 12,
                        'price' => $i % 3 == 0 ? 0 : 29,
                        'url' => '#'
                    ]" />
                @endforeach
            </div>

            {{-- Pagination --}}
            <nav class="flex items-center justify-center gap-1 py-12">
                <a href="#" class="px-3 py-1.5 border border-rule rounded-card text-ink2 hover:border-ink transition-colors">Prev</a>
                <a href="#" class="px-3 py-1.5 border border-accent bg-accent-bg text-accent rounded-card font-medium">1</a>
                <a href="#" class="px-3 py-1.5 border border-rule rounded-card text-ink2 hover:border-ink transition-colors">2</a>
                <a href="#" class="px-3 py-1.5 border border-rule rounded-card text-ink2 hover:border-ink transition-colors">3</a>
                <a href="#" class="px-3 py-1.5 border border-rule rounded-card text-ink2 hover:border-ink transition-colors">Next</a>
            </nav>
        </div>
    </div>
</div>
@endsection
