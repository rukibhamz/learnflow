<?php

namespace App\Livewire;

use App\Events\LessonCompleted;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use Livewire\Component;

class LessonPlayer extends Component
{
    public \App\Models\Course $course;
    public ?\App\Models\Lesson $currentLesson = null;
    public array $completedLessonIds = [];
    
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

    public function markComplete(): void
    {
        $user = auth()->user();
        if (! $user || ! $this->currentLesson) {
            return;
        }

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $this->course->id)
            ->first();

        if (! $enrollment) {
            return;
        }

        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $this->currentLesson->id],
            ['completed_at' => now()]
        );

        event(new LessonCompleted($user, $this->currentLesson->loadMissing('section.course'), now()));

        $this->refreshCompleted();
        session()->flash('success', 'Lesson marked complete.');
    }

    protected function refreshCompleted(): void
    {
        $userId = auth()->id();
        if (! $userId) {
            $this->completedLessonIds = [];
            return;
        }

        $lessonIds = $this->course->lessons()->pluck('lessons.id');
        $this->completedLessonIds = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $lessonIds)
            ->pluck('lesson_id')
            ->toArray();
    }

    public function render()
    {
        $this->refreshCompleted();

        return view('livewire.lesson-player', [
            'sections'    => $this->course->sections()->with('lessons')->get(),
            'currentTime' => '0:00',
            'duration'    => $this->currentLesson?->duration_seconds
                ? gmdate('G:i:s', $this->currentLesson->duration_seconds)
                : '0:00',
        ]);
    }
}
