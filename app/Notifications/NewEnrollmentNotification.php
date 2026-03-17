<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEnrollmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Enrollment $enrollment,
    ) {
        $this->onQueue('notifications');
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => ($this->enrollment->user->name ?? 'A student') . ' enrolled in ' . ($this->enrollment->course->title ?? 'your course'),
            'enrollment_id' => $this->enrollment->id,
            'course_id' => $this->enrollment->course_id,
            'student_name' => $this->enrollment->user->name ?? '',
            'icon' => 'person_add',
        ];
    }
}
