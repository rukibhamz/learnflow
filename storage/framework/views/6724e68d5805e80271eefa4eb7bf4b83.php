

<?php $__env->startSection('title', 'Requirements'); ?>
<?php
    $backHref = route('install.welcome');
    $continueHref = route('install.database');
    \Illuminate\Support\Facades\Log::info('[INSTALL_DEBUG] requirements view rendering', [
        'back_href' => $backHref,
        'continue_href' => $continueHref,
        'current_url' => request()->url(),
    ]);
?>

<?php $__env->startSection('steps'); ?>
    <span class="text-indigo-600 font-medium">1. Requirements</span>
    <span class="text-slate-300">→</span>
    <span class="text-slate-400">2. Database</span>
    <span class="text-slate-300">→</span>
    <span class="text-slate-400">3. Application</span>
    <span class="text-slate-300">→</span>
    <span class="text-slate-400">4. Install</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <h2 class="text-xl font-semibold mb-2">System requirements</h2>
    <p class="text-slate-600 text-sm mb-6">
        Before we begin, we need to verify your server meets the requirements.
    </p>

    <div class="space-y-2">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $requirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center justify-between py-2 px-3 rounded-lg <?php echo e($req['satisfied'] ? 'bg-slate-50' : 'bg-red-50'); ?>">
                <span class="text-sm <?php echo e($req['satisfied'] ? 'text-slate-700' : 'text-red-700'); ?>">
                    <?php echo e($req['label']); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($req['message'])): ?>
                        <span class="text-slate-500">(<?php echo e($req['message']); ?>)</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($req['satisfied']): ?>
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                <?php else: ?>
                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="mt-8 flex gap-3">
        <a href="<?php echo e($backHref); ?>"
           class="inline-flex items-center px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium transition">
            Back
        </a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($satisfied): ?>
            <a href="<?php echo e($continueHref); ?>"
               class="inline-flex items-center justify-center px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition">
                Continue
            </a>
        <?php else: ?>
            <button type="button" disabled
                    class="inline-flex items-center px-5 py-2 rounded-lg bg-slate-300 text-slate-500 font-medium text-sm cursor-not-allowed">
                Fix requirements to continue
            </button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('install.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\learnflow\resources\views/install/requirements.blade.php ENDPATH**/ ?>