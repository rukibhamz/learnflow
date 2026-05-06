

<?php $__env->startSection('content'); ?>
    <div class="text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 mb-6">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
        </div>
        <h2 class="text-xl font-semibold mb-2">Installation complete!</h2>
        <p class="text-slate-600 text-sm mb-6">
            LearnFlow has been successfully installed. Your administrator account has been created.
        </p>

        <div class="flex flex-wrap justify-center gap-3">
            <a href="<?php echo e(route('home')); ?>"
               class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition">
                Go to application
            </a>
            <a href="<?php echo e(route('login')); ?>"
               class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-medium text-sm transition">
                Admin login
            </a>
        </div>

        <p class="mt-6 text-xs text-slate-500">
            The installer is now locked. To reinstall, remove <code class="px-1 py-0.5 rounded bg-slate-200">storage/framework/installed</code>.
        </p>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('install.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\learnflow\resources\views/install/complete.blade.php ENDPATH**/ ?>