

<?php
    $couponCode = session('coupon_code');
    $couponValidation = null;
    $discountAmount = 0.0;
    if (auth()->check() && $couponCode && $course->price > 0) {
        $couponValidation = app(\App\Services\CouponService::class)->validate($couponCode, auth()->user(), (float) $course->price);
        if (! $couponValidation->valid) {
            session()->forget('coupon_code');
            $couponValidation = null;
        } else {
            $discountAmount = (float) $couponValidation->discount_amount;
        }
    }
?>

<?php $__env->startSection('title', $course->title . ' - LearnFlow'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="{ showPreviewModal: false, previewLesson: null }">
    
    <div class="bg-ink text-white">
        <div class="max-w-7xl mx-auto px-6 py-12 lg:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-3 py-1 bg-white/20 text-white text-xs font-bold uppercase rounded-full"><?php echo e(ucfirst($course->level?->value ?? 'All Levels')); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->category): ?>
                            <span class="text-white/60 text-sm"><?php echo e($course->category); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <h1 class="font-display font-extrabold text-3xl md:text-4xl lg:text-5xl leading-tight mb-6"><?php echo e($course->title); ?></h1>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->short_description): ?>
                        <p class="text-lg text-white/80 mb-6 max-w-2xl"><?php echo e($course->short_description); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="flex flex-wrap items-center gap-6 mb-6">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->reviews_avg_rating): ?>
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-bold text-amber-400"><?php echo e(number_format($course->reviews_avg_rating, 1)); ?></span>
                                <div class="flex">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= 5; $i++): ?>
                                        <span class="material-symbols-outlined text-[18px] <?php echo e($i <= round($course->reviews_avg_rating) ? 'text-amber-400' : 'text-white/30'); ?>" style="font-variation-settings: 'FILL' 1">star</span>
                                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <span class="text-white/60">(<?php echo e(number_format($course->reviews_count)); ?> reviews)</span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="flex items-center gap-2 text-white/80">
                            <span class="material-symbols-outlined text-[20px]">group</span>
                            <?php echo e(number_format($course->enrollments_count)); ?> students enrolled
                        </div>
                    </div>

                    
                    <div class="flex items-center gap-4">
                        <img src="<?php echo e($course->instructor?->avatar_url); ?>" alt="<?php echo e($course->instructor?->name); ?>" class="w-12 h-12 rounded-full border-2 border-white/30">
                        <div>
                            <p class="text-sm text-white/60">Created by</p>
                            <p class="font-medium text-white"><?php echo e($course->instructor?->name ?? 'Unknown Instructor'); ?></p>
                        </div>
                    </div>

                    
                    <div class="flex flex-wrap gap-6 mt-8 pt-6 border-t border-white/20">
                        <div class="flex items-center gap-2 text-white/80">
                            <span class="material-symbols-outlined text-[20px]">schedule</span>
                            <?php echo e(floor($totalDuration / 3600)); ?>h <?php echo e(floor(($totalDuration % 3600) / 60)); ?>m total
                        </div>
                        <div class="flex items-center gap-2 text-white/80">
                            <span class="material-symbols-outlined text-[20px]">play_lesson</span>
                            <?php echo e($totalLessons); ?> lessons
                        </div>
                        <div class="flex items-center gap-2 text-white/80">
                            <span class="material-symbols-outlined text-[20px]">folder</span>
                            <?php echo e($curriculumSections->count()); ?> sections
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->language): ?>
                            <div class="flex items-center gap-2 text-white/80">
                                <span class="material-symbols-outlined text-[20px]">language</span>
                                <?php echo e(strtoupper($course->language)); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="hidden lg:block">
                    <div class="bg-surface text-ink rounded-2xl shadow-2xl overflow-hidden sticky top-24">
                        
                        <div class="aspect-video bg-bg relative">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->getFirstMediaUrl('thumbnail')): ?>
                                <img src="<?php echo e($course->getFirstMediaUrl('thumbnail')); ?>" alt="<?php echo e($course->title); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-primary/10">
                                    <span class="material-symbols-outlined text-[64px] text-primary/30">school</span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="p-6">
                            
                            <div class="flex items-center gap-3 mb-6">
                                <span class="font-display font-extrabold text-4xl <?php echo e($course->price > 0 ? 'text-ink' : 'text-green-600'); ?>">
                                    <?php echo e(format_price($course->price)); ?>

                                </span>
                            </div>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEnrolled): ?>
                                    <a href="<?php echo e(route('learn.show', $course->slug)); ?>" class="block w-full py-4 bg-primary text-white font-display font-bold text-center rounded-xl hover:opacity-90 transition-opacity">
                                        Continue Learning
                                    </a>
                                <?php elseif(! $prerequisitesMet): ?>
                                    <div class="w-full py-4 bg-gray-300 text-gray-500 font-display font-bold text-center rounded-xl cursor-not-allowed">
                                        Complete Prerequisites First
                                    </div>
                                <?php else: ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->price > 0): ?>
                                        <div class="space-y-4">
                                            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('coupon-field', ['orderAmount' => (float) $course->price]);

$__key = 'coupon-field-'.$course->id;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3599523163-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($couponValidation && $discountAmount > 0): ?>
                                                <div class="text-xs text-ink2 border border-rule rounded-lg p-3 bg-bg">
                                                    <div class="flex items-center justify-between">
                                                        <span>Subtotal</span>
                                                        <span><?php echo e(format_price((float) $course->price)); ?></span>
                                                    </div>
                                                    <div class="flex items-center justify-between mt-1">
                                                        <span>Discount (<?php echo e(strtoupper($couponValidation->code)); ?>)</span>
                                                        <span class="text-green-700 font-medium">-<?php echo e(format_price($discountAmount)); ?></span>
                                                    </div>
                                                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-rule">
                                                        <span class="font-bold">Total</span>
                                                        <span class="font-bold"><?php echo e(format_price(max(0, (float) $course->price - $discountAmount))); ?></span>
                                                    </div>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            <form action="<?php echo e(route('checkout.course', $course)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="w-full py-4 bg-ink text-white font-display font-bold rounded-xl hover:opacity-90 transition-opacity">
                                                    Checkout
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <form action="<?php echo e(route('enrolments.store')); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="course_id" value="<?php echo e($course->id); ?>">
                                            <button type="submit" class="w-full py-4 bg-ink text-white font-display font-bold rounded-xl hover:opacity-90 transition-opacity">
                                                Enrol for Free
                                            </button>
                                        </form>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <a href="<?php echo e(route('login')); ?>?redirect=<?php echo e(urlencode(request()->url())); ?>" class="block w-full py-4 bg-ink text-white font-display font-bold text-center rounded-xl hover:opacity-90 transition-opacity">
                                    Login to Enrol
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <div class="mt-6 pt-6 border-t border-rule space-y-3">
                                <div class="flex items-center gap-3 text-sm text-ink2">
                                    <span class="material-symbols-outlined text-[20px] text-green-500">check_circle</span>
                                    Full lifetime access
                                </div>
                                <div class="flex items-center gap-3 text-sm text-ink2">
                                    <span class="material-symbols-outlined text-[20px] text-green-500">check_circle</span>
                                    Access on mobile and desktop
                                </div>
                                <div class="flex items-center gap-3 text-sm text-ink2">
                                    <span class="material-symbols-outlined text-[20px] text-green-500">check_circle</span>
                                    Certificate of completion
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-surface border-t border-rule p-4 z-40">
        <div class="flex items-center justify-between gap-4">
            <div>
                <span class="font-display font-extrabold text-2xl <?php echo e($course->price > 0 ? 'text-ink' : 'text-green-600'); ?>">
                    <?php echo e(format_price($course->price)); ?>

                </span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEnrolled): ?>
                    <a href="<?php echo e(route('learn.show', $course->slug)); ?>" class="flex-1 py-3 bg-primary text-white font-display font-bold text-center rounded-xl">
                        Continue Learning
                    </a>
                <?php elseif(! $prerequisitesMet): ?>
                    <span class="flex-1 py-3 bg-gray-300 text-gray-500 font-display font-bold text-center rounded-xl cursor-not-allowed">
                        Prerequisites Required
                    </span>
                <?php else: ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->price > 0): ?>
                        <form action="<?php echo e(route('checkout.course', $course)); ?>" method="POST" class="flex-1">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full py-3 bg-ink text-white font-display font-bold rounded-xl">
                                Checkout
                            </button>
                        </form>
                    <?php else: ?>
                        <form action="<?php echo e(route('enrolments.store')); ?>" method="POST" class="flex-1">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="course_id" value="<?php echo e($course->id); ?>">
                            <button type="submit" class="w-full py-3 bg-ink text-white font-display font-bold rounded-xl">
                                Enrol Free
                            </button>
                        </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="flex-1 py-3 bg-ink text-white font-display font-bold text-center rounded-xl">
                    Login to Enrol
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="max-w-7xl mx-auto px-6 py-12 lg:pb-12 pb-32">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-12">
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->outcomes && count($course->outcomes) > 0): ?>
                <section>
                    <h2 class="font-display font-bold text-2xl text-ink mb-6">What you'll learn</h2>
                    <div class="bg-surface border border-rule rounded-xl p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $course->outcomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outcome): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-[20px] text-green-500 shrink-0 mt-0.5">check_circle</span>
                                    <span class="text-sm text-ink2"><?php echo e($outcome); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->requirements && count($course->requirements) > 0): ?>
                <section>
                    <h2 class="font-display font-bold text-2xl text-ink mb-6">Requirements</h2>
                    <ul class="space-y-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $course->requirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $requirement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[18px] text-ink3 shrink-0 mt-0.5">arrow_right</span>
                                <span class="text-sm text-ink2"><?php echo e($requirement); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prerequisites->isNotEmpty()): ?>
                <section>
                    <h2 class="font-display font-bold text-2xl text-ink mb-6">Prerequisites</h2>
                    <div class="bg-surface border border-rule rounded-xl p-6 space-y-3">
                        <p class="text-sm text-ink2 mb-4">Complete these courses before enrolling:</p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $prerequisites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prereq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $completed = auth()->check()
                                    ? \App\Models\Enrollment::where('user_id', auth()->id())
                                        ->where('course_id', $prereq->id)
                                        ->whereNotNull('completed_at')
                                        ->exists()
                                    : false;
                            ?>
                            <a href="<?php echo e(route('courses.show', $prereq->slug)); ?>" class="flex items-center gap-3 p-3 rounded-lg hover:bg-bg transition-colors">
                                <span class="material-symbols-outlined text-[20px] <?php echo e($completed ? 'text-green-500' : 'text-ink3'); ?>" style="font-variation-settings: 'FILL' <?php echo e($completed ? '1' : '0'); ?>">
                                    <?php echo e($completed ? 'check_circle' : 'radio_button_unchecked'); ?>

                                </span>
                                <span class="text-sm <?php echo e($completed ? 'text-ink line-through' : 'text-primary font-medium'); ?>"><?php echo e($prereq->title); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($completed): ?>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-green-600 ml-auto">Completed</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $prerequisitesMet): ?>
                                <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                    <p class="text-xs text-amber-800 font-medium">You must complete all prerequisites before you can enrol in this course.</p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <section>
                    <h2 class="font-display font-bold text-2xl text-ink mb-6">Course Curriculum</h2>
                    <div class="bg-surface border border-rule rounded-xl overflow-hidden divide-y divide-rule" x-data="{ openSection: 0 }">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $curriculumSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>
                                <button @click="openSection = openSection === <?php echo e($index); ?> ? null : <?php echo e($index); ?>" 
                                    class="w-full flex items-center justify-between p-5 text-left hover:bg-bg transition-colors">
                                    <div class="flex items-center gap-4">
                                        <span class="material-symbols-outlined text-[20px] text-ink3 transition-transform" :class="{ 'rotate-90': openSection === <?php echo e($index); ?> }">chevron_right</span>
                                        <div>
                                            <h3 class="font-display font-bold text-sm text-ink"><?php echo e($section['title'] ?? ''); ?></h3>
                                            <p class="text-xs text-ink3 mt-1"><?php echo e(count($section['lessons'] ?? [])); ?> lessons · <?php echo e(floor(collect($section['lessons'] ?? [])->sum('duration_seconds') / 60)); ?> min</p>
                                        </div>
                                    </div>
                                </button>
                                <div x-show="openSection === <?php echo e($index); ?>" x-collapse class="border-t border-rule bg-bg">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($section['lessons'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center gap-4 px-5 py-3 <?php echo e(!$loop->last ? 'border-b border-rule' : ''); ?>">
                                            <?php
                                                $typeIcons = ['video' => 'play_circle', 'text' => 'article', 'pdf' => 'picture_as_pdf', 'embed' => 'code'];
                                            ?>
                                            <span class="material-symbols-outlined text-[20px] text-ink3"><?php echo e($typeIcons[$lesson['type'] ?? 'video'] ?? 'description'); ?></span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-ink truncate"><?php echo e($lesson['title'] ?? ''); ?></p>
                                            </div>
                                            <div class="flex items-center gap-3 shrink-0">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($lesson['is_preview'])): ?>
                                                    <button @click="showPreviewModal = true; previewLesson = { id: <?php echo e($lesson['id'] ?? 0); ?>, title: '<?php echo e(addslashes($lesson['title'] ?? '')); ?>', type: '<?php echo e($lesson['type'] ?? ''); ?>', url: '<?php echo e($lesson['content_url'] ?? ''); ?>', body: <?php echo e(json_encode($lesson['content_body'] ?? null)); ?> }" 
                                                        class="px-2 py-1 bg-primary/10 text-primary text-[10px] font-bold uppercase rounded hover:bg-primary/20 transition-colors">
                                                        Preview
                                                    </button>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($lesson['duration_seconds'])): ?>
                                                    <span class="text-xs text-ink3"><?php echo e(gmdate('i:s', $lesson['duration_seconds'])); ?></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </section>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->description): ?>
                <section>
                    <h2 class="font-display font-bold text-2xl text-ink mb-6">Description</h2>
                    <div class="prose prose-sm max-w-none text-ink2">
                        <?php echo $course->description; ?>

                    </div>
                </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <section>
                    <h2 class="font-display font-bold text-2xl text-ink mb-6">Your Instructor</h2>
                    <div class="bg-surface border border-rule rounded-xl p-6">
                        <div class="flex items-start gap-6">
                            <img src="<?php echo e($course->instructor?->avatar_url); ?>" alt="<?php echo e($course->instructor?->name); ?>" class="w-24 h-24 rounded-full">
                            <div class="flex-1">
                                <h3 class="font-display font-bold text-lg text-ink"><?php echo e($course->instructor?->name); ?></h3>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->instructor?->bio): ?>
                                    <p class="text-sm text-ink2 mt-2"><?php echo e($course->instructor->bio); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="flex items-center gap-6 mt-4 text-sm text-ink3">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[18px]">school</span>
                                        <?php echo e($instructorCourseCount ?? 0); ?> courses
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[18px]">group</span>
                                        <?php echo e(number_format($instructorStudentCount ?? 0)); ?> students
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($course->reviews->count() > 0): ?>
                <section>
                    <h2 class="font-display font-bold text-2xl text-ink mb-6">Student Reviews</h2>
                    <div class="space-y-6">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $course->reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-surface border border-rule rounded-xl p-6">
                                <div class="flex items-start gap-4">
                                    <img src="<?php echo e($review->user?->avatar_url); ?>" alt="<?php echo e($review->user?->name); ?>" class="w-12 h-12 rounded-full">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-medium text-ink"><?php echo e($review->user?->name); ?></h4>
                                            <span class="text-xs text-ink3"><?php echo e($review->created_at->diffForHumans()); ?></span>
                                        </div>
                                        <div class="flex mb-3">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 1; $i <= 5; $i++): ?>
                                                <span class="material-symbols-outlined text-[16px] <?php echo e($i <= $review->rating ? 'text-amber-400' : 'text-gray-200'); ?>" style="font-variation-settings: 'FILL' 1">star</span>
                                            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($review->comment): ?>
                                            <p class="text-sm text-ink2"><?php echo e($review->comment); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="hidden lg:block"></div>
        </div>
    </div>

    
    <div x-show="showPreviewModal" x-cloak 
        class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4"
        @click.self="showPreviewModal = false"
        @keydown.escape.window="showPreviewModal = false">
        <div class="bg-surface rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden" @click.stop>
            <div class="flex items-center justify-between p-4 border-b border-rule">
                <h3 class="font-display font-bold text-lg text-ink" x-text="previewLesson?.title"></h3>
                <button @click="showPreviewModal = false" class="p-2 hover:bg-bg rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                <template x-if="previewLesson?.type === 'video' && previewLesson?.url">
                    <div class="aspect-video bg-black rounded-lg overflow-hidden">
                        <iframe :src="previewLesson.url.includes('youtube') ? previewLesson.url.replace('watch?v=', 'embed/') : previewLesson.url" 
                            class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                    </div>
                </template>
                <template x-if="previewLesson?.type === 'text' && previewLesson?.body">
                    <div class="prose prose-sm max-w-none" x-html="previewLesson.body"></div>
                </template>
                <template x-if="previewLesson?.type === 'pdf'">
                    <p class="text-center text-ink3 py-12">PDF preview not available. Enrol to access.</p>
                </template>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\learnflow\resources\views/courses/show.blade.php ENDPATH**/ ?>