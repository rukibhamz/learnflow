<?php

namespace App\Livewire;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CourseForm extends Component
{
    use WithFileUploads;

    public ?Course $course = null;

    public bool $fromAdmin = false;

    public $title = '';
    public $slug = '';
    public $short_description = '';
    public $description = '';
    public $price = 0;
    public $level = 'beginner';
    public $language = 'en';
    public $category = '';
    public $requirements = [];
    public $outcomes = [];
    public $thumbnail;
    public $existingThumbnail = null;

    public $prerequisite_ids = [];

    public $newRequirement = '';
    public $newOutcome = '';

    public $lastSavedAt = null;
    public $isDirty = false;

    protected $listeners = ['autosave'];

    public function mount(?Course $course = null)
    {
        if ($course && $course->exists) {
            $this->authorize('update', $course);
            $this->course = $course;
            $this->title = $course->title;
            $this->slug = $course->slug;
            $this->short_description = $course->short_description ?? '';
            $this->description = $course->description ?? '';
            $this->price = $course->price;
            $this->level = $course->level?->value ?? 'beginner';
            $this->language = $course->language ?? 'en';
            $this->category = $course->category ?? '';
            $this->requirements = $course->requirements ?? [];
            $this->outcomes = $course->outcomes ?? [];
            $this->existingThumbnail = $course->getFirstMediaUrl('thumbnail', 'thumb');
            $this->prerequisite_ids = $course->prerequisite_ids ?? [];
        }
    }

    public function updatedTitle($value)
    {
        if (!$this->course || $this->course->status === CourseStatus::Draft) {
            $this->slug = Str::slug($value);
        }
        $this->isDirty = true;
    }

    public function updated($property)
    {
        if (!in_array($property, ['lastSavedAt', 'isDirty', 'newRequirement', 'newOutcome'])) {
            $this->isDirty = true;
        }
    }

    public function addRequirement()
    {
        if (trim($this->newRequirement)) {
            $this->requirements[] = trim($this->newRequirement);
            $this->newRequirement = '';
            $this->isDirty = true;
        }
    }

    public function removeRequirement($index)
    {
        unset($this->requirements[$index]);
        $this->requirements = array_values($this->requirements);
        $this->isDirty = true;
    }

    public function addOutcome()
    {
        if (trim($this->newOutcome)) {
            $this->outcomes[] = trim($this->newOutcome);
            $this->newOutcome = '';
            $this->isDirty = true;
        }
    }

    public function removeOutcome($index)
    {
        unset($this->outcomes[$index]);
        $this->outcomes = array_values($this->outcomes);
        $this->isDirty = true;
    }

    public function autosave()
    {
        if ($this->isDirty && $this->course && $this->course->status === CourseStatus::Draft) {
            $this->saveDraft(silent: true);
        }
    }

    public function saveDraft($silent = false)
    {
        $this->saveInternal(CourseStatus::Draft, $silent);
    }

    public function submitForReview()
    {
        $this->saveInternal(CourseStatus::Review);
        session()->flash('success', 'Course submitted for review.');
    }

    public function publish()
    {
        $this->authorize('publish', $this->course);
        $this->saveInternal(CourseStatus::Published);
        session()->flash('success', 'Course published successfully.');
    }

    public function unpublish()
    {
        $this->authorize('update', $this->course);
        $this->saveInternal(CourseStatus::Draft);
        session()->flash('success', 'Course unpublished.');
    }

    protected function saveInternal(CourseStatus $status, bool $silent = false)
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:courses,slug' . ($this->course ? ',' . $this->course->id : ''),
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'level' => 'required|in:beginner,intermediate,advanced',
            'language' => 'nullable|string|max:10',
            'category' => 'nullable|string|max:100',
            'thumbnail' => 'nullable|image|max:2048',
            'prerequisite_ids' => 'nullable|array',
            'prerequisite_ids.*' => 'integer|exists:courses,id',
        ]);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'price' => $this->price,
            'level' => CourseLevel::from($this->level),
            'language' => $this->language,
            'category' => $this->category,
            'requirements' => array_values(array_filter($this->requirements)),
            'outcomes' => array_values(array_filter($this->outcomes)),
            'prerequisite_ids' => array_values(array_filter(array_map('intval', $this->prerequisite_ids))),
            'status' => $status,
        ];

        if ($this->course) {
            $this->authorize('update', $this->course);
            $this->course->update($data);
        } else {
            $this->authorize('create', Course::class);
            $data['instructor_id'] = auth()->id();
            $this->course = Course::create($data);

            Enrollment::create([
                'user_id' => auth()->id(),
                'course_id' => $this->course->id,
                'enrolled_at' => now(),
            ]);
        }

        if ($this->thumbnail) {
            $this->course->clearMediaCollection('thumbnail');
            $this->course->addMedia($this->thumbnail->getRealPath())
                ->usingFileName(Str::uuid() . '.' . $this->thumbnail->getClientOriginalExtension())
                ->toMediaCollection('thumbnail');
            $this->existingThumbnail = $this->course->getFirstMediaUrl('thumbnail', 'thumb');
            $this->thumbnail = null;
        }

        $this->lastSavedAt = now();
        $this->isDirty = false;

        if (!$silent) {
            session()->flash('success', 'Course saved successfully.');
        }

        if (!request()->routeIs($this->fromAdmin ? 'admin.courses.edit' : 'instructor.courses.edit')) {
            return redirect()->route($this->fromAdmin ? 'admin.courses.edit' : 'instructor.courses.edit', $this->course);
        }
    }

    public function removeThumbnail()
    {
        if ($this->course) {
            $this->course->clearMediaCollection('thumbnail');
            $this->existingThumbnail = null;
        }
        $this->thumbnail = null;
    }

    public function render()
    {
        return view('livewire.course-form');
    }
}
