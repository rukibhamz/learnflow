<?php

namespace Tests\Unit;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Review;
use App\Models\Section;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseModelTest extends TestCase
{
    use RefreshDatabase;

    protected User $instructor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->instructor = User::factory()->create();
        $this->instructor->assignRole('instructor');
    }

    public function test_published_scope_returns_only_published_courses(): void
    {
        Course::factory()->create(['instructor_id' => $this->instructor->id, 'status' => CourseStatus::Published]);
        Course::factory()->create(['instructor_id' => $this->instructor->id, 'status' => CourseStatus::Draft]);

        $published = Course::published()->get();
        $this->assertCount(1, $published);
        $this->assertEquals(CourseStatus::Published, $published->first()->status);
    }

    public function test_by_instructor_scope_filters_correctly(): void
    {
        $other = User::factory()->create();
        $other->assignRole('instructor');

        Course::factory()->create(['instructor_id' => $this->instructor->id]);
        Course::factory()->create(['instructor_id' => $other->id]);

        $courses = Course::byInstructor($this->instructor->id)->get();
        $this->assertCount(1, $courses);
    }

    public function test_total_lessons_count_sums_across_sections(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->instructor->id]);
        $s1 = Section::factory()->create(['course_id' => $course->id]);
        $s2 = Section::factory()->create(['course_id' => $course->id]);
        Lesson::factory()->count(3)->create(['section_id' => $s1->id]);
        Lesson::factory()->count(2)->create(['section_id' => $s2->id]);

        $this->assertEquals(5, $course->fresh()->total_lessons_count);
    }

    public function test_total_duration_seconds_sums_lesson_durations(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->instructor->id]);
        $section = Section::factory()->create(['course_id' => $course->id]);
        Lesson::factory()->create(['section_id' => $section->id, 'duration_seconds' => 300]);
        Lesson::factory()->create(['section_id' => $section->id, 'duration_seconds' => 600]);

        $this->assertEquals(900, $course->fresh()->total_duration_seconds);
    }

    public function test_average_rating_is_zero_with_no_reviews(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->instructor->id]);
        $this->assertEquals(0.0, $course->average_rating);
    }

    public function test_sections_are_ordered_by_order_column(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->instructor->id]);
        Section::factory()->create(['course_id' => $course->id, 'order' => 3, 'title' => 'Third']);
        Section::factory()->create(['course_id' => $course->id, 'order' => 1, 'title' => 'First']);
        Section::factory()->create(['course_id' => $course->id, 'order' => 2, 'title' => 'Second']);

        $sections = $course->sections;
        $this->assertEquals('First', $sections[0]->title);
        $this->assertEquals('Second', $sections[1]->title);
        $this->assertEquals('Third', $sections[2]->title);
    }

    public function test_soft_delete_does_not_permanently_remove_course(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->instructor->id]);
        $course->delete();

        $this->assertSoftDeleted('courses', ['id' => $course->id]);
        $this->assertNotNull(Course::withTrashed()->find($course->id));
    }
}
