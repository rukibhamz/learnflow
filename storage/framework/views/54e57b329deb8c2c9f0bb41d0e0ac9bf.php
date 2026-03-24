

<?php $__env->startSection('title', 'Resources'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Hero -->
    <section class="bg-ink text-white">
        <div class="max-w-7xl mx-auto px-6 py-20 lg:py-28 text-center">
            <span class="text-accent font-bold tracking-[0.2em] text-xs uppercase">Learning Resources</span>
            <h1 class="text-5xl lg:text-7xl font-extrabold leading-[1.1] mt-4 mb-6">
                Level up your <span class="text-accent">skills.</span>
            </h1>
            <p class="text-lg text-white/70 max-w-2xl mx-auto leading-relaxed">
                Guides, tutorials, and tools to help you get the most out of your learning journey on LearnFlow.
            </p>
        </div>
    </section>

    <!-- Quick Links -->
    <section class="border-b border-rule bg-surface">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center gap-6 overflow-x-auto no-scrollbar text-sm font-bold">
            <a href="#guides" class="text-accent whitespace-nowrap">Guides</a>
            <a href="#tutorials" class="text-ink3 hover:text-accent transition-colors whitespace-nowrap">Tutorials</a>
            <a href="#tools" class="text-ink3 hover:text-accent transition-colors whitespace-nowrap">Tools</a>
            <a href="#faq" class="text-ink3 hover:text-accent transition-colors whitespace-nowrap">FAQ</a>
        </div>
    </section>

    <!-- Guides -->
    <section id="guides" class="max-w-7xl mx-auto px-6 pt-20 pb-16">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-accent font-bold tracking-[0.2em] text-xs uppercase">Getting Started</span>
                <h2 class="font-display font-extrabold text-3xl text-ink mt-2">Guides</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
                $guides = [
                    ['icon' => 'rocket_launch', 'color' => 'accent', 'title' => 'Getting Started with LearnFlow', 'desc' => 'Create your account, set up your profile, and enroll in your first course in under 5 minutes.', 'tag' => 'Beginner'],
                    ['icon' => 'school', 'color' => 'blue-600', 'title' => 'How to Complete a Course', 'desc' => 'Learn how to navigate lessons, take quizzes, track progress, and earn your completion certificate.', 'tag' => 'Beginner'],
                    ['icon' => 'workspace_premium', 'color' => 'amber-600', 'title' => 'Earning & Sharing Certificates', 'desc' => 'Once you complete a course, download your certificate as PDF and share the verification link with employers.', 'tag' => 'Beginner'],
                    ['icon' => 'quiz', 'color' => 'green-600', 'title' => 'Mastering Quizzes & Assessments', 'desc' => 'Tips and strategies for taking quizzes, understanding question types, and improving your scores.', 'tag' => 'Intermediate'],
                    ['icon' => 'payments', 'color' => 'purple-600', 'title' => 'Managing Your Subscription', 'desc' => 'How to upgrade, downgrade, cancel, or change your billing cycle from your account settings.', 'tag' => 'General'],
                    ['icon' => 'admin_panel_settings', 'color' => 'red-600', 'title' => 'Instructor Quick-Start Guide', 'desc' => 'Everything you need to know about creating courses, adding sections, uploading content, and publishing.', 'tag' => 'Instructor'],
                ];
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $guides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-surface border border-rule rounded-xl p-6 hover:border-accent/30 transition-all group">
                    <div class="w-12 h-12 bg-<?php echo e($guide['color']); ?>/10 flex items-center justify-center rounded-xl mb-5">
                        <span class="material-symbols-outlined text-<?php echo e($guide['color']); ?> text-[24px]"><?php echo e($guide['icon']); ?></span>
                    </div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 bg-bg px-2 py-0.5 rounded"><?php echo e($guide['tag']); ?></span>
                    </div>
                    <h3 class="font-display font-bold text-base text-ink mb-2 group-hover:text-accent transition-colors"><?php echo e($guide['title']); ?></h3>
                    <p class="text-sm text-ink2 leading-relaxed"><?php echo e($guide['desc']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    <!-- Tutorials -->
    <section id="tutorials" class="bg-surface border-y border-rule">
        <div class="max-w-7xl mx-auto px-6 py-20">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <span class="text-accent font-bold tracking-[0.2em] text-xs uppercase">Learn by Doing</span>
                    <h2 class="font-display font-extrabold text-3xl text-ink mt-2">Video Tutorials</h2>
                </div>
                <a href="<?php echo e(route('courses.index')); ?>" class="text-sm font-bold text-accent hover:opacity-80 transition-opacity hidden md:block">View All Courses &rarr;</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php
                    $tutorials = [
                        ['title' => 'Navigating the Student Dashboard', 'duration' => '4 min', 'desc' => 'A walkthrough of your personal dashboard including course progress, upcoming lessons, and certificate tracking.'],
                        ['title' => 'Using the Course Player', 'duration' => '6 min', 'desc' => 'Learn how to use the video player, take notes, mark lessons complete, and navigate between sections.'],
                        ['title' => 'Taking Your First Quiz', 'duration' => '5 min', 'desc' => 'Step-by-step guide to answering quiz questions, reviewing results, and retaking assessments.'],
                        ['title' => 'Setting Up Your Instructor Profile', 'duration' => '8 min', 'desc' => 'How to create your instructor account, set up your bio, and start building your first course.'],
                    ];
                ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tutorials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tut): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex gap-5 p-5 bg-bg rounded-xl border border-rule/50 hover:border-accent/30 transition-all group">
                        <div class="w-20 h-20 bg-ink/5 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-accent/10 transition-colors">
                            <span class="material-symbols-outlined text-ink3 text-[32px] group-hover:text-accent transition-colors">play_circle</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-bold text-ink3 uppercase tracking-widest"><?php echo e($tut['duration']); ?></span>
                            </div>
                            <h3 class="font-display font-bold text-[15px] text-ink mb-1 group-hover:text-accent transition-colors"><?php echo e($tut['title']); ?></h3>
                            <p class="text-sm text-ink2 leading-relaxed line-clamp-2"><?php echo e($tut['desc']); ?></p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Tools & Downloads -->
    <section id="tools" class="max-w-7xl mx-auto px-6 py-20">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-accent font-bold tracking-[0.2em] text-xs uppercase">Useful Extras</span>
                <h2 class="font-display font-extrabold text-3xl text-ink mt-2">Tools & Downloads</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
                $tools = [
                    ['icon' => 'calendar_month', 'title' => 'Study Planner', 'desc' => 'A weekly planner template to organize your learning schedule.'],
                    ['icon' => 'note_alt', 'title' => 'Note-Taking Template', 'desc' => 'Structured templates for taking effective course notes.'],
                    ['icon' => 'checklist', 'title' => 'Course Completion Checklist', 'desc' => 'Track your progress across multiple courses at a glance.'],
                    ['icon' => 'lightbulb', 'title' => 'Learning Tips PDF', 'desc' => 'Science-backed study techniques to retain more from each lesson.'],
                ];
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-surface border border-rule rounded-xl p-6 text-center hover:border-accent/30 transition-all group">
                    <div class="w-14 h-14 bg-accent/5 flex items-center justify-center rounded-xl mx-auto mb-4 group-hover:bg-accent/10 transition-colors">
                        <span class="material-symbols-outlined text-accent text-[28px]"><?php echo e($tool['icon']); ?></span>
                    </div>
                    <h4 class="font-display font-bold text-sm text-ink mb-1"><?php echo e($tool['title']); ?></h4>
                    <p class="text-xs text-ink3 leading-relaxed"><?php echo e($tool['desc']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="border-t border-rule bg-surface">
        <div class="max-w-3xl mx-auto px-6 py-20">
            <h2 class="font-display font-extrabold text-3xl text-ink text-center mb-12">Common Questions</h2>

            <div class="space-y-4" x-data="{ open: null }">
                <?php
                    $faqs = [
                        ['q' => 'How do I reset my password?', 'a' => 'Click "Forgot password?" on the login page. Enter your email address and we\'ll send you a secure reset link.'],
                        ['q' => 'Can I access courses on mobile?', 'a' => 'Yes! LearnFlow is fully responsive and works on all modern mobile browsers. You can learn on any device with an internet connection.'],
                        ['q' => 'How long do I have access to a course?', 'a' => 'Once enrolled, you have lifetime access to free courses and individual purchases. Subscription courses are accessible as long as your subscription is active.'],
                        ['q' => 'How do I become an instructor?', 'a' => 'Sign up for a free account, then apply to become an instructor from your dashboard. Our team reviews applications within 48 hours.'],
                        ['q' => 'Who do I contact for technical issues?', 'a' => 'Reach our support team at support@learnflow.ai or visit the Help Center. We typically respond within 24 hours.'],
                    ];
                ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border border-rule rounded-xl overflow-hidden">
                        <button @click="open = open === <?php echo e($i); ?> ? null : <?php echo e($i); ?>"
                                class="w-full flex items-center justify-between p-5 text-left hover:bg-bg/50 transition-colors">
                            <span class="font-display font-bold text-[15px] text-ink pr-4"><?php echo e($faq['q']); ?></span>
                            <span class="material-symbols-outlined text-ink3 text-[20px] transition-transform shrink-0"
                                  :class="open === <?php echo e($i); ?> && 'rotate-180'">expand_more</span>
                        </button>
                        <div x-show="open === <?php echo e($i); ?>" x-transition class="px-5 pb-5">
                            <p class="text-sm text-ink2 leading-relaxed"><?php echo e($faq['a']); ?></p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="max-w-7xl mx-auto px-6 py-20">
        <div class="bg-ink p-12 lg:p-20 flex flex-col lg:flex-row items-center gap-12">
            <div class="flex-1 flex flex-col gap-4">
                <h2 class="text-4xl lg:text-5xl font-bold font-display text-white leading-tight">Can't find what you need?</h2>
                <p class="text-white/80 text-lg">Our support team is here to help. Reach out anytime and we'll get back to you within 24 hours.</p>
            </div>
            <a href="<?php echo e(route('pages.contact')); ?>" class="bg-accent text-white font-bold px-10 py-4 rounded-card whitespace-nowrap hover:opacity-90 transition-opacity text-base">
                Contact Support
            </a>
        </div>
    </section>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\learnflow\resources\views/pages/resources.blade.php ENDPATH**/ ?>