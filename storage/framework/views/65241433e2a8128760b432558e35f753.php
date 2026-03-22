<?php
    $brandColor = $siteColor ?? '#1a42e0';
    $hex = preg_replace('/[^a-fA-F0-9]/', '', $brandColor);
    if (strlen($hex) !== 6) {
        $hex = '1a42e0';
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
?>
<style>
:root {
    --brand-primary: <?php echo e($brandColor); ?>;
    --brand-primary-rgb: <?php echo e($r); ?>, <?php echo e($g); ?>, <?php echo e($b); ?>;
}
.text-primary, .text-accent { color: var(--brand-primary) !important; }
.bg-primary, .bg-accent { background-color: var(--brand-primary) !important; }
.border-primary, .border-accent { border-color: var(--brand-primary) !important; }
.hover\:text-primary:hover, .hover\:text-accent:hover { color: var(--brand-primary) !important; }
.hover\:bg-primary:hover, .hover\:bg-accent:hover { background-color: var(--brand-primary) !important; }
.hover\:border-primary:hover, .hover\:border-accent:hover { border-color: var(--brand-primary) !important; }
.focus\:ring-primary:focus, .focus\:ring-accent:focus { --tw-ring-color: var(--brand-primary) !important; }
.focus\:ring-primary\/20:focus, .focus\:ring-primary\/30:focus { --tw-ring-color: rgba(var(--brand-primary-rgb), 0.3) !important; }
.focus\:border-primary:focus, .focus\:border-accent:focus { border-color: var(--brand-primary) !important; }
.bg-primary\/5 { background-color: rgba(var(--brand-primary-rgb), 0.05) !important; }
.bg-accent-bg { background-color: rgba(var(--brand-primary-rgb), 0.1) !important; }
.bg-primary\/10 { background-color: rgba(var(--brand-primary-rgb), 0.1) !important; }
.bg-primary\/20 { background-color: rgba(var(--brand-primary-rgb), 0.2) !important; }
.text-primary\/40 { color: rgba(var(--brand-primary-rgb), 0.4) !important; }
.border-accent\/20 { border-color: rgba(var(--brand-primary-rgb), 0.2) !important; }
</style>
<?php /**PATH C:\xampp\htdocs\learnflow\resources\views/partials/brand-styles.blade.php ENDPATH**/ ?>