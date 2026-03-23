

<?php $__env->startSection('title', 'Coming Soon — ' . ($siteName ?? config('app.name'))); ?>

<?php $__env->startSection('content'); ?>
<div class="flex-1 flex items-center justify-center min-h-[70vh] px-6 py-20">
    <div class="max-w-lg w-full text-center flex flex-col items-center gap-8">

        
        <div class="size-20 rounded-full bg-primary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-[40px] text-primary">construction</span>
        </div>

        
        <div class="flex flex-col gap-3">
            <p class="text-accent font-bold tracking-[0.2em] text-xs uppercase">
                <?php echo e($siteName ?? config('app.name')); ?>

            </p>
            <h1 class="text-4xl sm:text-5xl font-extrabold font-display text-ink leading-tight">
                <?php echo e(\App\Models\Setting::get('maintenance_coming_soon_title', 'Coming Soon')); ?>

            </h1>
            <p class="text-ink2 text-lg font-body leading-relaxed">
                <?php echo e(\App\Models\Setting::get('maintenance_coming_soon_message', 'Our courses are coming soon. We\'re working hard to bring you something great — check back soon.')); ?>

            </p>
        </div>

        
        <div class="flex flex-wrap justify-center gap-4 pt-2">
            <a href="<?php echo e(route('home')); ?>"
               class="bg-primary text-white px-8 py-3 text-sm font-bold rounded-lg hover:opacity-90 hover:-translate-y-1 transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">home</span>
                Back to Home
            </a>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->check() && auth()->user()->hasRole('student')): ?>
                <a href="<?php echo e(route('dashboard')); ?>"
                   class="border-2 border-rule text-ink px-8 py-3 text-sm font-bold rounded-lg hover:bg-ink hover:text-white hover:-translate-y-1 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">dashboard</span>
                    Go to Dashboard
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\learnflow\resources\views/components/maintenance-overlay.blade.php ENDPATH**/ ?>