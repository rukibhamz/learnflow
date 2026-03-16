<?php

use App\Http\Controllers\InstallerController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

// Installer (must be available before app is installed)
Route::middleware(['web'])->group(function () {
    Route::get('install', [InstallerController::class, 'welcome'])->name('install.welcome');
    Route::get('install/requirements', [InstallerController::class, 'requirements'])->name('install.requirements');
    Route::get('install/application', [InstallerController::class, 'application'])->name('install.application');
    Route::post('install/application', [InstallerController::class, 'storeApplication'])->name('install.application.store');
    Route::get('install/database', [InstallerController::class, 'database'])->name('install.database');
    Route::post('install/database', [InstallerController::class, 'storeDatabase'])->name('install.database.store');
    Route::get('install/run', [InstallerController::class, 'run'])->name('install.run');
    Route::post('install/run', [InstallerController::class, 'executeRun'])->name('install.run.execute');
    Route::get('install/complete', [InstallerController::class, 'complete'])->name('install.complete');
});

// Stripe webhooks (no web / CSRF middleware)
Route::post('webhooks/stripe', [StripeWebhookController::class, 'handle'])
    ->name('webhooks.stripe');

Route::middleware(['web', \App\Http\Middleware\RedirectIfNotInstalled::class])->group(function () {
    // Public routes
    Route::view('/', 'home')->name('home');
    Route::get('courses', fn () => view('courses.index'))->name('courses.index');
    Route::get('courses/{slug}', fn () => view('courses.show', ['slug' => request()->route('slug')]))->name('courses.show');
    Route::get('certificates/{uuid}/verify', [CertificateController::class, 'verify'])->name('certificates.verify');

    require __DIR__.'/auth.php';

    // Auth required
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
        Route::view('my-courses', 'student.courses')->name('my-courses');
        Route::view('student/courses', 'student.courses')->name('student.courses');
        Route::get('my-certificates', [CertificateController::class, 'index'])->name('my-certificates');
        Route::get('student/certificates', [CertificateController::class, 'index'])->name('student.certificates');
        Route::view('student/settings', 'student.settings')->name('student.settings');
        Route::get('learn/{course:slug}', fn ($course) => view('learn.show', ['course' => $course]))->name('learn.show');
        
        // Enrolment
        Route::post('enrolments', [\App\Http\Controllers\EnrolmentController::class, 'store'])->name('enrolments.store');

        // Payments
        Route::post('checkout/{course}', [PaymentController::class, 'checkout'])->name('checkout.course');
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
    });

    // Instructor
    Route::middleware(['auth', 'verified', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
        Route::view('dashboard', 'instructor.dashboard')->name('dashboard');
        Route::get('courses', fn () => view('instructor.courses.index'))->name('courses.index');
        Route::get('courses/create', fn () => view('instructor.courses.create'))->name('courses.create');
        Route::get('courses/{course}/edit', fn (\App\Models\Course $course) => view('instructor.courses.edit', ['course' => $course]))->name('courses.edit');
        Route::get('courses/{course}/curriculum', fn (\App\Models\Course $course) => view('instructor.courses.curriculum', ['course' => $course]))->name('courses.curriculum');
        Route::get('lessons/{lesson}/edit', fn (\App\Models\Lesson $lesson) => view('instructor.lessons.edit', ['lesson' => $lesson]))->name('lessons.edit');
    });

    // Admin
    Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::view('dashboard', 'admin.dashboard')->name('dashboard');
        Route::get('courses', fn () => view('admin.courses.index'))->name('courses.index');
        Route::get('courses/review', fn () => view('admin.courses.review'))->name('courses.review');
        Route::get('users', fn () => view('admin.users.index'))->name('users.index');
        Route::get('coupons', fn () => view('admin.coupons.index'))->name('coupons.index');
        Route::get('settings', fn () => view('admin.settings'))->name('settings');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/test-email', [\App\Http\Controllers\Admin\SettingsController::class, 'sendTestEmail'])->name('settings.test-email');
    });

    Route::middleware(['auth'])->group(function () {
        Route::view('profile', 'profile')->name('profile');
    });
});
