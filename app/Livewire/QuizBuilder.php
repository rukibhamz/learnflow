<?php

namespace App\Livewire;

use Livewire\Component;

class QuizBuilder extends Component
{
    public $quizId;
    public $questions = [
        ['type' => 'mcq', 'text' => 'What is HTML?', 'options' => ['Hyper Text', 'Markup Language'], 'correct' => 1],
    ];

    public function render()
    {
        return view('livewire.quiz-builder');
    }
}
