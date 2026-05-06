

<?php $__env->startSection('title', 'Overview'); ?>

<?php $__env->startSection('content'); ?>
<?php
    use App\Models\Order;
    use App\Models\User;
    use App\Models\Enrollment;
    use App\Models\Course;
    use Illuminate\Support\Carbon;

    // KPI values
    $totalRevenue  = Order::paid()->sum('amount') ?? 0;
    $activeStudents = User::role('student')->count();
    $enrolmentsToday = Enrollment::whereDate('created_at', today())->count();

    // Revenue chart — last 30 days grouped by day
    $revenueData = Order::paid()
        ->where('created_at', '>=', now()->subDays(29))
        ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as day, SUM(amount) as total")
        ->groupBy('day')
        ->orderBy('day')
        ->pluck('total', 'day');

    $revenueDays = collect();
    for ($i = 29; $i >= 0; $i--) {
        $day = now()->subDays($i)->format('Y-m-d');
        $revenueDays[$day] = $revenueData[$day] ?? 0;
    }
    $revenueLabels = $revenueDays->keys()->map(fn($d) => Carbon::parse($d)->format('M j'))->values();
    $revenueValues = $revenueDays->values();

    // Enrolments chart — last 12 weeks
    $enrolData = Enrollment::where('created_at', '>=', now()->subWeeks(11)->startOfWeek())
        ->selectRaw("DATE_FORMAT(created_at, '%x-%v') as week, COUNT(*) as total")
        ->groupBy('week')
        ->orderBy('week')
        ->pluck('total', 'week');

    $enrolWeeks = collect();
    for ($i = 11; $i >= 0; $i--) {
        $key = now()->subWeeks($i)->format('Y-W');
        $label = 'Wk ' . now()->subWeeks($i)->format('W');
        $enrolWeeks[$label] = $enrolData[$key] ?? 0;
    }
    $enrolLabels = $enrolWeeks->keys()->values();
    $enrolValues = $enrolWeeks->values();

    // Top courses
    $topCourses = Course::withCount('enrollments')
        ->withSum(['orders' => fn($q) => $q->where('status', 'paid')], 'amount')
        ->orderBy('enrollments_count', 'desc')
        ->take(5)
        ->get();

    // Recent orders
    $recentOrders = Order::with('user', 'course')->latest()->take(8)->get();
?>

<div class="space-y-8">

    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-poppins font-bold text-2xl tracking-tight text-ink">Overview</h1>
            <p class="text-sm text-ink3 mt-1">Welcome back, <?php echo e(auth()->user()->name); ?>. Here's what's happening.</p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.analytics.export', ['type' => 'revenue', 'days' => 90])); ?>" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium border border-rule rounded-lg hover:bg-bg transition-colors">
                <span class="material-symbols-outlined text-[16px]">download</span> Revenue CSV
            </a>
            <a href="<?php echo e(route('admin.analytics.export', ['type' => 'enrollments', 'days' => 90])); ?>" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium border border-rule rounded-lg hover:bg-bg transition-colors">
                <span class="material-symbols-outlined text-[16px]">download</span> Enrollments CSV
            </a>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-surface border border-rule p-6">
            <p class="text-[10px] font-poppins font-bold uppercase tracking-widest text-ink3 mb-2">Total Revenue</p>
            <p class="font-poppins font-extrabold text-2xl tracking-tight text-ink"><?php echo e(format_price($totalRevenue)); ?></p>
            <p class="text-[10px] mt-2 font-bold text-green-600 uppercase tracking-tighter">All time · paid orders</p>
        </div>
        <div class="bg-surface border border-rule p-6">
            <p class="text-[10px] font-poppins font-bold uppercase tracking-widest text-ink3 mb-2">Active Students</p>
            <p class="font-poppins font-extrabold text-2xl tracking-tight text-ink"><?php echo e(number_format($activeStudents)); ?></p>
            <p class="text-[10px] mt-2 font-bold text-ink3 uppercase tracking-tighter">Registered accounts</p>
        </div>
        <div class="bg-surface border border-rule p-6">
            <p class="text-[10px] font-poppins font-bold uppercase tracking-widest text-ink3 mb-2">Enrolments Today</p>
            <p class="font-poppins font-extrabold text-2xl tracking-tight text-ink"><?php echo e(number_format($enrolmentsToday)); ?></p>
            <p class="text-[10px] mt-2 font-bold text-ink3 uppercase tracking-tighter">Since midnight</p>
        </div>
    </div>

    
    <div class="grid grid-cols-12 gap-6">

        
        <div class="col-span-12 lg:col-span-8 bg-surface border border-rule p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="font-poppins font-bold text-base tracking-tight text-ink">Revenue</h2>
                    <p class="text-[11px] text-ink3 mt-0.5">Last 30 days · paid orders</p>
                </div>
                <span class="text-[11px] font-poppins font-bold text-primary"><?php echo e(format_price($revenueValues->sum())); ?></span>
            </div>
            <div class="h-[220px]">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        
        <div class="col-span-12 lg:col-span-4 bg-surface border border-rule p-6">
            <div class="mb-6">
                <h2 class="font-poppins font-bold text-base tracking-tight text-ink">Enrolments</h2>
                <p class="text-[11px] text-ink3 mt-0.5">Last 12 weeks</p>
            </div>
            <div class="h-[220px]">
                <canvas id="enrolmentsChart"></canvas>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-12 gap-6">

        
        <div class="col-span-12 lg:col-span-8 bg-surface border border-rule overflow-hidden">
            <div class="px-6 py-4 border-b border-rule flex items-center justify-between">
                <h2 class="font-poppins font-bold text-base tracking-tight text-ink">Top Performing Courses</h2>
                <a href="<?php echo e(route('admin.courses.index')); ?>" class="text-[10px] font-bold uppercase tracking-widest text-primary hover:opacity-75 transition-opacity">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-background-light text-[10px] font-poppins font-bold uppercase tracking-widest text-ink3">
                        <tr>
                            <th class="px-6 py-3">Course</th>
                            <th class="px-6 py-3">Instructor</th>
                            <th class="px-6 py-3 text-right">Students</th>
                            <th class="px-6 py-3 text-right">Earnings</th>
                        </tr>
                    </thead>
                    <tbody class="text-[13px]">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $topCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-t border-rule hover:bg-background-light/40 transition-colors">
                            <td class="px-6 py-3 font-semibold text-ink"><?php echo e($course->title); ?></td>
                            <td class="px-6 py-3 text-ink2"><?php echo e($course->instructor->name ?? '—'); ?></td>
                            <td class="px-6 py-3 text-right text-ink2"><?php echo e(number_format($course->enrollments_count)); ?></td>
                            <td class="px-6 py-3 text-right font-poppins font-bold text-primary"><?php echo e(format_price($course->orders_sum_amount ?? 0)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-ink3">No courses yet.</td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="col-span-12 lg:col-span-4 bg-surface border border-rule p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-poppins font-bold text-base tracking-tight text-ink">Recent Orders</h2>
                <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-ink3">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                    Live
                </span>
            </div>
            <div class="space-y-5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="relative pl-5 border-l-2 <?php echo e($loop->first ? 'border-primary' : 'border-rule'); ?>">
                    <p class="text-[13px] font-semibold text-ink leading-tight"><?php echo e($order->user->name ?? 'Guest'); ?></p>
                    <p class="text-[11px] text-ink2 mt-0.5 truncate"><?php echo e(optional($order->course)->title ?? '—'); ?></p>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-[11px] font-poppins font-bold text-primary"><?php echo e(format_price($order->amount)); ?></span>
                        <span class="text-[10px] text-ink3"><?php echo e($order->created_at->diffForHumans(null, true)); ?></span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-sm text-ink3 text-center py-6">No orders yet.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initRevenueChart('revenueChart',
            <?php echo $revenueLabels->toJson(); ?>,
            <?php echo $revenueValues->toJson(); ?>,
            <?php echo json_encode(currency_symbol()); ?>

        );
        initEnrolmentsChart('enrolmentsChart',
            <?php echo $enrolLabels->toJson(); ?>,
            <?php echo $enrolValues->toJson(); ?>

        );
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\learnflow\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>