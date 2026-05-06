

<?php $__env->startSection('title', 'Application'); ?>

<?php $__env->startSection('steps'); ?>
    <a href="<?php echo e(route('install.requirements')); ?>" class="text-slate-500 hover:text-slate-700">1. Requirements</a>
    <span class="text-slate-300">→</span>
    <a href="<?php echo e(route('install.database')); ?>" class="text-slate-500 hover:text-slate-700">2. Database</a>
    <span class="text-slate-300">→</span>
    <span class="text-indigo-600 font-medium">3. Application</span>
    <span class="text-slate-300">→</span>
    <span class="text-slate-400">4. Install</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <h2 class="text-xl font-semibold mb-2">Application &amp; admin account</h2>
    <p class="text-slate-600 text-sm mb-6">
        Configure your application and create the administrator account.
    </p>

    <form method="POST" action="<?php echo e(route('install.application.store')); ?>">
        <?php echo csrf_field(); ?>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Application name</label>
                <input type="text" name="app_name" value="<?php echo e(old('app_name', 'LearnFlow')); ?>"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Application URL</label>
                <input type="url" name="app_url" value="<?php echo e(old('app_url', url('/'))); ?>"
                       placeholder="https://example.com"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <hr class="border-slate-200 my-6">

            <p class="text-sm font-medium text-slate-700">Administrator account</p>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Name</label>
                <input type="text" name="admin_name" value="<?php echo e(old('admin_name')); ?>"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                <input type="email" name="admin_email" value="<?php echo e(old('admin_email')); ?>"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                <input type="password" name="admin_password"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="mt-1 text-xs text-slate-500">Minimum 8 characters.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Confirm password</label>
                <input type="password" name="admin_password_confirmation"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>

        <div class="mt-8 flex gap-3">
            <a href="<?php echo e(route('install.database')); ?>"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium transition">
                Back
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition">
                Continue
            </button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('install.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\learnflow\resources\views/install/application.blade.php ENDPATH**/ ?>