<?php

namespace App\Jobs;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IssueCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public int $courseId,
        public \DateTimeInterface $completedAt,
    ) {
    }

    public function handle(): void
    {
        // Idempotent: do nothing if certificate already exists
        $existing = Certificate::where('user_id', $this->userId)
            ->where('course_id', $this->courseId)
            ->first();

        if ($existing) {
            return;
        }

        $user = User::findOrFail($this->userId);
        $course = Course::with('instructor')->findOrFail($this->courseId);

        $uuid = (string) Str::uuid();

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'uuid' => $uuid,
            'issued_at' => now(),
        ]);

        $template = $course->certificateTemplate
            ?? CertificateTemplate::getDefault();

        $templateData = [
            'student_name' => $user->name,
            'course_title' => $course->title,
            'instructor_name' => $course->instructor?->name ?? 'Instructor',
            'issued_date' => now()->format('F j, Y'),
            'certificate_uuid' => $certificate->uuid,
            'verify_url' => route('certificates.verify', $certificate->uuid),
        ];

        if ($template) {
            $html = $template->render($templateData);
            $pdf = Pdf::loadHTML($html)
                ->setPaper($template->paper_size, $template->orientation)
                ->setOption('dpi', 150);
        } else {
            $pdf = Pdf::loadView('certificates.default', [
                'studentName' => $user->name,
                'courseTitle' => $course->title,
                'instructorName' => $course->instructor?->name ?? 'Instructor',
                'completionDate' => $this->completedAt,
                'uuid' => $certificate->uuid,
                'siteName' => config('app.name', 'LearnFlow'),
            ])->setPaper('a4', 'landscape')
                ->setOption('dpi', 150);
        }

        $path = 'certificates/' . $certificate->uuid . '.pdf';

        Storage::disk('s3')->put($path, $pdf->output(), [
            'visibility' => 'private',
            'ContentType' => 'application/pdf',
        ]);

        SendCertificateEmail::dispatch($certificate->id);

        $user->notify(new \App\Notifications\CertificateIssuedNotification($certificate));
    }
}

