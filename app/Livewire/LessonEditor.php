<?php

namespace App\Livewire;

use Livewire\Component;

class LessonEditor extends Component
{
    public $lessonId;
    public $type = 'video';

    public function render()
    {
        return view('livewire.lesson-editor');
    }
}
