<?php

namespace App\Services;

use App\Enums\QuizQuestionType;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;

class QuizGradingService
{
    /**
     * Grade a quiz attempt and return the result.
     *
     * @param  array<int, string|int>  $answers  [question_id => student_answer]
     */
    public function grade(Quiz $quiz, array $answers): array
    {
        $questions = $quiz->questions;
        $totalPoints = $questions->sum('points') ?: $questions->count();
        $earnedPoints = 0;
        $results = [];

        foreach ($questions as $question) {
            $studentAnswer = $answers[$question->id] ?? null;
            $isCorrect = $this->checkAnswer($question, $studentAnswer);

            if ($isCorrect) {
                $earnedPoints += $question->points ?: 1;
            }

            $results[$question->id] = [
                'correct' => $isCorrect,
                'student_answer' => $studentAnswer,
                'correct_answer' => $question->correct_answer,
                'explanation' => $question->explanation,
            ];
        }

        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 1) : 0;
        $passed = $score >= ($quiz->passing_score ?? 70);

        return [
            'score' => $score,
            'passed' => $passed,
            'earned_points' => $earnedPoints,
            'total_points' => $totalPoints,
            'results' => $results,
        ];
    }

    protected function checkAnswer(QuizQuestion $question, mixed $studentAnswer): bool
    {
        if ($studentAnswer === null || $studentAnswer === '') {
            return false;
        }

        return match ($question->type) {
            QuizQuestionType::Mcq => (string) $studentAnswer === (string) $question->correct_answer,
            QuizQuestionType::TrueFalse => strtolower(trim((string) $studentAnswer)) === strtolower(trim((string) $question->correct_answer)),
            QuizQuestionType::ShortAnswer => $this->checkShortAnswer($question->correct_answer, (string) $studentAnswer),
        };
    }

    protected function checkShortAnswer(string $correctAnswer, string $studentAnswer): bool
    {
        $normalize = fn (string $s) => strtolower(trim(preg_replace('/\s+/', ' ', $s)));

        return $normalize($studentAnswer) === $normalize($correctAnswer);
    }
}
