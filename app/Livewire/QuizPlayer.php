<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizGradingService;
use Livewire\Component;

class QuizPlayer extends Component
{
    public Quiz $quiz;
    public array $answers = [];
    public ?QuizAttempt $currentAttempt = null;
    public ?array $gradeResult = null;
    public string $state = 'ready'; // ready, taking, completed, no_retakes

    public function mount(Quiz $quiz): void
    {
        $this->quiz = $quiz->loadMissing('questions');

        $user = auth()->user();
        $attemptCount = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->count();

        if ($quiz->attempts_allowed && $attemptCount >= $quiz->attempts_allowed) {
            $this->state = 'no_retakes';
            $this->loadLastAttempt();
            return;
        }

        $inProgress = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->whereNull('completed_at')
            ->first();

        if ($inProgress) {
            $this->currentAttempt = $inProgress;
            $this->answers = $inProgress->answers ?? [];
            $this->state = 'taking';
        }
    }

    public function startAttempt(): void
    {
        $user = auth()->user();

        $attemptCount = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $this->quiz->id)
            ->count();

        if ($this->quiz->attempts_allowed && $attemptCount >= $this->quiz->attempts_allowed) {
            $this->state = 'no_retakes';
            return;
        }

        $this->currentAttempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $this->quiz->id,
            'answers' => [],
            'started_at' => now(),
        ]);

        $this->answers = [];
        $this->gradeResult = null;
        $this->state = 'taking';
    }

    public function submitQuiz(): void
    {
        if (! $this->currentAttempt) {
            return;
        }

        $grader = app(QuizGradingService::class);
        $result = $grader->grade($this->quiz, $this->answers);

        $this->currentAttempt->update([
            'answers' => $this->answers,
            'score' => $result['score'],
            'passed' => $result['passed'],
            'completed_at' => now(),
        ]);

        $this->gradeResult = $result;
        $this->state = 'completed';
    }

    public function retake(): void
    {
        $this->gradeResult = null;
        $this->currentAttempt = null;
        $this->answers = [];
        $this->state = 'ready';
        $this->mount($this->quiz);

        if ($this->state !== 'no_retakes') {
            $this->state = 'ready';
        }
    }

    protected function loadLastAttempt(): void
    {
        $last = QuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $this->quiz->id)
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->first();

        if ($last) {
            $this->currentAttempt = $last;
            $this->answers = $last->answers ?? [];

            $grader = app(QuizGradingService::class);
            $this->gradeResult = $grader->grade($this->quiz, $this->answers);
        }
    }

    public function getAttemptsUsedProperty(): int
    {
        return QuizAttempt::where('user_id', auth()->id())
            ->where('quiz_id', $this->quiz->id)
            ->count();
    }

    public function getQuestionsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        $questions = $this->quiz->questions;
        if ($this->quiz->shuffle_questions && $this->state === 'taking') {
            return $questions->shuffle();
        }
        return $questions;
    }

    public function render()
    {
        return view('livewire.quiz-player');
    }
}
