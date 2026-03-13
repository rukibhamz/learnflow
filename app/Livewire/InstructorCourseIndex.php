<?php

namespace App\Livewire;

use Livewire\Component;

class InstructorCourseIndex extends Component
{
    public $search = '';

    public function render()
    {
        $courses = [
            ['title' => 'Web Development Bootcamp', 'status' => 'published', 'students' => 240, 'price' => 49, 'updated' => 'Mar 10'],
            ['title' => 'Data Science Fundamentals', 'status' => 'draft', 'students' => 0, 'price' => 79, 'updated' => 'Mar 8'],
            ['title' => 'UX Design Masterclass', 'status' => 'review', 'students' => 12, 'price' => 0, 'updated' => 'Mar 5'],
        ];
        return view('livewire.instructor-course-index', ['courses' => $courses]);
    }
}
