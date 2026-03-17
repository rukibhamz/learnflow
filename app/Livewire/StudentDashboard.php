<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Illuminate\Support\Collection;
use Livewire\Component;

class StudentDashboard extends Component
{
    public $activeTab = 'in_progress';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function toggleWishlist($courseId)
    {
        $user = auth()->user();
        $wishlist = $user->wishlist ?? [];

        if (in_array($courseId, $wishlist)) {
            $wishlist = array_values(array_diff($wishlist, [$courseId]));
        } else {
            $wishlist[] = $courseId;
        }

        $user->update(['wishlist' => $wishlist]);
    }

    public function removeFromWishlist($courseId)
    {
        $this->toggleWishlist($courseId);
    }

    private function buildNextLessonMap(Collection $enrollments): array
    {
        $userId = auth()->id();
        if (! $userId || $enrollments->isEmpty()) {
            return [];
        }

        $courses = $enrollments->pluck('course')->filter();

        $allLessonIds = $courses->flatMap(fn ($course) => $course->lessons->pluck('id'))->unique()->values();

        $completed = $allLessonIds->isEmpty()
            ? collect()
            : LessonProgress::query()
                ->where('user_id', $userId)
                ->whereIn('lesson_id', $allLessonIds)
                ->pluck('lesson_id')
                ->flip();

        $nextByEnrollmentId = [];

        foreach ($enrollments as $enrollment) {
            $course = $enrollment->course;
            if (! $course) {
                continue;
            }

            $lessons = $course->lessons->sortBy('order');
            $next = $lessons->first(fn ($lesson) => ! $completed->has($lesson->id)) ?? $lessons->first();
            $nextByEnrollmentId[$enrollment->id] = $next;
        }

        return $nextByEnrollmentId;
    }

    public function render()
    {
        $user = auth()->user();

        $inProgressEnrollments = Enrollment::where('user_id', $user->id)
            ->whereNull('completed_at')
            ->with(['course.instructor', 'course.media', 'course.lessons'])
            ->latest('enrolled_at')
            ->get()
            ->values();

        $nextLessonIdByEnrollmentId = $this->buildNextLessonMap($inProgressEnrollments);

        // Attach next_lesson directly to each enrollment for easy access
        foreach ($inProgressEnrollments as $enrollment) {
            $enrollment->next_lesson = $nextLessonIdByEnrollmentId[$enrollment->id] ?? null;
        }

        $nextLessonIdByEnrollmentId = collect($nextLessonIdByEnrollmentId)
            ->map(fn ($lesson) => $lesson?->id)
            ->all();

        $completedEnrollments = Enrollment::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->with(['course.instructor', 'course.media'])
            ->latest('completed_at')
            ->get();

        $wishlistCourseIds = $user->wishlist ?? [];
        $wishlistCourses = Course::whereIn('id', $wishlistCourseIds)
            ->published()
            ->with(['instructor', 'media'])
            ->withCount('enrollments')
            ->get();

        return view('livewire.student-dashboard', [
            'inProgressEnrollments' => $inProgressEnrollments,
            'nextLessonIdByEnrollmentId' => $nextLessonIdByEnrollmentId,
            'completedEnrollments' => $completedEnrollments,
            'wishlistCourses' => $wishlistCourses,
        ]);
    }
}
