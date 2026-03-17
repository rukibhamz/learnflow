<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CertificateIssuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Certificate $certificate,
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
            'message' => 'Your certificate for "' . ($this->certificate->course->title ?? 'a course') . '" is ready!',
            'certificate_id' => $this->certificate->id,
            'course_id' => $this->certificate->course_id,
            'download_url' => route('certificates.download', $this->certificate->uuid),
            'icon' => 'workspace_premium',
        ];
    }
}
