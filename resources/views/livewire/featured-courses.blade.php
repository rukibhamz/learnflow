<div>
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
                <x-course-card :course="$course" />
            @empty
                <div class="col-span-full py-12 text-center border border-dashed border-rule">
                    <p class="text-ink3">No courses found in this category.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
