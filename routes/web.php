<?php

use App\Http\Controllers\InstallerController;
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

Route::middleware(['web', \App\Http\Middleware\RedirectIfNotInstalled::class])->group(function () {
    // Public routes
    Route::view('/', 'home')->name('home');
    Route::get('courses', fn () => view('courses.index'))->name('courses.index');
    Route::get('courses/{slug}', fn () => view('courses.show', ['slug' => request()->route('slug')]))->name('courses.show');
    Route::get('certificates/{uuid}/verify', fn () => view('certificates.verify'))->name('certificates.verify');

    require __DIR__.'/auth.php';

    // Auth required
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
        Route::view('my-courses', 'student.courses')->name('my-courses');
        Route::view('student/courses', 'student.courses')->name('student.courses');
        Route::view('student/certificates', 'student.certificates')->name('student.certificates');
        Route::view('student/settings', 'student.settings')->name('student.settings');
        Route::get('learn/{course:slug}', fn ($course) => view('learn.show', ['course' => $course]))->name('learn.show');
    });

    // Instructor
    Route::middleware(['auth', 'verified', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
        Route::view('dashboard', 'instructor.dashboard')->name('dashboard');
        Route::get('courses', fn () => view('instructor.courses.index'))->name('courses.index');
    });

    // Admin
    Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::view('dashboard', 'admin.dashboard')->name('dashboard');
        Route::get('courses', fn () => view('admin.courses.index'))->name('courses.index');
        Route::get('users', fn () => view('admin.users.index'))->name('users.index');
        Route::get('settings', fn () => view('admin.settings'))->name('settings');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/test-email', [\App\Http\Controllers\Admin\SettingsController::class, 'sendTestEmail'])->name('settings.test-email');
    });

    Route::middleware(['auth'])->group(function () {
        Route::view('profile', 'profile')->name('profile');
    });
});
