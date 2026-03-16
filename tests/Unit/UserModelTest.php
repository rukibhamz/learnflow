<?php

namespace Tests\Unit;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_is_suspended_returns_true_when_suspended_at_is_set(): void
    {
        $user = User::factory()->create(['suspended_at' => now()]);
        $this->assertTrue($user->isSuspended());
    }

    public function test_is_suspended_returns_false_when_not_suspended(): void
    {
        $user = User::factory()->create(['suspended_at' => null]);
        $this->assertFalse($user->isSuspended());
    }

    public function test_avatar_url_falls_back_to_ui_avatars_when_no_avatar(): void
    {
        $user = User::factory()->create(['name' => 'Test User', 'avatar' => null]);
        $this->assertStringContainsString('ui-avatars.com', $user->avatar_url);
    }

    public function test_avatar_url_returns_stored_avatar_url(): void
    {
        $user = User::factory()->create(['avatar' => 'https://example.com/avatar.jpg']);
        $this->assertEquals('https://example.com/avatar.jpg', $user->avatar_url);
    }

    public function test_enrolled_course_count_is_zero_by_default(): void
    {
        $user = User::factory()->create();
        $this->assertEquals(0, $user->enrolled_course_count);
    }

    public function test_instructors_scope_returns_only_instructors(): void
    {
        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');

        $student = User::factory()->create();
        $student->assignRole('student');

        $instructors = User::instructors()->get();
        $this->assertTrue($instructors->contains($instructor));
        $this->assertFalse($instructors->contains($student));
    }

    public function test_students_scope_returns_only_students(): void
    {
        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');

        $student = User::factory()->create();
        $student->assignRole('student');

        $students = User::students()->get();
        $this->assertTrue($students->contains($student));
        $this->assertFalse($students->contains($instructor));
    }

    public function test_social_links_are_cast_to_array(): void
    {
        $user = User::factory()->create(['social_links' => ['twitter' => 'https://twitter.com/test']]);
        $this->assertIsArray($user->social_links);
        $this->assertEquals('https://twitter.com/test', $user->social_links['twitter']);
    }
}
