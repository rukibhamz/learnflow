<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $course = Course::query()
            ->where('slug', $slug)
            ->published()
            ->with([
                'instructor',
                'media',
                'reviews' => fn ($q) => $q->whereNotNull('approved_at')
                    ->with('user')
                    ->latest()
                    ->take(10),
            ])
            ->withCount([
                'enrollments',
                'reviews' => fn ($q) => $q->whereNotNull('approved_at'),
            ])
            ->withAvg(['reviews' => fn ($q) => $q->whereNotNull('approved_at')], 'rating')
            ->firstOrFail();

        $curriculum = $course->cachedCurriculum();
        $sections = collect($curriculum['sections'] ?? []);

        $totalLessons = (int) $sections->sum(fn ($s) => count($s['lessons'] ?? []));
        $totalDuration = (int) $sections->sum(function ($s) {
            return collect($s['lessons'] ?? [])->sum(fn ($l) => (int) ($l['duration_seconds'] ?? 0));
        });

        $isEnrolled = $request->user()
            ? Enrollment::query()
                ->where('user_id', $request->user()->id)
                ->where('course_id', $course->id)
                ->exists()
            : false;

        $instructorCourseCount = Course::query()
            ->published()
            ->where('instructor_id', $course->instructor_id)
            ->count();

        $instructorStudentCount = Enrollment::query()
            ->join('courses', 'courses.id', '=', 'enrollments.course_id')
            ->where('courses.status', \App\Enums\CourseStatus::Published)
            ->where('courses.instructor_id', $course->instructor_id)
            ->count();

        $prerequisites = $course->prerequisites();
        $prerequisitesMet = $request->user()
            ? $course->prerequisitesMet($request->user())
            : true;

        return view('courses.show', [
            'course' => $course,
            'curriculumSections' => $sections,
            'isEnrolled' => $isEnrolled,
            'totalDuration' => $totalDuration,
            'totalLessons' => $totalLessons,
            'instructorCourseCount' => $instructorCourseCount,
            'instructorStudentCount' => $instructorStudentCount,
            'prerequisites' => $prerequisites,
            'prerequisitesMet' => $prerequisitesMet,
        ]);
    }
}

