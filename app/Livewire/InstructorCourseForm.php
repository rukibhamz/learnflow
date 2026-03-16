<?php

namespace App\Livewire;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\Support\Str;
use Livewire\Component;

class InstructorCourseForm extends Component
{
    public ?Course $course = null;

    public $title = '';
    public $slug = '';
    public $description = '';
    public $short_description = '';
    public $price = 0;
    public $level = 'beginner';
    public $category = '';
    public $language = 'en';

    public function mount(?Course $course = null)
    {
        if ($course && $course->exists) {
            $this->authorize('update', $course);
            $this->course = $course;
            $this->title = $course->title;
            $this->slug = $course->slug;
            $this->description = $course->description ?? '';
            $this->short_description = $course->short_description ?? '';
            $this->price = $course->price;
            $this->level = $course->level?->value ?? 'beginner';
            $this->category = $course->category ?? '';
            $this->language = $course->language ?? 'en';
        }
    }

    public function updatedTitle($value)
    {
        if (!$this->course) {
            $this->slug = Str::slug($value);
        }
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:courses,slug' . ($this->course ? ',' . $this->course->id : ''),
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'level' => 'required|in:beginner,intermediate,advanced',
            'category' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:10',
        ]);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'level' => CourseLevel::from($this->level),
            'category' => $this->category,
            'language' => $this->language,
        ];

        if ($this->course) {
            $this->authorize('update', $this->course);
            $this->course->update($data);
            session()->flash('success', 'Course updated successfully.');
        } else {
            $this->authorize('create', Course::class);
            $data['instructor_id'] = auth()->id();
            $data['status'] = CourseStatus::Draft;
            $course = Course::create($data);
            return redirect()->route('instructor.courses.edit', $course)->with('success', 'Course created successfully.');
        }
    }

    public function render()
    {
        return view('livewire.instructor-course-form');
    }
}
