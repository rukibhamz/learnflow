<?php

use App\Http\Controllers\InstallerController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

// Diagnostic route
Route::get('/debug-db', function() {
    return [
        'default' => config('database.default'),
        'sqlite' => config('database.connections.sqlite'),
        'mysql' => config('database.connections.mysql'),
        'env_connection' => env('DB_CONNECTION'),
        'env_database' => env('DB_DATABASE'),
    ];
});

// Installer (must be available before app is installed)
Route::middleware(['web'])->group(function () {
    Route::get('install', [InstallerController::class, 'welcome'])->name('install.welcome');
    Route::get('install/requirements', [InstallerController::class, 'requirements'])->name('install.requirements');
    Route::get('install/application', [InstallerController::class, 'application'])->name('install.application');
    Route::post('install/application', [InstallerController::class, 'storeApplication'])->name('install.application.store');
    Route::get('install/database', [InstallerController::class, 'database'])->name('install.database');
    Route::post('install/database', [InstallerController::class, 'storeDatabase'])->name('install.database.store');
    Route::post('install-database-test', [InstallerController::class, 'testDatabase'])->name('install.database.test');
    Route::get('install/run', [InstallerController::class, 'run'])->name('install.run');
    Route::post('install/run', [InstallerController::class, 'executeRun'])->name('install.run.execute');
    Route::get('install/complete', [InstallerController::class, 'complete'])->name('install.complete');
});

// Stripe webhooks (no web / CSRF middleware)
Route::post('webhooks/stripe', [StripeWebhookController::class, 'handle'])
    ->name('webhooks.stripe');

Route::middleware(['web', \App\Http\Middleware\RedirectIfNotInstalled::class])->group(function () {
    // Public routes
    Route::get('/', function () {
        $slides = \App\Models\HeroSlide::where('is_active', true)->orderBy('order')->get();
        return view('home', compact('slides'));
    })->name('home');
    Route::get('courses', fn () => view('courses.index'))->name('courses.index');
    Route::get('courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('certificates/{uuid}/verify', [CertificateController::class, 'verify'])->name('certificates.verify');
    Route::get('pricing', [SubscriptionController::class, 'pricing'])->name('pricing');

    // Blog Routes
    Route::get('blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
    Route::get('blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

    Route::view('about', 'pages.about')->name('pages.about');
    Route::view('contact', 'pages.contact')->name('pages.contact');
    Route::view('privacy', 'pages.privacy')->name('pages.privacy');
    Route::view('terms', 'pages.terms')->name('pages.terms');
    Route::view('help', 'pages.help')->name('pages.help');
    Route::view('resources', 'pages.resources')->name('pages.resources');

    Route::get('mentors', function () {
        $instructors = \App\Models\User::instructors()
            ->withCount('courses')
            ->with(['courses' => fn ($q) => $q->published()->latest()->limit(2)])
            ->get()
            ->map(function ($instructor) {
                $instructor->students_count = \App\Models\Enrollment::whereIn('course_id', $instructor->courses->pluck('id'))->distinct('user_id')->count('user_id');
                $instructor->avg_rating = \App\Models\Review::whereIn('course_id', $instructor->courses->pluck('id'))->avg('rating');
                return $instructor;
            });

        return view('pages.mentors', [
            'instructors' => $instructors,
            'totalCourses' => \App\Models\Course::published()->count(),
            'totalStudents' => \App\Models\Enrollment::distinct('user_id')->count('user_id'),
        ]);
    })->name('pages.mentors');

    Route::get('plans', function () {
        $plans = \App\Models\SubscriptionPlan::active()->get();
        return view('pages.pricing', ['plans' => $plans]);
    })->name('pages.pricing');

    require __DIR__.'/auth.php';

    // Auth required
    Route::middleware(['auth', 'verified', 'not-suspended'])->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
        Route::view('my-courses', 'student.courses')->name('my-courses');
        Route::view('student/courses', 'student.courses')->name('student.courses');
        Route::get('my-certificates', [CertificateController::class, 'index'])->name('my-certificates');
        Route::get('student/certificates', [CertificateController::class, 'index'])->name('student.certificates');
        Route::view('student/settings', 'student.settings')->name('student.settings');
        Route::get('learn/{course:slug}', fn (\App\Models\Course $course) => view('learn.show', ['course' => $course]))
            ->middleware(['enrolled', 'content-protection'])
            ->name('learn.show');
        Route::get('learn/{course:slug}/quiz/{quiz}', fn (\App\Models\Course $course, \App\Models\Quiz $quiz) => view('learn.quiz', ['course' => $course, 'quiz' => $quiz]))
            ->middleware(['enrolled', 'content-protection'])
            ->name('learn.quiz');

        // Protected media endpoints (signed, time-limited)
        Route::get('media/lesson/{lesson}/video', [\App\Http\Controllers\ProtectedMediaController::class, 'streamVideo'])
            ->middleware('content-protection')
            ->name('media.lesson.video');
        Route::get('media/lesson/{lesson}/pdf', [\App\Http\Controllers\ProtectedMediaController::class, 'streamPdf'])
            ->middleware('content-protection')
            ->name('media.lesson.pdf');
        
        // Enrolment
        Route::post('enrolments', [\App\Http\Controllers\EnrolmentController::class, 'store'])->name('enrolments.store');

        // Payments
        Route::post('checkout/{course}', [PaymentController::class, 'checkout'])->name('checkout.course');

        // Subscriptions
        Route::post('subscription/{plan}/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
        Route::get('subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
        Route::post('subscription/portal', [SubscriptionController::class, 'portal'])->name('subscription.portal');
        Route::post('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
        Route::post('subscription/resume', [SubscriptionController::class, 'resume'])->name('subscription.resume');
        Route::get('payment/success', function (\Illuminate\Http\Request $request) {
            $sessionId = $request->query('session_id');
            if (! $sessionId) {
                return redirect()->route('dashboard');
            }

            $order = \App\Models\Order::where('stripe_session_id', $sessionId)
                ->with('course')
                ->first();

            if (! $order) {
                return redirect()->route('dashboard');
            }

            // Clear coupon
            $request->session()->forget('coupon_code');

            return view('payment.success', ['order' => $order]);
        })->name('payment.success');

        Route::get('my-orders', function () {
            $orders = \App\Models\Order::where('user_id', auth()->id())
                ->with('course')
                ->latest()
                ->get();

            return view('student.orders', ['orders' => $orders]);
        })->name('my-orders');

        Route::get('my-orders/{order}/invoice', function (\App\Models\Order $order) {
            abort_unless($order->user_id === auth()->id(), 403);
            $meta = $order->metadata ?? [];
            $path = $meta['invoice_path'] ?? null;
            abort_unless($path && \Illuminate\Support\Facades\Storage::disk('local')->exists($path), 404);
            return \Illuminate\Support\Facades\Response::file(storage_path('app/'.$path), [
                'Content-Type' => 'application/pdf',
            ]);
        })->name('my-orders.invoice');

        Route::get('certificates/{uuid}/download', [CertificateController::class, 'download'])
            ->name('certificates.download');

        // Push notification subscription
        Route::post('push/subscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'store'])->name('push.subscribe');
        Route::post('push/unsubscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
        Route::get('push/vapid-key', [\App\Http\Controllers\PushSubscriptionController::class, 'vapidKey'])->name('push.vapid-key');
    });

    // Instructor
    Route::middleware(['auth', 'verified', 'not-suspended', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
        Route::view('dashboard', 'instructor.dashboard')->name('dashboard');
        Route::get('courses', fn () => view('instructor.courses.index'))->name('courses.index');
        Route::get('courses/create', fn () => view('instructor.courses.create'))->name('courses.create');
        Route::get('courses/{course}/edit', fn (\App\Models\Course $course) => view('instructor.courses.edit', ['course' => $course]))->name('courses.edit');
        Route::get('courses/{course}/curriculum', fn (\App\Models\Course $course) => view('instructor.courses.curriculum', ['course' => $course]))->name('courses.curriculum');
        Route::get('lessons/{lesson}/edit', fn (\App\Models\Lesson $lesson) => view('instructor.lessons.edit', ['lesson' => $lesson]))->name('lessons.edit');
        Route::get('lessons/{lesson}/quiz', fn (\App\Models\Lesson $lesson) => view('instructor.quizzes.builder', ['lesson' => $lesson]))->name('lessons.quiz');
        Route::get('courses/{course}/question-bank', fn (\App\Models\Course $course) => view('instructor.question-bank', ['course' => $course]))->name('courses.question-bank');
        Route::get('earnings', fn () => view('instructor.earnings'))->name('earnings');
    });

    // Admin
    Route::middleware(['auth', 'verified', 'not-suspended', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::view('dashboard', 'admin.dashboard')->name('dashboard');
        Route::get('courses', fn () => view('admin.courses.index'))->name('courses.index');
        Route::get('courses/create', fn () => view('admin.courses.create'))->name('courses.create');
        Route::get('courses/review', fn () => view('admin.courses.review'))->name('courses.review');
        Route::get('courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'show'])->name('courses.show');
        Route::get('courses/{course}/edit', fn (\App\Models\Course $course) => view('admin.courses.edit', ['course' => $course]))->name('courses.edit');
        Route::get('courses/{course}/curriculum', fn (\App\Models\Course $course) => view('admin.courses.curriculum', ['course' => $course]))->name('courses.curriculum');
        Route::get('lessons/{lesson}/edit', fn (\App\Models\Lesson $lesson) => view('admin.lessons.edit', ['lesson' => $lesson]))->name('lessons.edit');
        Route::get('lessons/{lesson}/quiz', fn (\App\Models\Lesson $lesson) => view('admin.quizzes.builder', ['lesson' => $lesson]))->name('lessons.quiz');
        Route::patch('courses/{course}/status', [\App\Http\Controllers\Admin\CourseController::class, 'updateStatus'])->name('courses.update-status');
        Route::delete('courses/{course}', [\App\Http\Controllers\Admin\CourseController::class, 'destroy'])->name('courses.destroy');
        Route::get('users', fn () => view('admin.users.index'))->name('users.index');
        Route::get('coupons', fn () => view('admin.coupons.index'))->name('coupons.index');
        Route::get('orders', fn () => view('admin.orders.index'))->name('orders.index');
        Route::get('search-analytics', fn () => view('admin.search-analytics'))->name('search-analytics');
        Route::get('certificate-templates', fn () => view('admin.certificate-templates'))->name('certificate-templates');
        Route::get('payouts', fn () => view('admin.payouts'))->name('payouts');
        Route::get('analytics/export/{type}', [\App\Http\Controllers\Admin\AnalyticsExportController::class, 'export'])->name('analytics.export');
        Route::get('settings', fn () => view('admin.settings'))->name('settings');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/test-email', [\App\Http\Controllers\Admin\SettingsController::class, 'sendTestEmail'])->name('settings.test-email');

        // Course Categories
        Route::get('categories', fn () => view('admin.categories.index'))->name('categories.index');
        Route::get('categories/create', fn () => view('admin.categories.create'))->name('categories.create');
        Route::get('categories/{category}/edit', function (\App\Models\Category $category) {
            return view('admin.categories.edit', ['category' => $category]);
        })->name('categories.edit');
        
        // Blog CMS
        Route::get('blogs', fn () => view('admin.blogs.index'))->name('blogs.index');
        Route::get('blogs/create', fn () => view('admin.blogs.create'))->name('blogs.create');
        Route::get('blogs/{id}/edit', fn (string $id) => view('admin.blogs.edit', ['id' => $id]))->name('blogs.edit');

        // Hero CMS
        Route::get('hero', fn () => view('admin.hero.index'))->name('hero.index');
    });

    Route::middleware(['auth'])->group(function () {
        Route::view('profile', 'profile')->name('profile');
    });
});
