<?php

namespace App\Http\Middleware;

use App\Models\Enrollment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEnrolled
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $course = $request->route('course');

        if (! $user || ! $course) {
            abort(403, 'Access denied.');
        }

        if ($user->hasRole('admin')) {
            return $next($request);
        }

        if ($course->instructor_id === $user->id) {
            return $next($request);
        }

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (! $enrollment) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'You must be enrolled to access this course.');
        }

        if ($enrollment->expires_at && $enrollment->expires_at->isPast()) {
            return redirect()->route('courses.show', $course->slug)
                ->with('error', 'Your enrollment has expired. Please re-enrol to continue.');
        }

        return $next($request);
    }
}
