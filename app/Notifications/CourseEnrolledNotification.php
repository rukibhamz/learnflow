<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseEnrolledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Enrollment $enrollment)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $course = $this->enrollment->course;
        
        return (new MailMessage)
            ->subject('Enrollment Confirmation: ' . $course->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have successfully enrolled in the course: ' . $course->title)
            ->action('Go to Course', url('/courses/' . $course->slug))
            ->line('Happy learning!');
    }
}
