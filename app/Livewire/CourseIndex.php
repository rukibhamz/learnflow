<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;


class CourseIndex extends Component
{
    #[Url]
    public string $category = 'all';

    public string $search = '';

    public function setCategory(string $value): void
    {
        $this->category = $value;
    }

    public function updatedSearch(): void
    {
        // Search triggers re-render; wire:model.live handles it
    }

    public function getCoursesProperty()
    {
        $query = \App\Models\Course::query()
            ->published()
            ->with(['instructor']);

        if ($this->category !== 'all') {
            $query->where('category', $this->category);
        }

        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        return $query->get()->map(function ($course) {
            $course->url = route('courses.show', $course->slug);
            return $course;
        });
    }

    public function render()
    {
        return view('livewire.course-index');
    }
}
