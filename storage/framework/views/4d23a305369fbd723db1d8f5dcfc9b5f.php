<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo $__env->yieldContent('title', 'Login'); ?> - <?php echo e(config('app.name', 'LearnFlow')); ?></title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

        <?php echo $__env->make('partials.brand-styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </head>
    <body class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col items-center justify-center p-4 font-display antialiased">
        <div class="mb-8 flex items-center gap-2">
            <?php if (isset($component)) { $__componentOriginal661589163ab40cca986dd99c9fa80b42 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal661589163ab40cca986dd99c9fa80b42 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.branding','data' => ['href' => ''.e(url('/')).'','class' => 'text-brand-black dark:text-slate-100 text-2xl font-extrabold tracking-tight','variant' => 'split']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('branding'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(url('/')).'','class' => 'text-brand-black dark:text-slate-100 text-2xl font-extrabold tracking-tight','variant' => 'split']); ?>
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
        </div>

        <div class="w-full max-w-[400px]">
            <?php echo e($slot); ?>

        </div>

        <footer class="mt-8 text-neutral-text dark:text-slate-500 text-[11px] uppercase tracking-widest text-center">
            © <?php echo e(date('Y')); ?> <?php echo e($siteName ?? config('app.name')); ?> Inc.
            <a class="hover:text-brand-black dark:hover:text-slate-300 transition-colors" href="<?php echo e(route('pages.privacy')); ?>">Privacy</a>
            <span class="mx-1">•</span>
            <a class="hover:text-brand-black dark:hover:text-slate-300 transition-colors" href="<?php echo e(route('pages.terms')); ?>">Terms</a>
        </footer>
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    </body>
</html>
<?php /**PATH C:\xampp\htdocs\learnflow\resources\views/layouts/guest.blade.php ENDPATH**/ ?>