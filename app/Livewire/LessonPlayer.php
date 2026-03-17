<?php

namespace App\Livewire;

use App\Events\LessonCompleted;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Livewire\Attributes\On;
use Livewire\Component;

class LessonPlayer extends Component
{
    public \App\Models\Course $course;
    public ?Lesson $currentLesson = null;
    public array $completedLessonIds = [];

    public function mount(\App\Models\Course $course, $lessonId = null)
    {
        $this->course = $course;
        $this->currentLesson = $lessonId
            ? Lesson::find($lessonId)
            : $course->sections->first()?->lessons->first();
    }

    #[On('selectLesson')]
    public function selectLesson($id): void
    {
        $this->currentLesson = Lesson::find($id);
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

        if (in_array($this->currentLesson->id, $this->completedLessonIds)) {
            return;
        }

        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $this->currentLesson->id],
            ['completed_at' => now()]
        );

        event(new LessonCompleted($user, $this->currentLesson->loadMissing('section.course'), now()));

        $this->refreshCompleted();
        $this->advanceToNext();
    }

    protected function advanceToNext(): void
    {
        $allLessons = $this->course->sections()
            ->with('lessons')
            ->get()
            ->flatMap(fn ($s) => $s->lessons);

        $currentIndex = $allLessons->search(fn ($l) => $l->id === $this->currentLesson->id);

        if ($currentIndex !== false && isset($allLessons[$currentIndex + 1])) {
            $this->currentLesson = $allLessons[$currentIndex + 1];
        }
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
