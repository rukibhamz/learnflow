<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CertificateApiController;
use App\Http\Controllers\Api\CourseApiController;
use App\Http\Controllers\Api\EnrollmentApiController;
use App\Http\Controllers\Api\NotificationApiController;
use Illuminate\Support\Facades\Route;

// Public (rate-limited)
Route::middleware('throttle:60,1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);

    Route::get('courses', [CourseApiController::class, 'index']);
    Route::get('courses/{slug}', [CourseApiController::class, 'show']);

    Route::get('certificates/{uuid}/verify', [CertificateApiController::class, 'verify']);
});

// Authenticated (rate-limited)
Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::get('enrollments', [EnrollmentApiController::class, 'index']);
    Route::post('enrollments', [EnrollmentApiController::class, 'store']);
    Route::get('enrollments/{courseSlug}/progress', [EnrollmentApiController::class, 'progress']);

    Route::get('certificates', [CertificateApiController::class, 'index']);

    Route::get('notifications', [NotificationApiController::class, 'index']);
    Route::post('notifications/{id}/read', [NotificationApiController::class, 'markAsRead']);
    Route::post('notifications/read-all', [NotificationApiController::class, 'markAllRead']);
});
