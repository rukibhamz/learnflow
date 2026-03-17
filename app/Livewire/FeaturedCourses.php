<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Course;
use Illuminate\Support\Facades\Cache;

class FeaturedCourses extends Component
{
    public string $category = 'all';

    public function setCategory(string $value): void
    {
        $this->category = $value;
    }

    public function getCoursesProperty()
    {
        $category = $this->category;

        $key = "courses:featured:{$category}:v1";
        $callback = function () use ($category) {
            $query = Course::published()
                ->with(['instructor', 'media'])
                ->withCount(['reviews' => fn ($q) => $q->whereNotNull('approved_at')])
                ->withAvg(['reviews' => fn ($q) => $q->whereNotNull('approved_at')], 'rating')
                ->latest();

            if ($category !== 'all') {
                $query->where('category', $category);
            }

            return $query->take(6)->get()->map(function ($c) {
                $c->url = route('courses.show', $c->slug);
                $c->rating = $c->reviews_avg_rating;
                return $c;
            });
        };

        try {
            return Cache::tags(['courses', 'featured'])->remember($key, 900, $callback);
        } catch (\Throwable) {
            return Cache::remember($key, 900, $callback);
        }
    }

    public function render()
    {
        return view('livewire.featured-courses');
    }
}
