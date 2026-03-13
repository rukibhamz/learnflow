@extends('layouts.app')

@section('title', 'Learn Without Limits')

@section('content')
    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-6 py-20 lg:py-32 grid lg:grid-cols-2 gap-16 items-center">
        <div class="flex flex-col gap-8">
            <div class="flex flex-col gap-4">
                <span class="text-accent font-bold tracking-[0.2em] text-xs uppercase">Online Learning Platform</span>
                <h1 class="text-6xl lg:text-8xl font-extrabold leading-[1.1] text-ink">
                    Learn without <span class="text-accent">limits.</span>
                </h1>
                <p class="text-lg text-ink2 max-w-lg leading-relaxed">
                    Experience the future of education with our expert-led courses designed for your success. Join over 14,000 students worldwide.
                </p>
            </div>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('register') }}" class="bg-accent text-white px-8 py-4 text-base font-bold flex items-center gap-2 rounded-card hover:opacity-90 transition-opacity">
                    Get Started
                    <x-icon name="arrow-right" class="w-4 h-4" />
                </a>
                <a href="{{ route('courses.index') }}" class="border border-ink text-ink px-8 py-4 text-base font-bold rounded-card hover:bg-ink hover:text-white transition-all">
                    View Courses
                </a>
            </div>
        </div>
        <div class="relative">
            <div class="aspect-square bg-bg border border-ink overflow-hidden">
                <img alt="Students collaborating" class="w-full h-full object-cover grayscale-[0.5] hover:grayscale-0 transition-all duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCVXyKxeNnRvM7s_AVDKWd9fGx1BtCq-kIU7eoTFDgOJ7fPUxJvhe01TQtt76uDyEhMpluqz5BHxV7xNgPUHuCA25g-rvTswWLDD6wyB_IfOY4C9W2bU0qC3AxIZOViXyMmQVeEY17tymuacYfSCqGjgiyg0Hkrtl5chmzMSBBW_TAGKPcC-PaS5FEplfc2DMqEDMSmlz5-xqOzsnEzcH6ErDQ8Y745XF0i75dJbpjD0dSmOWmvkZ1urVmuK5lQs0J-E12pB4ONXVaA"/>
            </div>
            <!-- Abstract decorative box -->
            <div class="absolute -bottom-6 -left-6 w-32 h-32 border border-ink -z-10 bg-accent/5"></div>
        </div>
    </section>

    <!-- Stats Bar -->
    <section class="border-y border-rule bg-surface">
        <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 divide-x divide-rule">
            <div class="py-10 px-8 flex flex-col gap-1 items-center md:items-start">
                <span class="text-3xl font-bold font-display">{{ number_format(\App\Models\User::count() + 14000) }}+</span>
                <span class="text-sm text-ink3 uppercase tracking-widest">Students</span>
            </div>
            <div class="py-10 px-8 flex flex-col gap-1 items-center md:items-start">
                <span class="text-3xl font-bold font-display">{{ \App\Models\Course::published()->count() }}</span>
                <span class="text-sm text-ink3 uppercase tracking-widest">Courses</span>
            </div>
            <div class="py-10 px-8 flex flex-col gap-1 items-center md:items-start">
                <span class="text-3xl font-bold font-display">{{ \App\Models\User::count() }}</span>
                <span class="text-sm text-ink3 uppercase tracking-widest">Mentors</span>
            </div>
            <div class="py-10 px-8 flex flex-col gap-1 items-center md:items-start">
                <div class="flex items-center gap-2">
                    <span class="text-3xl font-bold font-display">4.8</span>
                    <x-icon name="star-filled" class="w-6 h-6 text-amber-500 shrink-0" />
                </div>
                <span class="text-sm text-ink3 uppercase tracking-widest">Avg Rating</span>
            </div>
        </div>
    </section>
 
    <livewire:featured-courses />

    <!-- Newsletter CTA -->
    <section class="max-w-7xl mx-auto px-6 pb-20">
        <div class="bg-ink p-12 lg:p-20 flex flex-col lg:flex-row items-center gap-12">
            <div class="flex-1 flex flex-col gap-6">
                <h2 class="text-4xl lg:text-5xl font-bold font-display text-white leading-tight">Ready to start your journey?</h2>
                <p class="text-white/80 text-lg">Join thousands of students who are already advancing their careers with LearnFlow.</p>
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
                    <a href="{{ url('/') }}" class="flex items-center gap-2 mb-4">
                        <x-icon name="school" class="w-8 h-8 text-accent shrink-0" />
                        <span class="text-xl font-bold tracking-tight font-display text-ink">Learn<span class="text-accent">Flow</span></span>
                    </a>
                    <p class="text-sm text-ink2 max-w-xs">Experience the future of education with expert-led courses designed for your success.</p>
                </div>
                <div>
                    <h4 class="font-display font-bold text-sm text-ink uppercase tracking-wider mb-4">Platform</h4>
                    <ul class="space-y-2 text-sm text-ink2">
                        <li><a href="{{ route('courses.index') }}" class="hover:text-accent transition-colors">Courses</a></li>
                        <li><a href="{{ route('courses.index') }}" class="hover:text-accent transition-colors">Mentors</a></li>
                        <li><a href="{{ route('courses.index') }}" class="hover:text-accent transition-colors">Enterprise</a></li>
                        <li><a href="{{ route('courses.index') }}" class="hover:text-accent transition-colors">Resources</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-display font-bold text-sm text-ink uppercase tracking-wider mb-4">Company</h4>
                    <ul class="space-y-2 text-sm text-ink2">
                        <li><a href="#" class="hover:text-accent transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-accent transition-colors">Careers</a></li>
                        <li><a href="#" class="hover:text-accent transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-accent transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-display font-bold text-sm text-ink uppercase tracking-wider mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-ink2">
                        <li><a href="#" class="hover:text-accent transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-accent transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-accent transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-accent transition-colors">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-rule flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-ink3">
                <span>© {{ date('Y') }} LearnFlow LMS. All rights reserved.</span>
                <span>English (US) · USD ($)</span>
            </div>
        </div>
    </footer>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
@endsection
