<?php

namespace App\Livewire;

use App\Enums\QuizQuestionType;
use App\Models\BankQuestion;
use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionBankManager extends Component
{
    use WithPagination;

    public int $courseId;
    public string $search = '';
    public string $categoryFilter = '';

    public bool $showForm = false;
    public ?int $editingId = null;
    public string $question = '';
    public string $type = 'mcq';
    public array $options = ['', ''];
    public string $correct_answer = '';
    public string $explanation = '';
    public string $category = '';
    public int $points = 1;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openForm(?int $id = null): void
    {
        if ($id) {
            $bq = BankQuestion::findOrFail($id);
            $this->editingId = $bq->id;
            $this->question = $bq->question;
            $this->type = $bq->type->value;
            $this->options = $bq->options ?? ['', ''];
            $this->correct_answer = $bq->correct_answer;
            $this->explanation = $bq->explanation ?? '';
            $this->category = $bq->category ?? '';
            $this->points = $bq->points;
        } else {
            $this->reset(['editingId', 'question', 'type', 'options', 'correct_answer', 'explanation', 'category', 'points']);
            $this->options = ['', ''];
            $this->points = 1;
        }
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
    }

    public function addOption(): void
    {
        $this->options[] = '';
    }

    public function removeOption(int $index): void
    {
        if (count($this->options) > 2) {
            unset($this->options[$index]);
            $this->options = array_values($this->options);
        }
    }

    public function save(): void
    {
        $this->validate([
            'question' => 'required|string|max:2000',
            'type' => 'required|in:mcq,true_false,short_answer',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);

        $data = [
            'course_id' => $this->courseId,
            'question' => $this->question,
            'type' => QuizQuestionType::from($this->type),
            'options' => $this->type === 'short_answer' ? null : array_values($this->options),
            'correct_answer' => $this->correct_answer,
            'explanation' => $this->explanation,
            'category' => $this->category ?: null,
            'points' => $this->points,
        ];

        if ($this->editingId) {
            BankQuestion::where('id', $this->editingId)->update($data);
            session()->flash('success', 'Question updated.');
        } else {
            BankQuestion::create($data);
            session()->flash('success', 'Question added to bank.');
        }

        $this->closeForm();
    }

    public function delete(int $id): void
    {
        BankQuestion::destroy($id);
        session()->flash('success', 'Question removed from bank.');
    }

    public function importToQuiz(int $bankQuestionId, int $quizId): void
    {
        $bq = BankQuestion::findOrFail($bankQuestionId);
        $maxOrder = \App\Models\QuizQuestion::where('quiz_id', $quizId)->max('order') ?? 0;

        \App\Models\QuizQuestion::create([
            'quiz_id' => $quizId,
            'question' => $bq->question,
            'type' => $bq->type,
            'options' => $bq->options,
            'correct_answer' => $bq->correct_answer,
            'explanation' => $bq->explanation,
            'order' => $maxOrder + 1,
            'points' => $bq->points,
        ]);

        session()->flash('success', 'Question imported to quiz.');
    }

    public function render()
    {
        $query = BankQuestion::where('course_id', $this->courseId);

        if ($this->search) {
            $query->where('question', 'like', "%{$this->search}%");
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        $questions = $query->latest()->paginate(15);
        $categories = BankQuestion::where('course_id', $this->courseId)
            ->whereNotNull('category')->distinct()->pluck('category');

        return view('livewire.question-bank-manager', [
            'questions' => $questions,
            'categories' => $categories,
        ]);
    }
}
