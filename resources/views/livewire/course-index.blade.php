<div class="min-h-screen bg-bg">
    <div class="max-w-7xl mx-auto px-6 py-10">
        <div class="flex gap-10">

            {{-- Sidebar --}}
            <aside class="w-56 shrink-0">
                <div class="flex items-center gap-2 mb-6">
                    <span class="material-symbols-outlined text-[16px] text-ink3">filter_list</span>
                    <span class="text-[11px] font-bold uppercase tracking-widest text-ink3">Filters</span>
                    @if($search || !empty($levels) || $priceFilter || $categoryFilter)
                        <button wire:click="clearFilters" class="ml-auto text-[11px] text-primary hover:underline">Clear</button>
                    @endif
                </div>

                {{-- Level --}}
                <div class="mb-7">
                    <h4 class="text-[12px] font-bold text-ink mb-3">Level</h4>
                    <div class="space-y-2.5">
                        @foreach($levelOptions as $level)
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox"
                                       wire:click="toggleLevel('{{ $level->value }}')"
                                       @checked(in_array($level->value, $levels))
                                       class="w-4 h-4 rounded border-rule text-primary focus:ring-primary/20 accent-primary">
                                <span class="text-[13px] text-ink2">{{ ucfirst($level->value) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Price --}}
                <div class="mb-7">
                    <h4 class="text-[12px] font-bold text-ink mb-3">Price</h4>
                    <div class="space-y-2.5">
                        @foreach(['free' => 'Free', 'paid' => 'Paid'] as $val => $label)
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox"
                                       wire:click="$set('priceFilter', '{{ $priceFilter === $val ? '' : $val }}')"
                                       @checked($priceFilter === $val)
                                       class="w-4 h-4 rounded border-rule text-primary focus:ring-primary/20 accent-primary">
                                <span class="text-[13px] text-ink2">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Category --}}
                @if($categories->isNotEmpty())
                <div class="mb-7">
                    <h4 class="text-[12px] font-bold text-ink mb-3">Category</h4>
                    <div class="space-y-2.5">
                        @foreach($categories as $cat)
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox"
                                       wire:click="$set('categoryFilter', '{{ $categoryFilter === $cat ? '' : $cat }}')"
                                       @checked($categoryFilter === $cat)
                                       class="w-4 h-4 rounded border-rule text-primary focus:ring-primary/20 accent-primary">
                                <span class="text-[13px] text-ink2">{{ $cat }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif
            </aside>

            {{-- Main --}}
            <div class="flex-1 min-w-0">

                {{-- Search + Sort bar --}}
                <div class="flex items-center gap-4 mb-8">
                    <div class="flex-1 relative" x-data="{ open: @entangle('showSuggestions') }" @click.outside="open = false">
                        <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-ink3">search</span>
                        <input type="search"
                               wire:model.live.debounce.300ms="search"
                               @focus="if ($wire.suggestions.length) open = true"
                               @keydown.escape="open = false"
                               placeholder="Search for courses, skills, or teachers..."
                               class="w-full h-11 bg-surface border border-rule rounded-xl pl-10 pr-4 text-[13px] text-ink focus:outline-none focus:ring-1 focus:ring-primary/30">

                        @if(count($suggestions) > 0)
                            <div x-show="open" x-transition.opacity class="absolute top-full left-0 right-0 mt-1 bg-surface rounded-xl shadow-xl border border-rule overflow-hidden z-50">
                                @foreach($suggestions as $suggestion)
                                    <button wire:click="selectSuggestion('{{ addslashes($suggestion) }}')"
                                            class="w-full text-left px-4 py-2.5 text-[13px] text-ink hover:bg-bg flex items-center gap-3 transition-colors">
                                        <span class="material-symbols-outlined text-[16px] text-ink3">search</span>
                                        {{ $suggestion }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <p class="text-[13px] text-ink2 whitespace-nowrap shrink-0">
                        Showing <span class="font-bold text-ink">{{ number_format($courses->total()) }} {{ Str::plural('course', $courses->total()) }}</span>
                    </p>

                    <select wire:model.live="sort" class="h-11 px-4 bg-surface border border-rule rounded-xl text-[13px] text-ink focus:outline-none focus:ring-1 focus:ring-primary/30 cursor-pointer shrink-0">
                        <option value="popular">Most Popular</option>
                        <option value="newest">Newest</option>
                        <option value="rated">Highest Rated</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                    </select>
                </div>

                {{-- Course Grid --}}
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                    @forelse($courses as $course)
                        @php $hl = $highlights[$course->id] ?? null; @endphp
                        <a href="{{ route('courses.show', $course->slug) }}"
                           class="group flex bg-surface border border-rule rounded-xl overflow-hidden hover:shadow-md hover:border-primary/20 transition-all">

                            {{-- Thumbnail --}}
                            <div class="w-40 shrink-0 bg-bg relative overflow-hidden">
                                @if($course->getFirstMediaUrl('thumbnail'))
                                    <img src="{{ $course->getFirstMediaUrl('thumbnail', 'thumb') }}"
                                         alt="{{ $course->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/10 to-primary/5">
                                        <span class="material-symbols-outlined text-[40px] text-primary/30">school</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 p-5 min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    @if($course->category)
                                        <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] font-bold uppercase rounded tracking-wide">{{ $course->category }}</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] font-bold uppercase rounded tracking-wide">{{ ucfirst($course->level?->value ?? 'course') }}</span>
                                    @endif
                                    <span class="text-[13px] font-bold text-ink shrink-0">
                                        {{ $course->price > 0 ? '$' . number_format($course->price, 2) : 'Free' }}
                                    </span>
                                </div>

                                <h3 class="font-display font-bold text-[15px] text-ink leading-snug mb-1.5 line-clamp-2 group-hover:text-primary transition-colors">
                                    @if($hl && isset($hl['title']))
                                        {!! $hl['title'] !!}
                                    @else
                                        {{ $course->title }}
                                    @endif
                                </h3>

                                @if($course->short_description)
                                    <p class="text-[12px] text-ink3 line-clamp-2 mb-3">
                                        @if($hl && isset($hl['short_description']) && trim($search) !== '')
                                            {!! $hl['short_description'] !!}
                                        @else
                                            {{ $course->short_description }}
                                        @endif
                                    </p>
                                @endif

                                <div class="flex items-center justify-between mt-auto">
                                    <div class="flex items-center gap-2">
                                        @if($course->instructor?->getFirstMediaUrl('avatar'))
                                            <img src="{{ $course->instructor->getFirstMediaUrl('avatar') }}" class="w-6 h-6 rounded-full object-cover" alt="">
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center">
                                                <span class="text-[10px] font-bold text-primary">{{ substr($course->instructor?->name ?? '?', 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <span class="text-[12px] text-ink2">
                                            @if($hl && isset($hl['instructor_name']))
                                                {!! $hl['instructor_name'] !!}
                                            @else
                                                {{ $course->instructor?->name ?? 'Unknown' }}
                                            @endif
                                        </span>
                                    </div>
                                    @if($course->total_duration_seconds)
                                        @php $mins = (int) round($course->total_duration_seconds / 60); @endphp
                                        <div class="flex items-center gap-1 text-[12px] text-ink3">
                                            <span class="material-symbols-outlined text-[14px]">schedule</span>
                                            {{ floor($mins / 60) }}h {{ $mins % 60 }}m
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <span class="material-symbols-outlined text-[64px] text-ink3 mb-4 block">search_off</span>
                            <h3 class="font-display font-bold text-lg text-ink mb-2">No courses found</h3>
                            <p class="text-[13px] text-ink3 mb-6">Try adjusting your filters or search terms</p>
                            <button wire:click="clearFilters" class="px-6 py-2.5 bg-ink text-white font-bold text-[13px] rounded-lg hover:opacity-90 transition-opacity">
                                Clear Filters
                            </button>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($courses->hasPages())
                    <div class="mt-10 flex items-center justify-center gap-1">
                        {{-- Prev --}}
                        @if($courses->onFirstPage())
                            <span class="flex items-center gap-1 px-4 py-2 text-[13px] text-ink3 rounded-lg cursor-not-allowed">
                                <span class="material-symbols-outlined text-[16px]">chevron_left</span> Prev
                            </span>
                        @else
                            <button wire:click="previousPage" class="flex items-center gap-1 px-4 py-2 text-[13px] text-ink2 rounded-lg hover:bg-surface border border-transparent hover:border-rule transition-all">
                                <span class="material-symbols-outlined text-[16px]">chevron_left</span> Prev
                            </button>
                        @endif

                        {{-- Page numbers --}}
                        @foreach($courses->getUrlRange(1, $courses->lastPage()) as $page => $url)
                            <button wire:click="gotoPage({{ $page }})"
                                    class="w-9 h-9 text-[13px] font-bold rounded-lg transition-all {{ $page == $courses->currentPage() ? 'bg-primary text-white' : 'text-ink2 hover:bg-surface border border-transparent hover:border-rule' }}">
                                {{ $page }}
                            </button>
                        @endforeach

                        {{-- Next --}}
                        @if($courses->hasMorePages())
                            <button wire:click="nextPage" class="flex items-center gap-1 px-4 py-2 text-[13px] text-ink2 rounded-lg hover:bg-surface border border-transparent hover:border-rule transition-all">
                                Next <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </button>
                        @else
                            <span class="flex items-center gap-1 px-4 py-2 text-[13px] text-ink3 rounded-lg cursor-not-allowed">
                                Next <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
