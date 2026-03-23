<div x-data="{ open: false }" class="relative">
    <button
        type="button"
        @click="open = !open"
        class="p-2 text-ink2 hover:text-ink transition-colors duration-150 rounded-card focus:outline-none focus-visible:ring-2 focus-visible:ring-accent"
        aria-label="Notifications"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
            <span class="absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center bg-accent text-white font-display font-bold text-[11px] rounded-full"><?php echo e($unreadCount); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        @click.outside="open = false"
        class="absolute right-0 top-full mt-2 w-[320px] bg-surface border border-rule rounded-card z-50 overflow-hidden shadow-lg"
    >
        <div class="p-3 border-b border-rule flex items-center justify-between">
            <span class="font-display font-bold text-xs text-ink uppercase tracking-wider">Notifications</span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                <button wire:click="markAllRead" class="text-primary text-[11px] font-medium hover:underline">Mark all read</button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="max-h-[360px] overflow-y-auto">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div
                    wire:key="notif-<?php echo e($notification->id); ?>"
                    wire:click="markAsRead('<?php echo e($notification->id); ?>')"
                    class="px-4 py-3 flex items-start gap-3 border-b border-rule last:border-0 cursor-pointer hover:bg-bg transition-colors <?php echo e($notification->read_at ? '' : 'bg-primary/5'); ?>"
                >
                    <span class="material-symbols-outlined text-[20px] mt-0.5 <?php echo e($notification->read_at ? 'text-ink3' : 'text-primary'); ?>">
                        <?php echo e($notification->data['icon'] ?? 'notifications'); ?>

                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] text-ink leading-snug <?php echo e($notification->read_at ? '' : 'font-medium'); ?>">
                            <?php echo e($notification->data['message'] ?? 'New notification'); ?>

                        </p>
                        <span class="text-[10px] text-ink3 mt-1 block"><?php echo e($notification->created_at->diffForHumans()); ?></span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $notification->read_at): ?>
                        <span class="w-2 h-2 bg-primary rounded-full mt-1.5 shrink-0"></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="px-4 py-8 text-center text-ink3 text-sm">
                    No notifications yet.
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\learnflow\resources\views/livewire/notification-bell.blade.php ENDPATH**/ ?>