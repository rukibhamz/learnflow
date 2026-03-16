<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Section;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentProgressTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $instructor;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->instructor = User::factory()->create();
        $this->instructor->assignRole('instructor');

        $this->student = User::factory()->create();
        $this->student->assignRole('student');

        $this->course = Course::factory()->create(['instructor_id' => $this->instructor->id]);
    }

    public function test_progress_is_zero_when_no_lessons_exist(): void
    {
        $enrollment = Enrollment::factory()->create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);

        $this->assertEquals(0.0, $enrollment->progress_percentage);
    }

    public function test_progress_is_zero_when_no_lessons_completed(): void
    {
        $section = Section::factory()->create(['course_id' => $this->course->id]);
        Lesson::factory()->count(3)->create(['section_id' => $section->id]);

        $enrollment = Enrollment::factory()->create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);

        $this->assertEquals(0.0, $enrollment->progress_percentage);
    }

    public function test_progress_is_100_when_all_lessons_completed(): void
    {
        $section = Section::factory()->create(['course_id' => $this->course->id]);
        $lessons = Lesson::factory()->count(2)->create(['section_id' => $section->id]);

        $enrollment = Enrollment::factory()->create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);

        foreach ($lessons as $lesson) {
            LessonProgress::create([
                'user_id' => $this->student->id,
                'lesson_id' => $lesson->id,
                'completed_at' => now(),
            ]);
        }

        $this->assertEquals(100.0, $enrollment->progress_percentage);
    }

    public function test_progress_is_partial_when_some_lessons_completed(): void
    {
        $section = Section::factory()->create(['course_id' => $this->course->id]);
        $lessons = Lesson::factory()->count(4)->create(['section_id' => $section->id]);

        $enrollment = Enrollment::factory()->create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
        ]);

        // Complete 2 of 4 lessons
        LessonProgress::create([
            'user_id' => $this->student->id,
            'lesson_id' => $lessons[0]->id,
            'completed_at' => now(),
        ]);
        LessonProgress::create([
            'user_id' => $this->student->id,
            'lesson_id' => $lessons[1]->id,
            'completed_at' => now(),
        ]);

        $this->assertEquals(50.0, $enrollment->progress_percentage);
    }

    public function test_course_completion_rate_is_null_with_no_enrollments(): void
    {
        $this->assertNull($this->course->completion_rate);
    }

    public function test_course_completion_rate_calculates_correctly(): void
    {
        $e1 = Enrollment::factory()->create(['course_id' => $this->course->id, 'completed_at' => now()]);
        $e2 = Enrollment::factory()->create(['course_id' => $this->course->id, 'completed_at' => null]);

        $this->assertEquals(50.0, $this->course->fresh()->completion_rate);
    }

    public function test_enrolled_count_reflects_enrollments(): void
    {
        Enrollment::factory()->count(3)->create(['course_id' => $this->course->id]);

        $this->assertEquals(3, $this->course->fresh()->enrolled_count);
    }
}
