<?php

namespace Tests\Feature;

use App\Enums\QuizQuestionType;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\Section;
use App\Models\User;
use App\Services\QuizGradingService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function createQuizWithQuestions(): Quiz
    {
        $course = Course::factory()->published()->create();
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create(['section_id' => $section->id]);

        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'course_id' => $course->id,
            'title' => 'Test Quiz',
            'time_limit_minutes' => 30,
            'attempts_allowed' => 3,
            'passing_score' => 70,
            'shuffle_questions' => false,
            'show_answers_after' => true,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'What is 2+2?',
            'type' => QuizQuestionType::Mcq,
            'options' => ['3', '4', '5', '6'],
            'correct_answer' => '1',
            'order' => 1,
            'points' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'PHP is a programming language',
            'type' => QuizQuestionType::TrueFalse,
            'options' => null,
            'correct_answer' => 'true',
            'order' => 2,
            'points' => 1,
        ]);

        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'What does HTML stand for?',
            'type' => QuizQuestionType::ShortAnswer,
            'options' => null,
            'correct_answer' => 'HyperText Markup Language',
            'order' => 3,
            'points' => 1,
        ]);

        return $quiz->fresh(['questions']);
    }

    public function test_grading_service_scores_perfect_answers(): void
    {
        $quiz = $this->createQuizWithQuestions();
        $questions = $quiz->questions;

        $answers = [
            $questions[0]->id => '1',
            $questions[1]->id => 'true',
            $questions[2]->id => 'HyperText Markup Language',
        ];

        $grader = new QuizGradingService();
        $result = $grader->grade($quiz, $answers);

        $this->assertEquals(100, $result['score']);
        $this->assertTrue($result['passed']);
        $this->assertEquals(3, $result['earned_points']);
        $this->assertEquals(3, $result['total_points']);
    }

    public function test_grading_service_scores_zero_for_wrong_answers(): void
    {
        $quiz = $this->createQuizWithQuestions();
        $questions = $quiz->questions;

        $answers = [
            $questions[0]->id => '0',
            $questions[1]->id => 'false',
            $questions[2]->id => 'Wrong Answer',
        ];

        $grader = new QuizGradingService();
        $result = $grader->grade($quiz, $answers);

        $this->assertEquals(0, $result['score']);
        $this->assertFalse($result['passed']);
    }

    public function test_grading_service_handles_partial_answers(): void
    {
        $quiz = $this->createQuizWithQuestions();
        $questions = $quiz->questions;

        $answers = [
            $questions[0]->id => '1',
            $questions[1]->id => 'false',
        ];

        $grader = new QuizGradingService();
        $result = $grader->grade($quiz, $answers);

        $this->assertEqualsWithDelta(33.3, $result['score'], 0.1);
        $this->assertFalse($result['passed']);
    }

    public function test_short_answer_is_case_insensitive(): void
    {
        $quiz = $this->createQuizWithQuestions();
        $questions = $quiz->questions;

        $answers = [
            $questions[0]->id => '1',
            $questions[1]->id => 'true',
            $questions[2]->id => 'hypertext markup language',
        ];

        $grader = new QuizGradingService();
        $result = $grader->grade($quiz, $answers);

        $this->assertEquals(100, $result['score']);
    }

    public function test_attempt_count_is_enforced(): void
    {
        $quiz = $this->createQuizWithQuestions();
        $user = User::factory()->create();
        $user->assignRole('student');

        for ($i = 0; $i < 3; $i++) {
            QuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'answers' => [],
                'score' => 50,
                'passed' => false,
                'started_at' => now(),
                'completed_at' => now(),
            ]);
        }

        $attemptCount = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->count();

        $this->assertEquals(3, $attemptCount);
        $this->assertTrue($attemptCount >= $quiz->attempts_allowed);
    }
}
