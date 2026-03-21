<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to LearnFlow!')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Welcome to our learning platform. We are excited to have you on board.')
            ->action('Start Learning', url('/dashboard'))
            ->line('If you have any questions, feel free to contact our support team.');
    }
}
