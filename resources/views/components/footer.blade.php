<footer class="bg-slate-900 text-slate-300 mt-auto">
    <div class="px-6 md:px-10 py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-16">
            <!-- Brand -->
            <div class="sm:col-span-2 lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-5">
                    <x-icon name="school" class="w-7 h-7 text-primary shrink-0" />
                    <span class="text-white text-xl font-bold tracking-tight">LearnFlow</span>
                </a>
                <p class="text-slate-400 text-sm leading-relaxed mb-6">Empowering learners worldwide with high-quality courses, expert instructors, and an engaging learning experience.</p>
                <div class="flex items-center gap-4">
                    <a href="#" class="size-9 rounded-full bg-slate-800 hover:bg-primary flex items-center justify-center transition-colors" aria-label="Twitter">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" class="size-9 rounded-full bg-slate-800 hover:bg-primary flex items-center justify-center transition-colors" aria-label="LinkedIn">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <a href="#" class="size-9 rounded-full bg-slate-800 hover:bg-primary flex items-center justify-center transition-colors" aria-label="YouTube">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Platform -->
            <div>
                <h4 class="text-white text-sm font-bold uppercase tracking-wider mb-5">Platform</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('courses.index') }}" class="text-sm hover:text-white transition-colors">Browse Courses</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Pricing</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Become an Instructor</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Resources</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div>
                <h4 class="text-white text-sm font-bold uppercase tracking-wider mb-5">Company</h4>
                <ul class="space-y-3">
                    <li><a href="#" class="text-sm hover:text-white transition-colors">About Us</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Careers</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Blog</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Contact</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h4 class="text-white text-sm font-bold uppercase tracking-wider mb-5">Support</h4>
                <ul class="space-y-3">
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Help Center</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Terms of Service</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="text-sm hover:text-white transition-colors">Cookie Policy</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} LearnFlow. All rights reserved.</p>
            <div class="flex items-center gap-6">
                <a href="#" class="text-slate-500 text-sm hover:text-white transition-colors">Terms</a>
                <a href="#" class="text-slate-500 text-sm hover:text-white transition-colors">Privacy</a>
                <a href="#" class="text-slate-500 text-sm hover:text-white transition-colors">Cookies</a>
            </div>
        </div>
    </div>
</footer>
