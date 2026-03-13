<?php

namespace App\Livewire;

use Livewire\Component;

class LessonPlayer extends Component
{
    public \App\Models\Course $course;
    public ?\App\Models\Lesson $currentLesson = null;
    
    public function mount(\App\Models\Course $course, $lessonId = null)
    {
        $this->course = $course;
        $this->currentLesson = $lessonId 
            ? \App\Models\Lesson::find($lessonId) 
            : $course->sections->first()?->lessons->first();
    }

    public function selectLesson($id): void
    {
        $this->currentLesson = \App\Models\Lesson::find($id);
    }

    public function render()
    {
        return view('livewire.lesson-player', [
            'sections' => $this->course->sections()->with('lessons')->get()
        ]);
    }
}
