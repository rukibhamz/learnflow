<?php

namespace App\Http\Controllers\Api;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::published()
            ->with(['instructor:id,name', 'media'])
            ->withCount(['enrollments', 'lessons', 'sections'])
            ->withAvg(['reviews' => fn ($q) => $q->whereNotNull('approved_at')], 'rating');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($level = $request->query('level')) {
            $query->where('level', $level);
        }

        if ($request->query('free') === 'true') {
            $query->where('price', 0);
        }

        $sort = $request->query('sort', 'newest');
        match ($sort) {
            'popular' => $query->orderByDesc('enrollments_count'),
            'rated' => $query->orderByDesc('reviews_avg_rating'),
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            default => $query->latest(),
        };

        $courses = $query->paginate($request->query('per_page', 15));

        return response()->json([
            'data' => $courses->map(fn ($c) => $this->courseListResource($c)),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $course = Course::where('slug', $slug)
            ->where('status', CourseStatus::Published)
            ->with(['instructor:id,name,bio', 'sections.lessons', 'media'])
            ->withCount(['enrollments', 'reviews' => fn ($q) => $q->whereNotNull('approved_at')])
            ->withAvg(['reviews' => fn ($q) => $q->whereNotNull('approved_at')], 'rating')
            ->firstOrFail();

        return response()->json([
            'data' => $this->courseDetailResource($course),
        ]);
    }

    protected function courseListResource(Course $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->title,
            'slug' => $course->slug,
            'short_description' => $course->short_description,
            'price' => (float) $course->price,
            'level' => $course->level?->value,
            'language' => $course->language,
            'thumbnail_url' => $course->getFirstMediaUrl('thumbnail', 'thumb') ?: null,
            'instructor' => [
                'id' => $course->instructor?->id,
                'name' => $course->instructor?->name,
            ],
            'enrollments_count' => $course->enrollments_count,
            'lessons_count' => $course->lessons_count,
            'sections_count' => $course->sections_count,
            'average_rating' => $course->reviews_avg_rating ? round($course->reviews_avg_rating, 1) : null,
        ];
    }

    protected function courseDetailResource(Course $course): array
    {
        $base = $this->courseListResource($course);

        $base['description'] = $course->description;
        $base['requirements'] = $course->requirements;
        $base['outcomes'] = $course->outcomes;
        $base['reviews_count'] = $course->reviews_count;
        $base['instructor']['bio'] = $course->instructor?->bio;

        $base['curriculum'] = $course->sections->map(fn ($section) => [
            'id' => $section->id,
            'title' => $section->title,
            'order' => $section->order,
            'lessons' => $section->lessons->map(fn ($lesson) => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'type' => $lesson->type?->value,
                'duration_seconds' => $lesson->duration_seconds,
                'is_preview' => $lesson->is_preview,
                'order' => $lesson->order,
            ]),
        ]);

        return $base;
    }
}
