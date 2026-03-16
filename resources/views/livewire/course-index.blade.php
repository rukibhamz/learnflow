<div class="min-h-screen bg-bg">
    {{-- Hero Section --}}
    <div class="bg-gradient-to-br from-ink via-ink to-primary/90 text-white py-16">
        <div class="max-w-7xl mx-auto px-6">
            <h1 class="font-display font-extrabold text-4xl md:text-5xl mb-4">Explore Courses</h1>
            <p class="text-lg text-white/80 max-w-2xl">Discover thousands of courses taught by expert instructors. Start learning today.</p>
            
            {{-- Search Bar --}}
            <div class="mt-8 max-w-2xl">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-ink3">search</span>
                    <input type="search" wire:model.live.debounce.300ms="search" 
                        placeholder="Search for courses, topics, or instructors..."
                        class="w-full h-14 bg-white text-ink rounded-xl pl-12 pr-4 text-base shadow-lg focus:outline-none focus:ring-2 focus:ring-primary/50">
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-10">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Sidebar Filters --}}
            <aside class="w-full lg:w-64 shrink-0">
                <div class="bg-surface border border-rule rounded-xl p-6 sticky top-24">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-display font-bold text-sm uppercase tracking-widest text-ink">Filters</h3>
                        @if($search || !empty($levels) || $priceFilter || $language)
                            <button wire:click="clearFilters" class="text-xs text-primary hover:underline">Clear all</button>
                        @endif
                    </div>

                    {{-- Level Filter --}}
                    <div class="mb-6">
                        <h4 class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-3">Level</h4>
                        <div class="space-y-2">
                            @foreach($levelOptions as $level)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" 
                                        wire:click="toggleLevel('{{ $level->value }}')"
                                        @checked(in_array($level->value, $levels))
                                        class="w-4 h-4 rounded border-rule text-primary focus:ring-primary/20">
                                    <span class="text-sm text-ink2 group-hover:text-ink transition-colors">{{ ucfirst($level->value) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Price Filter --}}
                    <div class="mb-6">
                        <h4 class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-3">Price</h4>
                        <div class="flex gap-2">
                            <button wire:click="$set('priceFilter', '')" 
                                class="flex-1 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ $priceFilter === '' ? 'bg-ink text-white' : 'bg-bg text-ink2 hover:bg-background-light' }}">
                                All
                            </button>
                            <button wire:click="$set('priceFilter', 'free')" 
                                class="flex-1 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ $priceFilter === 'free' ? 'bg-ink text-white' : 'bg-bg text-ink2 hover:bg-background-light' }}">
                                Free
                            </button>
                            <button wire:click="$set('priceFilter', 'paid')" 
                                class="flex-1 px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ $priceFilter === 'paid' ? 'bg-ink text-white' : 'bg-bg text-ink2 hover:bg-background-light' }}">
                                Paid
                            </button>
                        </div>
                    </div>

                    {{-- Language Filter --}}
                    @if($languages->isNotEmpty())
                    <div class="mb-6">
                        <h4 class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-3">Language</h4>
                        <select wire:model.live="language" class="w-full h-10 bg-bg border border-rule rounded-lg px-3 text-sm focus:outline-none focus:border-primary">
                            <option value="">All Languages</option>
                            @foreach($languages as $lang)
                                <option value="{{ $lang }}">{{ strtoupper($lang) }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
            </aside>

            {{-- Main Content --}}
            <div class="flex-1">
                {{-- Sort & Results Count --}}
                <div class="flex items-center justify-between mb-6">
                    <p class="text-sm text-ink2">
                        <span class="font-medium text-ink">{{ $courses->total() }}</span> courses found
                    </p>
                    <select wire:model.live="sort" class="h-10 px-4 bg-surface border border-rule rounded-lg text-sm focus:outline-none focus:border-primary">
                        <option value="newest">Newest</option>
                        <option value="popular">Most Popular</option>
                        <option value="rated">Highest Rated</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                    </select>
                </div>

                {{-- Course Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse($courses as $course)
                        <a href="{{ route('courses.show', $course->slug) }}" class="group bg-surface border border-rule rounded-xl overflow-hidden hover:shadow-lg hover:border-primary/30 transition-all">
                            {{-- Thumbnail --}}
                            <div class="aspect-video bg-bg relative overflow-hidden">
                                @if($course->getFirstMediaUrl('thumbnail'))
                                    <img src="{{ $course->getFirstMediaUrl('thumbnail', 'thumb') }}" 
                                        alt="{{ $course->title }}" 
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/10 to-primary/5">
                                        <span class="material-symbols-outlined text-[64px] text-primary/30">school</span>
                                    </div>
                                @endif
                                @if($course->price == 0)
                                    <span class="absolute top-3 left-3 px-2 py-1 bg-green-500 text-white text-[10px] font-bold uppercase rounded">Free</span>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="p-5">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] font-bold uppercase rounded">{{ ucfirst($course->level?->value ?? 'all') }}</span>
                                    @if($course->language)
                                        <span class="text-[10px] text-ink3 uppercase">{{ $course->language }}</span>
                                    @endif
                                </div>

                                <h3 class="font-display font-bold text-base text-ink leading-tight mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                                    {{ $course->title }}
                                </h3>

                                <p class="text-xs text-ink3 mb-3">by {{ $course->instructor?->name ?? 'Unknown' }}</p>

                                {{-- Rating --}}
                                <div class="flex items-center gap-2 mb-3">
                                    @if($course->reviews_avg_rating)
                                        <div class="flex items-center gap-1">
                                            <span class="text-sm font-bold text-amber-500">{{ number_format($course->reviews_avg_rating, 1) }}</span>
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span class="material-symbols-outlined text-[14px] {{ $i <= round($course->reviews_avg_rating) ? 'text-amber-400' : 'text-gray-200' }}" style="font-variation-settings: 'FILL' 1">star</span>
                                                @endfor
                                            </div>
                                            <span class="text-xs text-ink3">({{ $course->reviews_count }})</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-ink3">No reviews yet</span>
                                    @endif
                                </div>

                                {{-- Footer --}}
                                <div class="flex items-center justify-between pt-3 border-t border-rule">
                                    <div class="flex items-center gap-1 text-xs text-ink3">
                                        <span class="material-symbols-outlined text-[16px]">group</span>
                                        {{ number_format($course->enrollments_count) }} students
                                    </div>
                                    <span class="font-display font-bold text-lg {{ $course->price > 0 ? 'text-ink' : 'text-green-600' }}">
                                        {{ $course->price > 0 ? '$' . number_format($course->price, 2) : 'Free' }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <span class="material-symbols-outlined text-[64px] text-ink3 mb-4 block">search_off</span>
                            <h3 class="font-display font-bold text-lg text-ink mb-2">No courses found</h3>
                            <p class="text-sm text-ink3 mb-6">Try adjusting your filters or search terms</p>
                            <button wire:click="clearFilters" class="px-6 py-3 bg-ink text-white font-display font-bold text-sm rounded-lg hover:opacity-90 transition-opacity">
                                Clear Filters
                            </button>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($courses->hasPages())
                    <div class="mt-10">
                        {{ $courses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
