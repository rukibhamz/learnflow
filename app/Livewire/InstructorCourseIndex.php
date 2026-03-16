<?php

namespace App\Livewire;

use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class InstructorCourseIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    public $showDeleteModal = false;
    public $deletingCourseId = null;
    public $deletingCourseTitle = '';

    protected $queryString = ['search', 'statusFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function confirmDelete($courseId)
    {
        $course = Course::where('instructor_id', auth()->id())->findOrFail($courseId);
        $this->deletingCourseId = $courseId;
        $this->deletingCourseTitle = $course->title;
        $this->showDeleteModal = true;
    }

    public function deleteCourse()
    {
        $course = Course::where('instructor_id', auth()->id())->findOrFail($this->deletingCourseId);
        $this->authorize('delete', $course);

        $course->delete();
        $this->showDeleteModal = false;
        session()->flash('success', 'Course deleted successfully.');
    }

    public function duplicateCourse($courseId)
    {
        $original = Course::where('instructor_id', auth()->id())
            ->with(['sections.lessons'])
            ->findOrFail($courseId);

        $this->authorize('create', Course::class);

        $newCourse = $original->replicate();
        $newCourse->title = $original->title . ' (Copy)';
        $newCourse->slug = Str::slug($newCourse->title) . '-' . Str::random(6);
        $newCourse->status = CourseStatus::Draft;
        $newCourse->save();

        foreach ($original->sections as $section) {
            $newSection = $section->replicate();
            $newSection->course_id = $newCourse->id;
            $newSection->save();

            foreach ($section->lessons as $lesson) {
                $newLesson = $lesson->replicate();
                $newLesson->section_id = $newSection->id;
                $newLesson->save();
            }
        }

        session()->flash('success', 'Course duplicated successfully.');
        return redirect()->route('instructor.courses.edit', $newCourse);
    }

    public function submitForReview($courseId)
    {
        $course = Course::where('instructor_id', auth()->id())->findOrFail($courseId);
        $this->authorize('update', $course);

        if ($course->status === CourseStatus::Draft) {
            $course->update(['status' => CourseStatus::Review]);
            session()->flash('success', 'Course submitted for review.');
        }
    }

    public function unpublish($courseId)
    {
        $course = Course::where('instructor_id', auth()->id())->findOrFail($courseId);
        $this->authorize('update', $course);

        if ($course->status === CourseStatus::Published) {
            $course->update(['status' => CourseStatus::Draft]);
            session()->flash('success', 'Course unpublished and returned to draft.');
        }
    }

    public function render()
    {
        $query = Course::where('instructor_id', auth()->id())
            ->withCount(['enrollments', 'sections', 'lessons'])
            ->withSum(['orders' => fn($q) => $q->where('status', 'paid')], 'amount');

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $courses = $query->latest()->paginate(10);

        return view('livewire.instructor-course-index', [
            'courses' => $courses,
        ]);
    }
}
