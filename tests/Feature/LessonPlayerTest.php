<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LessonPlayerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function enrolledStudent(Course $course): User
    {
        $user = User::factory()->create();
        $user->assignRole('student');
        Enrollment::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);
        return $user;
    }

    private function courseWithLessons(): array
    {
        $course  = Course::factory()->published()->create();
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lesson1 = Lesson::factory()->create(['section_id' => $section->id, 'order' => 1]);
        $lesson2 = Lesson::factory()->create(['section_id' => $section->id, 'order' => 2]);
        return [$course, $section, $lesson1, $lesson2];
    }

    public function test_player_renders_with_course(): void
    {
        [$course, , $lesson1] = $this->courseWithLessons();
        $user = $this->enrolledStudent($course);

        Livewire::actingAs($user)
            ->test(\App\Livewire\LessonPlayer::class, ['course' => $course])
            ->assertOk();
    }

    public function test_player_defaults_to_first_lesson(): void
    {
        [$course, , $lesson1] = $this->courseWithLessons();
        $user = $this->enrolledStudent($course);

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\LessonPlayer::class, ['course' => $course]);

        $this->assertEquals($lesson1->id, $component->get('currentLesson')->id);
    }

    public function test_player_mounts_with_specific_lesson(): void
    {
        [$course, , $lesson1, $lesson2] = $this->courseWithLessons();
        $user = $this->enrolledStudent($course);

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\LessonPlayer::class, [
                'course'   => $course,
                'lessonId' => $lesson2->id,
            ]);

        $this->assertEquals($lesson2->id, $component->get('currentLesson')->id);
    }

    public function test_select_lesson_switches_current_lesson(): void
    {
        [$course, , $lesson1, $lesson2] = $this->courseWithLessons();
        $user = $this->enrolledStudent($course);

        Livewire::actingAs($user)
            ->test(\App\Livewire\LessonPlayer::class, ['course' => $course])
            ->call('selectLesson', $lesson2->id)
            ->assertSet('currentLesson.id', $lesson2->id);
    }

    public function test_player_passes_sections_to_view(): void
    {
        [$course, $section] = $this->courseWithLessons();
        $user = $this->enrolledStudent($course);

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\LessonPlayer::class, ['course' => $course]);

        $sections = $component->viewData('sections');
        $this->assertCount(1, $sections);
        $this->assertEquals($section->id, $sections->first()->id);
    }

    public function test_player_shows_lesson_title(): void
    {
        [$course, , $lesson1] = $this->courseWithLessons();
        $user = $this->enrolledStudent($course);

        Livewire::actingAs($user)
            ->test(\App\Livewire\LessonPlayer::class, ['course' => $course])
            ->assertSee($lesson1->title);
    }

    public function test_player_handles_course_with_no_lessons(): void
    {
        $course = Course::factory()->published()->create();
        $user   = $this->enrolledStudent($course);

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\LessonPlayer::class, ['course' => $course]);

        $this->assertNull($component->get('currentLesson'));
    }
}
