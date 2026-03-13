<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Course;

class FeaturedCourses extends Component
{
    public string $category = 'all';

    public function setCategory(string $value): void
    {
        $this->category = $value;
    }

    public function getCoursesProperty()
    {
        $query = Course::published()
            ->with('instructor');

        if ($this->category !== 'all') {
            $query->where('category', $this->category);
        }

        return $query->take(6)->get()->map(function($c) {
            $c->url = route('courses.show', $c->slug);
            return $c;
        });
    }

    public function render()
    {
        return view('livewire.featured-courses');
    }
}
