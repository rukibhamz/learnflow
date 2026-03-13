<?php

namespace App\Livewire;

use Livewire\Component;

class CourseForm extends Component
{
    public $courseId;
    public $title = '';
    public $slug = '';
    public $shortDescription = '';
    public $status = 'draft';
    public $autosaved = null;

    public function render()
    {
        return view('livewire.course-form');
    }
}
