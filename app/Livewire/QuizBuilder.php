<?php

namespace App\Livewire;

use App\Enums\QuizQuestionType;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Livewire\Component;

class QuizBuilder extends Component
{
    public ?int $lessonId = null;
    public ?Quiz $quiz = null;

    public string $title = '';
    public string $description = '';
    public ?int $time_limit_minutes = 30;
    public int $attempts_allowed = 3;
    public int $passing_score = 70;
    public bool $shuffle_questions = false;
    public bool $show_answers_after = true;

    public array $questions = [];

    public function mount(?int $lessonId = null, ?int $quizId = null): void
    {
        $this->lessonId = $lessonId;

        if ($quizId) {
            $this->quiz = Quiz::with('questions')->find($quizId);
        } elseif ($lessonId) {
            $this->quiz = Quiz::with('questions')->where('lesson_id', $lessonId)->first();
        }

        if ($this->quiz) {
            $this->title = $this->quiz->title;
            $this->description = $this->quiz->description ?? '';
            $this->time_limit_minutes = $this->quiz->time_limit_minutes;
            $this->attempts_allowed = $this->quiz->attempts_allowed ?? 3;
            $this->passing_score = $this->quiz->passing_score ?? 70;
            $this->shuffle_questions = $this->quiz->shuffle_questions ?? false;
            $this->show_answers_after = $this->quiz->show_answers_after ?? true;

            $this->questions = $this->quiz->questions->map(fn (QuizQuestion $q) => [
                'id' => $q->id,
                'type' => $q->type->value,
                'question' => $q->question,
                'options' => $q->options ?? [],
                'correct_answer' => $q->correct_answer,
                'explanation' => $q->explanation ?? '',
                'points' => $q->points ?? 1,
            ])->toArray();
        }
    }

    public function addQuestion(): void
    {
        $this->questions[] = [
            'id' => null,
            'type' => 'mcq',
            'question' => '',
            'options' => ['', ''],
            'correct_answer' => '',
            'explanation' => '',
            'points' => 1,
        ];
    }

    public function removeQuestion(int $index): void
    {
        $q = $this->questions[$index] ?? null;

        if ($q && $q['id']) {
            QuizQuestion::destroy($q['id']);
        }

        unset($this->questions[$index]);
        $this->questions = array_values($this->questions);
    }

    public function addOption(int $questionIndex): void
    {
        $this->questions[$questionIndex]['options'][] = '';
    }

    public function removeOption(int $questionIndex, int $optionIndex): void
    {
        unset($this->questions[$questionIndex]['options'][$optionIndex]);
        $this->questions[$questionIndex]['options'] = array_values($this->questions[$questionIndex]['options']);
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'time_limit_minutes' => 'nullable|integer|min:1|max:300',
            'attempts_allowed' => 'required|integer|min:1|max:100',
            'passing_score' => 'required|integer|min:0|max:100',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string|max:2000',
            'questions.*.type' => 'required|in:mcq,true_false,short_answer',
            'questions.*.correct_answer' => 'required|string',
            'questions.*.points' => 'required|integer|min:1',
        ]);

        $lesson = $this->lessonId ? Lesson::with('section')->find($this->lessonId) : null;

        $quizData = [
            'title' => $this->title,
            'description' => $this->description,
            'time_limit_minutes' => $this->time_limit_minutes,
            'attempts_allowed' => $this->attempts_allowed,
            'passing_score' => $this->passing_score,
            'shuffle_questions' => $this->shuffle_questions,
            'show_answers_after' => $this->show_answers_after,
            'lesson_id' => $this->lessonId,
            'course_id' => $lesson?->section?->course_id,
        ];

        if ($this->quiz) {
            $this->quiz->update($quizData);
        } else {
            $this->quiz = Quiz::create($quizData);
        }

        $existingIds = [];
        foreach ($this->questions as $order => $qData) {
            $questionData = [
                'quiz_id' => $this->quiz->id,
                'question' => $qData['question'],
                'type' => QuizQuestionType::from($qData['type']),
                'options' => $qData['type'] === 'short_answer' ? null : array_values($qData['options'] ?? []),
                'correct_answer' => $qData['correct_answer'],
                'explanation' => $qData['explanation'] ?? '',
                'order' => $order + 1,
                'points' => $qData['points'] ?? 1,
            ];

            if (! empty($qData['id'])) {
                QuizQuestion::where('id', $qData['id'])->update($questionData);
                $existingIds[] = $qData['id'];
            } else {
                $newQ = QuizQuestion::create($questionData);
                $this->questions[$order]['id'] = $newQ->id;
                $existingIds[] = $newQ->id;
            }
        }

        QuizQuestion::where('quiz_id', $this->quiz->id)
            ->whereNotIn('id', $existingIds)
            ->delete();

        session()->flash('success', 'Quiz saved successfully.');
    }

    public function deleteQuiz(): void
    {
        if ($this->quiz) {
            $this->quiz->questions()->delete();
            $this->quiz->delete();
            $this->quiz = null;
            $this->questions = [];
            $this->title = '';
            $this->description = '';
            session()->flash('success', 'Quiz deleted.');
        }
    }

    public function render()
    {
        return view('livewire.quiz-builder');
    }
}
