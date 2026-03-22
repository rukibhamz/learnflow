@extends('layouts.app')

@section('title', 'Learn Without Limits')

@push('head')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('heroSlider', () => ({
            activeSlide: 1,
            autoplayInterval: null,
            autoplaySpeed: window._heroAutoplaySpeed || 6000,
            totalSlides: window._heroTotalSlides || 1,

            init() {
                if (this.totalSlides > 1) {
                    this.startAutoplay();
                }
            },

            next() {
                this.activeSlide = this.activeSlide === this.totalSlides ? 1 : this.activeSlide + 1;
            },

            prev() {
                this.activeSlide = this.activeSlide === 1 ? this.totalSlides : this.activeSlide - 1;
            },

            startAutoplay() {
                if (this.autoplayInterval) return;
                this.autoplayInterval = setInterval(() => this.next(), this.autoplaySpeed);
            },

            stopAutoplay() {
                clearInterval(this.autoplayInterval);
                this.autoplayInterval = null;
            },

            resetAutoplay() {
                this.stopAutoplay();
                this.startAutoplay();
            }
        }));
    });
</script>
@endpush

@section('content')
    <script>
        window._heroTotalSlides = {{ $slides->count() ?: 1 }};
        window._heroAutoplaySpeed = {{ \App\Models\Setting::get('hero_autoplay_speed', 6000) }};
    </script>
    <!-- Hero Slider Section -->
    <section x-data="heroSlider" 
             @mouseenter="stopAutoplay()"
             @mouseleave="startAutoplay()"
             class="relative overflow-hidden w-full bg-surface" style="min-height:600px">
        
        <!-- Slider Track -->
        <div class="relative w-full" style="min-height:600px">
            @forelse($slides as $index => $slide)
                <div x-show="activeSlide === {{ $index + 1 }}"
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 translate-x-full"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-700"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-full"
                     class="absolute inset-0 w-full h-full flex items-center">
                    
                    <div class="w-full max-w-7xl mx-auto px-6 py-16 lg:py-32 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <!-- Content Side -->
                        <div class="flex flex-col gap-8 order-2 lg:order-1">
                            <div class="flex flex-col gap-6">
                                <span class="text-accent font-bold tracking-[0.2em] text-xs uppercase">{{ $slide->tag }}</span>
                                
                                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold leading-tight text-ink">{!! $slide->title !!}</h1>
                                
                                <p class="text-lg sm:text-xl text-ink2 max-w-lg leading-relaxed font-body">{{ $slide->description }}</p>
                            </div>
                            
                            <div class="flex flex-wrap gap-4 pt-2">
                                <a href="{{ route('register') }}" class="bg-primary text-white px-8 py-4 text-sm sm:text-base font-bold flex items-center gap-2 rounded-lg hover:opacity-90 hover:-translate-y-1 transition-all shadow-lg shadow-primary/20">
                                    Get Started
                                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                                </a>
                                <a href="{{ route('courses.index') }}" class="border-2 border-rule text-ink px-8 py-4 text-sm sm:text-base font-bold rounded-lg hover:bg-ink hover:text-white hover:-translate-y-1 transition-all">
                                    View Courses
                                </a>
                            </div>
                        </div>

                        <!-- Image Side -->
                        <div class="relative order-1 lg:order-2">
                            <div class="aspect-[4/3] lg:aspect-square bg-background-light border border-rule overflow-hidden rounded-2xl shadow-2xl group cursor-zoom-in">
                                <img src="{{ $slide->getFirstMediaUrl('background') ?: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=2671&auto=format&fit=crop' }}" 
                                     alt="{{ $slide->tag }}" 
                                     class="w-full h-full object-cover grayscale-[0.1] group-hover:grayscale-0 group-hover:scale-110 transition-all duration-700" />
                            </div>
                            <!-- Abstract decorative elements -->
                            <div class="hidden sm:block absolute -bottom-8 -right-8 w-40 h-40 border-2 border-primary/10 -z-10 rounded-full animate-pulse"></div>
                            <div class="hidden sm:block absolute -top-8 -left-8 w-24 h-24 bg-accent/5 -z-10 rounded-xl rotate-12"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="absolute inset-0 w-full h-full flex items-center">
                    <div class="w-full max-w-7xl mx-auto px-6 py-16 lg:py-32 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <!-- Content Side -->
                        <div class="flex flex-col gap-8 order-2 lg:order-1">
                            <div class="flex flex-col gap-6">
                                <span class="text-accent font-bold tracking-[0.2em] text-xs uppercase">Online Learning Platform</span>
                                
                                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold leading-tight text-ink">Learn without <span class="text-accent">limits.</span></h1>
                                
                                <p class="text-lg sm:text-xl text-ink2 max-w-lg leading-relaxed font-body">Experience the future of education with our expert-led courses designed for your success.</p>
                            </div>
                            
                            <div class="flex flex-wrap gap-4 pt-2">
                                <a href="{{ route('register') }}" class="bg-primary text-white px-8 py-4 text-sm sm:text-base font-bold flex items-center gap-2 rounded-lg hover:opacity-90 hover:-translate-y-1 transition-all shadow-lg shadow-primary/20">
                                    Get Started
                                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                                </a>
                                <a href="{{ route('courses.index') }}" class="border-2 border-rule text-ink px-8 py-4 text-sm sm:text-base font-bold rounded-lg hover:bg-ink hover:text-white hover:-translate-y-1 transition-all">
                                    View Courses
                                </a>
                            </div>
                        </div>

                        <!-- Image Side -->
                        <div class="relative order-1 lg:order-2">
                            <div class="aspect-[4/3] lg:aspect-square bg-background-light border border-rule overflow-hidden rounded-2xl shadow-2xl group cursor-zoom-in">
                                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=2671&auto=format&fit=crop" 
                                     alt="Online Learning Platform" 
                                     class="w-full h-full object-cover grayscale-[0.1] group-hover:grayscale-0 group-hover:scale-110 transition-all duration-700" />
                            </div>
                            <!-- Abstract decorative elements -->
                            <div class="hidden sm:block absolute -bottom-8 -right-8 w-40 h-40 border-2 border-primary/10 -z-10 rounded-full animate-pulse"></div>
                            <div class="hidden sm:block absolute -top-8 -left-8 w-24 h-24 bg-accent/5 -z-10 rounded-xl rotate-12"></div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination Dots -->
        @if($slides->count() > 1)
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex items-center gap-4 z-40">
            @foreach($slides as $index => $slide)
                <button @click="activeSlide = {{ $index + 1 }}; resetAutoplay();" 
                        class="h-1.5 rounded-full transition-all duration-500"
                        :class="activeSlide === {{ $index + 1 }} ? 'w-10 bg-primary' : 'w-4 bg-rule hover:bg-ink/20'"
                        aria-label="Go to slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
        @endif

        <!-- Navigation Arrows -->
        @if($slides->count() > 1)
        <div class="hidden md:flex absolute inset-y-0 left-0 right-0 items-center justify-between px-8 pointer-events-none z-50">
            <button @click="prev(); resetAutoplay();" 
                    class="pointer-events-auto group relative size-14 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-md border border-rule shadow-xl hover:bg-primary hover:text-white transition-all transform hover:scale-110 active:scale-95"
                    aria-label="Previous slide">
                <span class="material-symbols-outlined text-[28px] group-hover:-translate-x-1 transition-transform">chevron_left</span>
            </button>
            <button @click="next(); resetAutoplay();" 
                    class="pointer-events-auto group relative size-14 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-md border border-rule shadow-xl hover:bg-primary hover:text-white transition-all transform hover:scale-110 active:scale-95"
                    aria-label="Next slide">
                <span class="material-symbols-outlined text-[28px] group-hover:translate-x-1 transition-transform">chevron_right</span>
            </button>
        </div>
        @endif
    </section>

    <!-- Stats Bar -->
    <section class="reveal border-y border-rule bg-surface shadow-sm relative z-10">
        <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 divide-x divide-rule">
            <div class="py-12 px-8 flex flex-col gap-2 items-center group hover:bg-background-light/50 transition-colors">
                <span class="text-4xl font-bold font-display text-primary group-hover:scale-110 transition-transform">{{ \App\Models\Setting::get('stat_students', '14,000+') }}</span>
                <span class="text-[11px] font-bold text-ink3 uppercase tracking-[0.2em] font-poppins">Students Joined</span>
            </div>
            <div class="py-12 px-8 flex flex-col gap-2 items-center group hover:bg-background-light/50 transition-colors">
                <span class="text-4xl font-bold font-display text-accent group-hover:scale-110 transition-transform">{{ \App\Models\Setting::get('stat_courses', '1,200+') }}</span>
                <span class="text-[11px] font-bold text-ink3 uppercase tracking-[0.2em] font-poppins">Courses Available</span>
            </div>
            <div class="py-12 px-8 flex flex-col gap-2 items-center group hover:bg-background-light/50 transition-colors">
                <span class="text-4xl font-bold font-display text-primary group-hover:scale-110 transition-transform">{{ \App\Models\Setting::get('stat_mentors', '450+') }}</span>
                <span class="text-[11px] font-bold text-ink3 uppercase tracking-[0.2em] font-poppins">Expert Mentors</span>
            </div>
            <div class="py-12 px-8 flex flex-col gap-2 items-center group hover:bg-background-light/50 transition-colors">
                <div class="flex items-center gap-2">
                    <span class="text-4xl font-bold font-display text-accent group-hover:scale-110 transition-transform">{{ \App\Models\Setting::get('stat_rating', '4.8/5') }}</span>
                    <span class="material-symbols-outlined text-amber-400 text-[24px] fill-1">star</span>
                </div>
                <span class="text-[11px] font-bold text-ink3 uppercase tracking-[0.2em] font-poppins">Satisfaction Rate</span>
            </div>
        </div>
    </section>
 
    <div class="reveal">
        <livewire:featured-courses />
    </div>

    <!-- Newsletter CTA -->
    <section class="reveal max-w-7xl mx-auto px-6 pb-20">
        <div class="bg-ink p-12 lg:p-20 flex flex-col lg:flex-row items-center gap-12">
            <div class="flex-1 flex flex-col gap-6">
                <h2 class="text-4xl lg:text-5xl font-bold font-display text-white leading-tight">Ready to start your journey?</h2>
                <p class="text-white/80 text-lg">Join thousands of students who are already advancing their careers with {{ $siteName ?? config('app.name') }}.</p>
            </div>
            <div class="w-full lg:w-auto flex flex-col sm:flex-row gap-4">
                <input class="bg-white/10 border border-white/20 rounded-card text-white px-6 py-4 w-full sm:w-80 placeholder:text-white/60 focus:ring-1 focus:ring-accent focus:border-accent" placeholder="Enter your email" type="email"/>
                <button class="bg-accent text-white font-bold px-8 py-4 rounded-card whitespace-nowrap hover:opacity-90 transition-opacity">Join Now</button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-rule bg-surface mt-20">
        <div class="max-w-7xl mx-auto px-6 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                <div class="lg:col-span-1">
                    <x-branding href="{{ url('/') }}" class="mb-4" variant="split" />
                    <p class="text-sm text-ink2 max-w-xs">Experience the future of education with expert-led courses designed for your success.</p>
                </div>
                <div>
                    <h4 class="font-display font-bold text-sm text-ink uppercase tracking-wider mb-4">Platform</h4>
                    <ul class="space-y-2 text-sm text-ink2">
                        <li><a href="{{ route('courses.index') }}" class="hover:text-accent transition-colors">Courses</a></li>
                        <li><a href="{{ route('pages.mentors') }}" class="hover:text-accent transition-colors">Mentors</a></li>
                        <li><a href="{{ route('pages.pricing') }}" class="hover:text-accent transition-colors">Pricing</a></li>
                        <li><a href="{{ route('pages.resources') }}" class="hover:text-accent transition-colors">Resources</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-display font-bold text-sm text-ink uppercase tracking-wider mb-4">Company</h4>
                    <ul class="space-y-2 text-sm text-ink2">
                        <li><a href="{{ route('pages.about') }}" class="hover:text-accent transition-colors">About Us</a></li>
                        <li><a href="{{ route('pages.about') }}" class="hover:text-accent transition-colors">Careers</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-accent transition-colors">Blog</a></li>
                        <li><a href="{{ route('pages.contact') }}" class="hover:text-accent transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-display font-bold text-sm text-ink uppercase tracking-wider mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-ink2">
                        <li><a href="{{ route('pages.help') }}" class="hover:text-accent transition-colors">Help Center</a></li>
                        <li><a href="{{ route('pages.terms') }}" class="hover:text-accent transition-colors">Terms of Service</a></li>
                        <li><a href="{{ route('pages.privacy') }}" class="hover:text-accent transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('pages.privacy') }}" class="hover:text-accent transition-colors">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-rule flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-ink3">
                <span>© {{ date('Y') }} {{ $siteName ?? config('app.name') }} LMS. All rights reserved.</span>
                <span>English (US) · USD ($)</span>
            </div>
        </div>
    </footer>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
@endsection
