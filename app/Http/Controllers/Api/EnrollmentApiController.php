<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Services\EnrolmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentApiController extends Controller
{
    public function __construct(
        protected EnrolmentService $enrolmentService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $enrollments = Enrollment::where('user_id', $request->user()->id)
            ->with(['course:id,title,slug', 'course.media'])
            ->latest('enrolled_at')
            ->get();

        return response()->json([
            'data' => $enrollments->map(fn ($e) => [
                'id' => $e->id,
                'course' => [
                    'id' => $e->course->id,
                    'title' => $e->course->title,
                    'slug' => $e->course->slug,
                    'thumbnail_url' => $e->course->getFirstMediaUrl('thumbnail', 'thumb') ?: null,
                ],
                'progress_percentage' => $e->progress_percentage,
                'enrolled_at' => $e->enrolled_at?->toISOString(),
                'completed_at' => $e->completed_at?->toISOString(),
                'expires_at' => $e->expires_at?->toISOString(),
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($request->course_id);

        if ($course->price > 0) {
            return response()->json([
                'message' => 'Paid courses must be purchased via checkout.',
            ], 422);
        }

        try {
            $enrollment = $this->enrolmentService->enrol($request->user(), $course);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Enrolled successfully.',
            'enrollment_id' => $enrollment->id,
        ], 201);
    }

    public function progress(Request $request, string $courseSlug): JsonResponse
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();

        $enrollment = Enrollment::where('user_id', $request->user()->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        $completedLessonIds = LessonProgress::where('user_id', $request->user()->id)
            ->whereIn('lesson_id', $course->lessons()->pluck('lessons.id'))
            ->pluck('lesson_id');

        return response()->json([
            'enrollment_id' => $enrollment->id,
            'progress_percentage' => $enrollment->progress_percentage,
            'completed_at' => $enrollment->completed_at?->toISOString(),
            'completed_lesson_ids' => $completedLessonIds,
        ]);
    }
}
