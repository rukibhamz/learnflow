<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Section;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function student(): User
    {
        $user = User::factory()->create();
        $user->assignRole('student');
        return $user;
    }

    public function test_dashboard_renders_for_authenticated_student(): void
    {
        $user = $this->student();

        Livewire::actingAs($user)
            ->test(\App\Livewire\StudentDashboard::class)
            ->assertOk();
    }

    public function test_in_progress_tab_shows_active_enrollments(): void
    {
        $user = $this->student();
        $course = Course::factory()->published()->create();
        Enrollment::factory()->create([
            'user_id'      => $user->id,
            'course_id'    => $course->id,
            'completed_at' => null,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\StudentDashboard::class)
            ->assertSet('activeTab', 'in_progress')
            ->assertSee($course->title);
    }

    public function test_completed_tab_shows_finished_enrollments(): void
    {
        $user = $this->student();
        $course = Course::factory()->published()->create();
        Enrollment::factory()->completed()->create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\StudentDashboard::class)
            ->call('setTab', 'completed')
            ->assertSet('activeTab', 'completed')
            ->assertSee($course->title);
    }

    public function test_wishlist_tab_shows_wishlisted_courses(): void
    {
        $user = $this->student();
        $course = Course::factory()->published()->create();
        $user->update(['wishlist' => [$course->id]]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\StudentDashboard::class)
            ->call('setTab', 'wishlist')
            ->assertSet('activeTab', 'wishlist')
            ->assertSee($course->title);
    }

    public function test_toggle_wishlist_adds_course(): void
    {
        $user = $this->student();
        $course = Course::factory()->published()->create();

        Livewire::actingAs($user)
            ->test(\App\Livewire\StudentDashboard::class)
            ->call('toggleWishlist', $course->id);

        $this->assertContains($course->id, $user->fresh()->wishlist);
    }

    public function test_toggle_wishlist_removes_course(): void
    {
        $user = $this->student();
        $course = Course::factory()->published()->create();
        $user->update(['wishlist' => [$course->id]]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\StudentDashboard::class)
            ->call('toggleWishlist', $course->id);

        $this->assertNotContains($course->id, $user->fresh()->wishlist ?? []);
    }

    public function test_next_lesson_is_first_incomplete_lesson(): void
    {
        $user = $this->student();
        $course = Course::factory()->published()->create();
        $section = Section::factory()->create(['course_id' => $course->id]);
        $lesson1 = Lesson::factory()->create(['section_id' => $section->id, 'order' => 1]);
        $lesson2 = Lesson::factory()->create(['section_id' => $section->id, 'order' => 2]);

        $enrollment = Enrollment::factory()->create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ]);

        // Mark lesson1 complete
        LessonProgress::create([
            'user_id'      => $user->id,
            'lesson_id'    => $lesson1->id,
            'completed_at' => now(),
        ]);

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\StudentDashboard::class);

        // next_lesson should be lesson2 (first incomplete)
        $inProgress = $component->viewData('inProgressEnrollments');
        $this->assertEquals($lesson2->id, $inProgress->first()->next_lesson->id);
    }

    public function test_in_progress_does_not_show_completed_enrollments(): void
    {
        $user = $this->student();
        $course = Course::factory()->published()->create();
        Enrollment::factory()->completed()->create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ]);

        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\StudentDashboard::class);

        $this->assertCount(0, $component->viewData('inProgressEnrollments'));
    }
}
