<?php

namespace App\Livewire;

use App\Enums\CourseStatus;
use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;

class AdminCourseReview extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function approveCourse($courseId)
    {
        $course = Course::findOrFail($courseId);
        $this->authorize('update', $course);

        $course->update(['status' => CourseStatus::Published]);
        session()->flash('success', 'Course "' . $course->title . '" has been approved and published.');
    }

    public function rejectCourse($courseId)
    {
        $course = Course::findOrFail($courseId);
        $this->authorize('update', $course);

        $course->update(['status' => CourseStatus::Draft]);
        session()->flash('success', 'Course "' . $course->title . '" has been rejected and returned to draft.');
    }

    public function render()
    {
        $query = Course::with('instructor')
            ->withCount(['sections', 'lessons'])
            ->where('status', CourseStatus::Review);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('instructor', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
            });
        }

        $courses = $query->latest()->paginate(10);

        return view('livewire.admin-course-review', [
            'courses' => $courses,
        ]);
    }
}
