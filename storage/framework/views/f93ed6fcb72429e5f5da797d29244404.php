<?php

use Livewire\Volt\Component;

?>

<nav x-data="{ open: false }" class="bg-surface border-b border-rule h-20 flex items-center sticky top-0 z-50 w-full">
    <!-- Primary Navigation Menu -->
    <div class="px-6 md:px-10 w-full">
        <div class="flex justify-between h-20 items-center">
            <div class="flex items-center gap-12">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <?php if (isset($component)) { $__componentOriginal661589163ab40cca986dd99c9fa80b42 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal661589163ab40cca986dd99c9fa80b42 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.branding','data' => ['href' => ''.e(route('home')).'','variant' => 'split']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('branding'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('home')).'','variant' => 'split']); ?>
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
 
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:flex">
                    <a href="<?php echo e(route('courses.index')); ?>" class="text-sm font-medium <?php echo e(request()->routeIs('courses*') ? 'text-accent' : 'text-ink2 hover:text-accent'); ?> transition-colors">Courses</a>
                    <a href="<?php echo e(route('pages.mentors')); ?>" class="text-sm font-medium <?php echo e(request()->routeIs('pages.mentors') ? 'text-accent' : 'text-ink2 hover:text-accent'); ?> transition-colors">Mentors</a>
                    <a href="<?php echo e(route('pages.pricing')); ?>" class="text-sm font-medium <?php echo e(request()->routeIs('pages.pricing') ? 'text-accent' : 'text-ink2 hover:text-accent'); ?> transition-colors">Pricing</a>
                    <a href="<?php echo e(route('pages.resources')); ?>" class="text-sm font-medium <?php echo e(request()->routeIs('pages.resources') ? 'text-accent' : 'text-ink2 hover:text-accent'); ?> transition-colors">Resources</a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('dashboard')); ?>" class="text-sm font-medium <?php echo e(request()->routeIs('dashboard') ? 'text-accent' : 'text-ink2 hover:text-accent'); ?> transition-colors">Dashboard</a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
 
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <?php if (isset($component)) { $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown','data' => ['align' => 'right','width' => '48']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '48']); ?>
                     <?php $__env->slot('trigger', null, []); ?> 
                        <button class="inline-flex items-center gap-2 px-4 py-2 border border-rule bg-bg text-sm font-bold text-ink hover:border-accent transition-colors">
                            <div x-data="<?php echo e(json_encode(['name' => auth()->user()->name ?? 'Guest'])); ?>" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
 
                            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-down','class' => 'w-4 h-4 text-ink3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-down','class' => 'w-4 h-4 text-ink3']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                        </button>
                     <?php $__env->endSlot(); ?>
 
                     <?php $__env->slot('content', null, []); ?> 
                        <?php if (isset($component)) { $__componentOriginal68cb1971a2b92c9735f83359058f7108 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal68cb1971a2b92c9735f83359058f7108 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-link','data' => ['href' => route('profile'),'wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-link'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('profile')),'wire:navigate' => true]); ?>
                            <?php echo e(__('Profile')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $attributes = $__attributesOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__attributesOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal68cb1971a2b92c9735f83359058f7108)): ?>
<?php $component = $__componentOriginal68cb1971a2b92c9735f83359058f7108; ?>
<?php unset($__componentOriginal68cb1971a2b92c9735f83359058f7108); ?>
<?php endif; ?>
 
                        <!-- Authentication -->
                        <form method="POST" action="<?php echo e(route('logout')); ?>" class="w-full">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-slate-700 transition duration-150 ease-in-out">
                                <?php echo e(__('Log Out')); ?>

                            </button>
                        </form>
                     <?php $__env->endSlot(); ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $attributes = $__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__attributesOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe)): ?>
<?php $component = $__componentOriginaldf8083d4a852c446488d8d384bbc7cbe; ?>
<?php unset($__componentOriginaldf8083d4a852c446488d8d384bbc7cbe); ?>
<?php endif; ?>
                <?php else: ?>
                <div class="flex items-center gap-4">
                    <a href="<?php echo e(route('login')); ?>" class="text-sm font-medium text-ink2 hover:text-accent transition-colors" wire:navigate>Log In</a>
                    <a href="<?php echo e(route('register')); ?>" class="px-4 py-2 bg-accent text-white text-sm font-bold rounded-custom hover:opacity-90 transition-opacity" wire:navigate>Sign Up</a>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-ink2 hover:text-ink hover:bg-bg focus:outline-none focus:bg-bg focus:text-ink transition duration-150 ease-in-out">
                    <span class="material-symbols-outlined text-[28px]">menu</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div x-show="open" 
         x-transition:enter="transition-opacity ease-linear duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition-opacity ease-linear duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 z-40 bg-ink/50 backdrop-blur-sm sm:hidden" 
         @click="open = false" x-cloak></div>

    <!-- Mobile Menu Flyout -->
    <div x-show="open" 
         x-transition:enter="transition ease-in-out duration-300 transform" 
         x-transition:enter-start="translate-x-full" 
         x-transition:enter-end="translate-x-0" 
         x-transition:leave="transition ease-in-out duration-300 transform" 
         x-transition:leave-start="translate-x-0" 
         x-transition:leave-end="translate-x-full" 
         class="fixed inset-y-0 right-0 z-50 w-full max-w-sm bg-surface overflow-y-auto sm:hidden flex flex-col shadow-2xl border-l border-rule" x-cloak>
        
        <div class="flex items-center justify-between px-6 h-20 border-b border-rule shrink-0">
            <div class="flex items-center gap-2">
                <?php if (isset($component)) { $__componentOriginal661589163ab40cca986dd99c9fa80b42 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal661589163ab40cca986dd99c9fa80b42 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.branding','data' => ['href' => ''.e(route('home')).'','variant' => 'split']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('branding'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('home')).'','variant' => 'split']); ?>
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
            <button @click="open = false" class="p-2 text-ink2 hover:text-ink transition-colors bg-bg rounded-lg">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="px-6 py-8 space-y-6 flex-1 bg-white">
            <div class="flex flex-col gap-5 border-b border-rule pb-8">
                <a href="<?php echo e(route('courses.index')); ?>" class="text-lg font-bold font-display <?php echo e(request()->routeIs('courses*') ? 'text-accent' : 'text-ink2 hover:text-accent'); ?> transition-colors">Courses</a>
                <a href="<?php echo e(route('pages.mentors')); ?>" class="text-lg font-bold font-display <?php echo e(request()->routeIs('pages.mentors') ? 'text-accent' : 'text-ink2 hover:text-accent'); ?> transition-colors">Mentors</a>
                <a href="<?php echo e(route('pages.pricing')); ?>" class="text-lg font-bold font-display <?php echo e(request()->routeIs('pages.pricing') ? 'text-accent' : 'text-ink2 hover:text-accent'); ?> transition-colors">Pricing</a>
                <a href="<?php echo e(route('pages.resources')); ?>" class="text-lg font-bold font-display <?php echo e(request()->routeIs('pages.resources') ? 'text-accent' : 'text-ink2 hover:text-accent'); ?> transition-colors">Resources</a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('dashboard')); ?>" class="text-lg font-bold font-display <?php echo e(request()->routeIs('dashboard') ? 'text-accent' : 'text-ink2 hover:text-accent'); ?> transition-colors">Dashboard</a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="pt-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <div class="mb-6 bg-bg p-4 rounded-xl border border-rule">
                        <div class="font-bold text-base font-display text-ink" x-data="<?php echo e(json_encode(['name' => auth()->user()->name ?? ''])); ?>" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                        <div class="font-medium text-xs text-ink3 mt-1"><?php echo e(auth()->user()->email ?? ''); ?></div>
                    </div>

                    <div class="flex flex-col gap-4">
                        <a href="<?php echo e(route('profile')); ?>" wire:navigate class="text-base font-bold font-display text-ink2 hover:text-accent transition-colors flex items-center gap-3">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                            <?php echo e(__('Profile')); ?>

                        </a>

                        <form method="POST" action="<?php echo e(route('logout')); ?>" class="w-full">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="text-left w-full text-base font-bold font-display text-red-600 hover:text-red-700 transition flex items-center gap-3">
                                <span class="material-symbols-outlined text-[20px]">logout</span>
                                <?php echo e(__('Log Out')); ?>

                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col gap-4">
                        <a href="<?php echo e(route('login')); ?>" class="py-3.5 px-4 text-center border-2 border-rule rounded-custom text-base font-bold font-display text-ink hover:border-ink transition-colors">Log In</a>
                        <a href="<?php echo e(route('register')); ?>" class="py-3.5 px-4 text-center bg-accent rounded-custom text-base font-bold font-display text-white hover:opacity-90 transition-opacity">Sign Up</a>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</nav><?php /**PATH C:\xampp\htdocs\learnflow\resources\views\livewire/layout/navigation.blade.php ENDPATH**/ ?>