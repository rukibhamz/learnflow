<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
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

    public function getLastIncompleteLesson($enrollment)
    {
        $completedLessonIds = LessonProgress::where('user_id', $enrollment->user_id)
            ->whereIn('lesson_id', $enrollment->course->lessons()->pluck('lessons.id'))
            ->pluck('lesson_id');

        $nextLesson = $enrollment->course->lessons()
            ->whereNotIn('lessons.id', $completedLessonIds)
            ->first();

        return $nextLesson ?? $enrollment->course->lessons()->first();
    }

    public function render()
    {
        $user = auth()->user();

        $inProgressEnrollments = Enrollment::where('user_id', $user->id)
            ->whereNull('completed_at')
            ->with(['course.instructor', 'course.media', 'course.sections.lessons'])
            ->latest('enrolled_at')
            ->get()
            ->map(function ($enrollment) {
                $enrollment->next_lesson = $this->getLastIncompleteLesson($enrollment);
                return $enrollment;
            });

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
            'completedEnrollments' => $completedEnrollments,
            'wishlistCourses' => $wishlistCourses,
        ]);
    }
}
