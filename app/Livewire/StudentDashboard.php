<?php

namespace App\Livewire;

use Livewire\Component;

class StudentDashboard extends Component
{
    public function getContinueLearningProperty()
    {
        return auth()->user()->enrollments()
            ->whereNull('completed_at')
            ->with(['course.instructor'])
            ->get()
            ->map(function ($enrollment) {
                $course = $enrollment->course;
                $course->url = route('courses.show', $course->slug);
                return $course;
            });
    }

    public function getCompletedCoursesProperty()
    {
        return auth()->user()->enrollments()
            ->whereNotNull('completed_at')
            ->with(['course.instructor'])
            ->get()
            ->map(function ($enrollment) {
                $course = $enrollment->course;
                $course->url = route('courses.show', $course->slug);
                return $course;
            });
    }

    public function render()
    {
        return view('livewire.student-dashboard');
    }
}
