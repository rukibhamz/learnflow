<?php

namespace App\Listeners;

use App\Events\CourseCompleted;
use App\Events\LessonCompleted;
use App\Models\Enrollment;
use App\Models\LessonProgress;

class CheckCourseCompletion
{
    public function __invoke(LessonCompleted $event): void
    {
        $course = $event->lesson->section->course;

        $enrollment = Enrollment::where('user_id', $event->user->id)
            ->where('course_id', $course->id)
            ->first();

        if (! $enrollment) {
            return;
        }

        if ($enrollment->completed_at) {
            return;
        }

        $lessonIds = $course->lessons()->pluck('lessons.id');
        if ($lessonIds->count() === 0) {
            return;
        }

        $completedCount = LessonProgress::where('user_id', $event->user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->count();

        if ($completedCount === $lessonIds->count()) {
            $enrollment->update(['completed_at' => $event->completedAt]);
            event(new CourseCompleted($event->user, $course, $event->completedAt));
        }
    }
}

