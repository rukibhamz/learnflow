<div>
    @php $maintenanceOn = config('settings.maintenance_mode') && !(auth()->check() && auth()->user()?->hasRole(['admin', 'instructor'])); @endphp

    @if($maintenanceOn)
    {{-- Coming Soon placeholder shown to guests/students during maintenance --}}
    <section class="max-w-7xl mx-auto px-6 py-20" id="featured-courses">
        <div class="flex flex-col items-center justify-center text-center py-16 gap-6 border border-dashed border-rule rounded-2xl bg-surface">
            <div class="size-16 rounded-full bg-primary/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-[32px] text-primary">construction</span>
            </div>
            <div class="flex flex-col gap-3 max-w-md">
                <h2 class="text-2xl font-bold font-display text-ink">{{ \App\Models\Setting::get('maintenance_coming_soon_title', 'Coming Soon') }}</h2>
                <p class="text-ink2 text-base leading-relaxed">
                    {{ \App\Models\Setting::get('maintenance_coming_soon_message', 'Our courses are coming soon. We\'re working hard to bring you something great — check back soon.') }}
                </p>
            </div>
        </div>
    </section>
    @else
    <!-- Categories Strip -->
    <section class="py-12 bg-bg overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex gap-4 overflow-x-auto pb-4 no-scrollbar">
                <button wire:click="setCategory('all')" 
                    class="shrink-0 px-6 py-2 border {{ $category === 'all' ? 'border-ink bg-ink text-white' : 'border-rule bg-surface text-ink' }} text-sm font-medium cursor-pointer transition-colors">
                    All Categories
                </button>
                @foreach(['development', 'business', 'design', 'marketing', 'photography', 'music', 'finance', 'data-science'] as $cat)
                    <button wire:click="setCategory('{{ $cat }}')" 
                        class="shrink-0 px-6 py-2 border {{ $category === $cat ? 'border-ink bg-ink text-white' : 'border-rule bg-surface text-ink' }} text-sm font-medium hover:border-accent cursor-pointer transition-colors capitalize">
                        {{ str_replace('-', ' ', $cat) }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Courses -->
    <section class="max-w-7xl mx-auto px-6 py-20" id="featured-courses">
        <div class="flex items-end justify-between mb-12">
            <div class="flex flex-col gap-4">
                <h2 class="text-4xl font-bold font-display">Featured Courses</h2>
                <p class="text-ink2">Hand-picked selections to start your journey today.</p>
            </div>
            <a class="text-accent font-bold flex items-center gap-2 group" href="{{ route('courses.index') }}">
                Explore all
                <x-icon name="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
            </a>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($this->courses as $course)
                <div wire:key="featured-course-{{ $course->id }}">
                    <x-course-card :course="$course" />
                </div>
            @empty
                <div class="col-span-full py-12 text-center border border-dashed border-rule">
                    <p class="text-ink3">No courses found in this category.</p>
                </div>
            @endforelse
        </div>
    </section>
    @endif
</div>
