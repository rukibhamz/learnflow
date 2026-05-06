<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Install'); ?> – LearnFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg: '#F5F4F0',
                        surface: '#FFFFFF',
                        ink: '#0E0E0C',
                        ink2: '#5A5A56',
                        rule: '#E0DFD8',
                        accent: '#1A43E0',
                    },
                    fontFamily: {
                        display: ['Poppins', 'sans-serif'],
                        body: ['Poppins', 'DM Sans', 'sans-serif'],
                    },
                    borderRadius: {
                        card: '6px',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-bg font-body text-ink antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6">
        
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-display font-extrabold text-ink tracking-tight">
                Learn<span class="text-accent">Flow</span>
            </h1>
            <p class="text-[13px] text-ink2 font-medium mt-2 uppercase tracking-widest opacity-70">Installation Wizard</p>
        </div>

        
        <?php if (! empty(trim($__env->yieldContent('steps')))): ?>
        <nav class="mb-8 flex items-center gap-3 text-[12px] font-bold uppercase tracking-wider text-ink3" aria-label="Installation progress">
            <?php echo $__env->yieldContent('steps'); ?>
        </nav>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="w-full max-w-lg bg-surface/80 backdrop-blur-xl rounded-card shadow-sm border border-rule overflow-hidden">
            <div class="p-10 text-center">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                    <div class="mb-6 p-4 rounded-card bg-red-50 border border-red-100 text-red-600 text-[13px] font-medium leading-relaxed">
                        <ul class="list-disc list-inside space-y-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>

        <p class="mt-8 text-xs text-slate-400">LearnFlow &copy; <?php echo e(date('Y')); ?></p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\learnflow\resources\views/install/layout.blade.php ENDPATH**/ ?>