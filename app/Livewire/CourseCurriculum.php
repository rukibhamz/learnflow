<?php

namespace App\Livewire;

use Livewire\Component;

class CourseCurriculum extends Component
{
    public $courseId;
    public $sections = [
        ['title' => 'Getting Started', 'lessons' => [
            ['title' => 'Welcome', 'type' => 'video', 'duration' => '5:00'],
            ['title' => 'Setup', 'type' => 'text', 'duration' => '10 min'],
        ]],
        ['title' => 'HTML Basics', 'lessons' => [
            ['title' => 'Structure', 'type' => 'video', 'duration' => '12:00'],
        ]],
    ];

    public function render()
    {
        return view('livewire.course-curriculum');
    }
}
