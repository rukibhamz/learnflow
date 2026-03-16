<?php

namespace Tests\Feature;

use App\Events\CourseCompleted;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateIssuanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_completed_creates_certificate_and_pdf_on_s3(): void
    {
        Storage::fake('s3');
        config(['queue.default' => 'sync']);

        $user = User::factory()->create();
        $course = Course::factory()->published()->create();

        event(new CourseCompleted($user, $course, now()));

        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $this->assertNotNull($certificate);

        Storage::disk('s3')->assertExists('certificates/' . $certificate->uuid . '.pdf');
    }
}

