<footer class="bg-slate-900 text-slate-300 mt-auto">
    <div class="px-6 md:px-10 py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-16">
            <!-- Brand -->
            <div class="sm:col-span-2 lg:col-span-1">
                <?php if (isset($component)) { $__componentOriginal661589163ab40cca986dd99c9fa80b42 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal661589163ab40cca986dd99c9fa80b42 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.branding','data' => ['href' => ''.e(route('home')).'','class' => 'text-white text-xl font-bold tracking-tight mb-5','variant' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('branding'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('home')).'','class' => 'text-white text-xl font-bold tracking-tight mb-5','variant' => 'dark']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal661589163ab40cca986dd99c9fa80b42)): ?>
<?php $attributes = $__attributesOriginal661589163ab40cca986dd99c9fa80b42; ?>
<?php unset($__attributesOriginal661589163ab40cca986dd99c9fa80b42); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal661589163ab40cca986dd99c9fa80b42)): ?>
<?php $component = $__componentOriginal661589163ab40cca986dd99c9fa80b42; ?>
<?php unset($__componentOriginal661589163ab40cca986dd99c9fa80b42); ?>
<?php endif; ?>
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
                    <li><a href="<?php echo e(route('courses.index')); ?>" class="text-sm hover:text-white transition-colors">Browse Courses</a></li>
                    <li><a href="<?php echo e(route('pages.mentors')); ?>" class="text-sm hover:text-white transition-colors">Mentors</a></li>
                    <li><a href="<?php echo e(route('pages.pricing')); ?>" class="text-sm hover:text-white transition-colors">Pricing</a></li>
                    <li><a href="<?php echo e(route('pages.resources')); ?>" class="text-sm hover:text-white transition-colors">Resources</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div>
                <h4 class="text-white text-sm font-bold uppercase tracking-wider mb-5">Company</h4>
                <ul class="space-y-3">
                    <li><a href="<?php echo e(route('pages.about')); ?>" class="text-sm hover:text-white transition-colors">About Us</a></li>
                    <li><a href="<?php echo e(route('blog.index')); ?>" class="text-sm hover:text-white transition-colors">Blog</a></li>
                    <li><a href="<?php echo e(route('pages.contact')); ?>" class="text-sm hover:text-white transition-colors">Contact</a></li>
                    <li><a href="<?php echo e(route('register')); ?>" class="text-sm hover:text-white transition-colors">Become an Instructor</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h4 class="text-white text-sm font-bold uppercase tracking-wider mb-5">Support</h4>
                <ul class="space-y-3">
                    <li><a href="<?php echo e(route('pages.help')); ?>" class="text-sm hover:text-white transition-colors">Help Center</a></li>
                    <li><a href="<?php echo e(route('pages.terms')); ?>" class="text-sm hover:text-white transition-colors">Terms of Service</a></li>
                    <li><a href="<?php echo e(route('pages.privacy')); ?>" class="text-sm hover:text-white transition-colors">Privacy Policy</a></li>
                    <li><a href="<?php echo e(route('pages.contact')); ?>" class="text-sm hover:text-white transition-colors">Cookie Policy</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-slate-500 text-sm">&copy; <?php echo e(date('Y')); ?> <?php echo e($siteName ?? config('app.name')); ?>. All rights reserved.</p>
            <div class="flex items-center gap-6">
                <a href="<?php echo e(route('pages.terms')); ?>" class="text-slate-500 text-sm hover:text-white transition-colors">Terms</a>
                <a href="<?php echo e(route('pages.privacy')); ?>" class="text-slate-500 text-sm hover:text-white transition-colors">Privacy</a>
                <a href="<?php echo e(route('pages.contact')); ?>" class="text-slate-500 text-sm hover:text-white transition-colors">Cookies</a>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH C:\xampp\htdocs\learnflow\resources\views/components/footer.blade.php ENDPATH**/ ?>