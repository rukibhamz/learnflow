<div class="flex flex-1 w-full">
    <style>
        .custom-checkbox:checked {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
        }
    </style>

    <!-- Sidebar Filters -->
    <aside class="hidden lg:flex flex-col w-[220px] shrink-0 bg-white border-r border-slate-200 p-6 gap-8">
        <div>
            <h3 class="text-slate-900 text-sm font-bold uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">filter_list</span>
                Filters
            </h3>

            <!-- Level Section -->
            <div class="mb-8">
                <p class="text-slate-900 text-sm font-semibold mb-3">Level</p>
                <div class="flex flex-col gap-2">
                    @foreach($levelOptions as $level)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox"
                                   wire:click="toggleLevel('{{ $level->value }}')"
                                   @checked(in_array($level->value, $levels))
                                   class="custom-checkbox h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary focus:ring-offset-0 bg-transparent transition-all">
                            <span class="text-sm text-slate-600 group-hover:text-primary">{{ ucfirst($level->value) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Price Section -->
            <div class="mb-8">
                <p class="text-slate-900 text-sm font-semibold mb-3">Price</p>
                <div class="flex flex-col gap-2">
                    @foreach(['free' => 'Free', 'paid' => 'Paid'] as $val => $label)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox"
                                   wire:click="$set('priceFilter', '{{ $priceFilter === $val ? '' : $val }}')"
                                   @checked($priceFilter === $val)
                                   class="custom-checkbox h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary focus:ring-offset-0 bg-transparent transition-all">
                            <span class="text-sm text-slate-600 group-hover:text-primary">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Category Section -->
            @if($categories->isNotEmpty())
                <div>
                    <p class="text-slate-900 text-sm font-semibold mb-3">Category</p>
                    <div class="flex flex-col gap-2">
                        @foreach($categories as $cat)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox"
                                       wire:click="$set('categoryFilter', '{{ $categoryFilter === $cat ? '' : $cat }}')"
                                       @checked($categoryFilter === $cat)
                                       class="custom-checkbox h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary focus:ring-offset-0 bg-transparent transition-all">
                                <span class="text-sm text-slate-600 group-hover:text-primary">{{ $cat }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </aside>

    <!-- Main Content -->
    <section class="flex-1 p-6 md:p-8 overflow-y-auto">
        <!-- Search & Sort Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8"
             x-data="{ open: @entangle('showSuggestions') }" @click.outside="open = false">
            <div class="flex-1 max-w-lg relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <span class="material-symbols-outlined">search</span>
                </span>
                <input type="search"
                       wire:model.live.debounce.300ms="search"
                       @focus="if ($wire.suggestions.length) open = true"
                       @keydown.escape="open = false"
                       class="block w-full rounded-lg border border-slate-200 bg-white py-3 pl-10 pr-3 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm"
                       placeholder="Search for courses, skills, or teachers...">

                @if(count($suggestions) > 0)
                    <div x-show="open" x-transition.opacity
                         class="absolute top-full left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden z-50">
                        @foreach($suggestions as $suggestion)
                            <button wire:click="selectSuggestion('{{ addslashes($suggestion) }}')"
                                    class="w-full text-left px-4 py-2.5 text-[13px] text-slate-700 hover:bg-slate-50 flex items-center gap-3">
                                <span class="material-symbols-outlined text-[15px] text-slate-400">search</span>
                                {{ $suggestion }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4 justify-between md:justify-end">
                <p class="text-slate-500 text-sm whitespace-nowrap">Showing <span class="font-bold text-slate-900">{{ number_format($courses->total()) }} {{ Str::plural('course', $courses->total()) }}</span></p>
                <div class="relative inline-block text-left">
                    <select wire:model.live="sort"
                            class="appearance-none bg-white border border-slate-200 rounded-lg py-2 pl-4 pr-10 text-sm font-medium focus:outline-none focus:ring-1 focus:ring-primary cursor-pointer text-slate-700">
                        <option value="popular">Most Popular</option>
                        <option value="newest">Newest First</option>
                        <option value="rated">Highest Rated</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                        <span class="material-symbols-outlined text-lg">expand_more</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-10">
            @forelse($courses as $course)
                @php $hl = $highlights[$course->id] ?? null; @endphp
                <a href="{{ route('courses.show', $course->slug) }}" class="flex flex-col sm:flex-row bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-xl transition-all group">
                    <div class="sm:w-48 h-48 sm:h-auto overflow-hidden">
                        @if($course->getFirstMediaUrl('thumbnail'))
                            <div class="w-full h-full bg-slate-200 bg-cover bg-center group-hover:scale-105 transition-transform"
                                 style="background-image: url('{{ $course->getFirstMediaUrl('thumbnail', 'thumb') }}');"></div>
                        @else
                            <div class="w-full h-full bg-slate-200 flex items-center justify-center group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined text-[40px] text-slate-400">school</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                @php $badge = $course->category ?: ucfirst($course->level?->value ?? ''); @endphp
                                @if($badge)
                                    <span class="text-xs font-bold uppercase tracking-widest text-primary bg-primary/10 px-2 py-1 rounded">{{ $badge }}</span>
                                @else
                                    <span></span>
                                @endif
                                <span class="text-sm font-bold text-slate-900">
                                    {{ format_price((float) $course->price) }}
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2 leading-tight">
                                @if($hl && isset($hl['title']))
                                    {!! $hl['title'] !!}
                                @else
                                    {{ $course->title }}
                                @endif
                            </h3>
                            <p class="text-slate-500 text-sm line-clamp-2 mb-4">
                                @if($hl && isset($hl['short_description']) && trim($search) !== '')
                                    {!! $hl['short_description'] !!}
                                @else
                                    {{ $course->short_description }}
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="size-8 rounded-full bg-slate-100 overflow-hidden">
                                    @if($course->instructor?->getFirstMediaUrl('avatar'))
                                        <img alt="{{ $course->instructor->name }}" class="w-full h-full object-cover" src="{{ $course->instructor->getFirstMediaUrl('avatar') }}"/>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-[10px] font-bold text-slate-500">
                                            {{ substr($course->instructor?->name ?? '?', 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <span class="text-sm font-medium text-slate-700">
                                    @if($hl && isset($hl['instructor_name']))
                                        {!! $hl['instructor_name'] !!}
                                    @else
                                        {{ $course->instructor?->name ?? 'Unknown' }}
                                    @endif
                                </span>
                            </div>
                            @if($course->total_duration_seconds)
                                @php $mins = (int) round($course->total_duration_seconds / 60); @endphp
                                <div class="flex items-center gap-1 text-slate-500 text-xs">
                                    <span class="material-symbols-outlined text-sm">schedule</span>
                                    {{ floor($mins / 60) }}h {{ str_pad($mins % 60, 2, '0', STR_PAD_LEFT) }}m
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-20 text-center">
                    <span class="material-symbols-outlined text-[56px] text-slate-300 mb-3 block">search_off</span>
                    <h3 class="text-base font-bold text-slate-800 mb-1">No courses found</h3>
                    <p class="text-[13px] text-slate-500 mb-5">Try adjusting your filters or search terms</p>
                    <button wire:click="clearFilters"
                            class="px-5 py-2 bg-primary text-white text-[13px] font-bold rounded-lg hover:opacity-90 transition-opacity">
                        Clear Filters
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($courses->hasPages())
            <div class="flex items-center justify-center gap-4 py-8 border-t border-slate-200">
                @if($courses->onFirstPage())
                    <button disabled class="flex items-center justify-center gap-2 px-6 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-400 bg-slate-50 cursor-not-allowed">
                        <span class="material-symbols-outlined text-lg">chevron_left</span> Prev
                    </button>
                @else
                    <button wire:click="previousPage" class="flex items-center justify-center gap-2 px-6 py-2 border border-slate-200 rounded-lg text-sm font-semibold hover:bg-slate-50 transition-colors text-slate-700">
                        <span class="material-symbols-outlined text-lg">chevron_left</span> Prev
                    </button>
                @endif

                <div class="flex items-center gap-2">
                    @foreach($courses->getUrlRange(1, $courses->lastPage()) as $page => $url)
                        <button wire:click="gotoPage({{ $page }})"
                                class="size-10 flex items-center justify-center rounded-lg text-sm font-bold transition-all
                                       {{ $page == $courses->currentPage() ? 'bg-primary text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                            {{ $page }}
                        </button>
                    @endforeach
                </div>

                @if($courses->hasMorePages())
                    <button wire:click="nextPage" class="flex items-center justify-center gap-2 px-6 py-2 border border-slate-200 rounded-lg text-sm font-semibold hover:bg-slate-50 transition-colors text-slate-700">
                        Next <span class="material-symbols-outlined text-lg">chevron_right</span>
                    </button>
                @else
                    <button disabled class="flex items-center justify-center gap-2 px-6 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-400 bg-slate-50 cursor-not-allowed">
                        Next <span class="material-symbols-outlined text-lg">chevron_right</span>
                    </button>
                @endif
            </div>
        @endif
    </section>
</div>
