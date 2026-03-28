<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['course']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['course']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $abbr = strtoupper(substr($course->title ?? 'C', 0, 2));
    $colors = ['#EEF1FF', '#E8F5EE', '#FFF8E6', '#F5F4F0'];
    $textColors = ['#1A43E0', '#1B7A3E', '#96650A', '#5A5A56'];
    $colorIndex = ($course->id ?? 0) % 4;
?>

<a href="<?php echo e($course->url ?? '#'); ?>" class="group block bg-surface border border-rule transition-all duration-150 hover:border-ink rounded-card overflow-hidden">
    <div class="h-[110px] w-full flex items-center justify-center font-display font-bold text-2xl" 
         style="background-color: <?php echo e($colors[$colorIndex]); ?>; color: <?php echo e($textColors[$colorIndex]); ?>;">
        <?php echo e($abbr); ?>

    </div>
    <div class="p-4">
        <h3 class="font-display font-bold text-[13px] text-ink line-clamp-2 leading-tight mb-2 group-hover:text-accent transition-colors"><?php echo e($course->title ?? 'Course Title'); ?></h3>
        
        <div class="flex items-center gap-1.5 mb-2">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'star-filled','class' => 'w-4 h-4 text-amber-500 shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'star-filled','class' => 'w-4 h-4 text-amber-500 shrink-0']); ?>
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
            <span class="text-xs text-ink3 font-body"><?php echo e(number_format($course->rating ?? 4.8, 1)); ?></span>
            <span class="text-[11px] text-ink3 font-body">(<?php echo e($course->reviews_count ?? 120); ?>)</span>
        </div>

        <p class="text-[11px] text-ink3 font-body truncate mb-3"><?php echo e($course->instructor->name ?? 'Instructor Name'); ?></p>

        <div class="flex items-center justify-between">
            <span class="font-display font-bold text-sm <?php echo e(($course->price ?? 0) == 0 ? 'text-success' : 'text-ink'); ?>">
                <?php echo e(format_price($course->price ?? 19)); ?>

            </span>
        </div>
    </div>
</a>
<?php /**PATH C:\xampp\htdocs\learnflow\resources\views/components/course-card.blade.php ENDPATH**/ ?>