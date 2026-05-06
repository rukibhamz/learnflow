

<?php $__env->startSection('title', 'Database'); ?>

<?php $__env->startSection('steps'); ?>
    <a href="<?php echo e(route('install.requirements')); ?>" class="text-slate-500 hover:text-slate-700">1. Requirements</a>
    <span class="text-slate-300">→</span>
    <span class="text-indigo-600 font-medium">2. Database</span>
    <span class="text-slate-300">→</span>
    <span class="text-slate-400">3. Application</span>
    <span class="text-slate-300">→</span>
    <span class="text-slate-400">4. Install</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <h2 class="text-xl font-semibold mb-2">Database configuration</h2>
    <p class="text-slate-600 text-sm mb-6">
        Enter your database credentials. For SQLite, only the file path is needed.
    </p>

    <form method="POST" action="<?php echo e(route('install.database.store')); ?>" id="db-form">
        <?php echo csrf_field(); ?>

        <div class="mb-4 text-left">
            <label class="block text-sm font-medium text-slate-700 mb-2">Database type</label>
            <select name="db_connection" id="db_connection"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['db_connection'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <option value="sqlite" <?php echo e(old('db_connection', 'sqlite') === 'sqlite' ? 'selected' : ''); ?>>SQLite</option>
                <option value="mysql" <?php echo e(old('db_connection') === 'mysql' ? 'selected' : ''); ?>>MySQL</option>
                <option value="mariadb" <?php echo e(old('db_connection') === 'mariadb' ? 'selected' : ''); ?>>MariaDB</option>
                <option value="pgsql" <?php echo e(old('db_connection') === 'pgsql' ? 'selected' : ''); ?>>PostgreSQL</option>
            </select>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['db_connection'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div id="sqlite-fields" class="mb-4 text-left">
            <label class="block text-sm font-medium text-slate-700 mb-2">Database file path</label>
            <input type="text" name="db_database"
                   value="<?php echo e(old('db_database', database_path('database.sqlite'))); ?>"
                   placeholder="<?php echo e(database_path('database.sqlite')); ?>"
                   class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['db_database'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['db_database'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <p class="mt-1 text-xs text-slate-500">Leave default or enter full path. File will be created if it doesn't exist.</p>
        </div>

        
        <div id="server-fields" class="space-y-4 text-left" style="display: none;">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Host</label>
                <input type="text" name="db_host" value="<?php echo e(old('db_host', '127.0.0.1')); ?>"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['db_host'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['db_host'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Port</label>
                <input type="text" name="db_port" value="<?php echo e(old('db_port', '')); ?>"
                       placeholder="<?php echo e(old('db_connection') === 'pgsql' ? '5432' : '3306'); ?>"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['db_port'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['db_port'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Database name</label>
                <input type="text" name="db_database" value="<?php echo e(old('db_database', 'learnflow')); ?>"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['db_database'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['db_database'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                <input type="text" name="db_username" value="<?php echo e(old('db_username')); ?>"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['db_username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['db_username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                <input type="password" name="db_password" value="<?php echo e(old('db_password')); ?>"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php $__errorArgs = ['db_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['db_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-xs text-red-600 font-medium"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div id="test-status" class="mt-6"></div>

        <div class="mt-8 flex gap-3">
            <a href="<?php echo e(route('install.requirements')); ?>"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-medium transition">
                Back
            </a>
            <button type="button" id="test-db-btn"
                    class="inline-flex items-center justify-center px-5 py-2 rounded-lg border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-medium text-sm transition">
                Test connection
            </button>
            <button type="submit"
                    class="inline-flex items-center justify-center px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm transition ml-auto">
                Continue
            </button>
        </div>
    </form>

    <script>
        (function() {
            var sel = document.getElementById('db_connection');
            var sqlite = document.getElementById('sqlite-fields');
            var server = document.getElementById('server-fields');
            var testBtn = document.getElementById('test-db-btn');
            var testStatus = document.getElementById('test-status');

            function toggle() {
                var isSqlite = sel.value === 'sqlite';
                sqlite.style.display = isSqlite ? 'block' : 'none';
                server.style.display = isSqlite ? 'none' : 'block';

                // Ensure hidden fields are disabled so they aren't submitted
                sqlite.querySelectorAll('input, select, textarea').forEach(el => el.disabled = !isSqlite);
                server.querySelectorAll('input, select, textarea').forEach(el => el.disabled = isSqlite);
            }
            sel.addEventListener('change', toggle);
            toggle();

            testBtn.addEventListener('click', function() {
                testStatus.innerHTML = '<div class="p-3 bg-blue-50 text-blue-700 text-xs rounded border border-blue-100 italic">Testing connection...</div>';
                
                var formData = new FormData(document.getElementById('db-form'));
                var testUrl = '<?php echo e(route("install.database.test")); ?>';
                console.log('Testing connection to:', testUrl);

                fetch(testUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error('Server returned ' + response.status + ': ' + text.substring(0, 100));
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        testStatus.innerHTML = '<div class="p-3 bg-green-50 text-green-700 text-xs rounded border border-green-100 font-medium">✔ ' + data.message + '</div>';
                    } else {
                        testStatus.innerHTML = '<div class="p-3 bg-red-50 text-red-700 text-xs rounded border border-red-100 font-medium whitespace-pre-wrap">✖ Error: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    console.error('AJAX Error:', error);
                    testStatus.innerHTML = '<div class="p-3 bg-red-50 text-red-700 text-xs rounded border border-red-100 font-medium">✖ Connection failed or server error. Check browser console for details.</div>';
                });
            });
        })();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('install.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\learnflow\resources\views/install/database.blade.php ENDPATH**/ ?>