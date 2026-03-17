<?php

namespace Tests\Feature;

use App\Models\BankQuestion;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionBankTest extends TestCase
{
    use RefreshDatabase;

    public function test_bank_question_can_be_created()
    {
        $course = Course::factory()->create();
        $bq = BankQuestion::create([
            'course_id' => $course->id,
            'question' => 'What is PHP?',
            'type' => 'mcq',
            'options' => ['A scripting language', 'A database', 'An OS', 'A browser'],
            'correct_answer' => '0',
            'explanation' => 'PHP is a scripting language.',
            'category' => 'Basics',
            'points' => 2,
        ]);

        $this->assertDatabaseHas('question_bank', [
            'id' => $bq->id,
            'question' => 'What is PHP?',
            'category' => 'Basics',
        ]);
    }

    public function test_bank_question_belongs_to_course()
    {
        $course = Course::factory()->create();
        $bq = BankQuestion::create([
            'course_id' => $course->id,
            'question' => 'Test question',
            'type' => 'true_false',
            'correct_answer' => 'true',
        ]);

        $this->assertEquals($course->id, $bq->course->id);
    }

    public function test_bank_question_options_cast_to_array()
    {
        $course = Course::factory()->create();
        $bq = BankQuestion::create([
            'course_id' => $course->id,
            'question' => 'MCQ test',
            'type' => 'mcq',
            'options' => ['Option A', 'Option B'],
            'correct_answer' => '0',
        ]);

        $bq->refresh();
        $this->assertIsArray($bq->options);
        $this->assertCount(2, $bq->options);
    }

    public function test_bank_questions_deleted_when_course_deleted()
    {
        $course = Course::factory()->create();
        BankQuestion::create([
            'course_id' => $course->id,
            'question' => 'Will be deleted',
            'type' => 'short_answer',
            'correct_answer' => 'yes',
        ]);

        $course->forceDelete();

        $this->assertDatabaseCount('question_bank', 0);
    }
}
