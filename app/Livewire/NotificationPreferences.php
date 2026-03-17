<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationPreferences extends Component
{
    public bool $emailEnrollment = true;
    public bool $emailCourseComplete = true;
    public bool $emailCertificate = true;
    public bool $emailPromotions = false;

    public function mount(): void
    {
        $prefs = auth()->user()->notification_preferences ?? [];

        $this->emailEnrollment = $prefs['email_enrollment'] ?? true;
        $this->emailCourseComplete = $prefs['email_course_complete'] ?? true;
        $this->emailCertificate = $prefs['email_certificate'] ?? true;
        $this->emailPromotions = $prefs['email_promotions'] ?? false;
    }

    public function save(): void
    {
        $user = auth()->user();
        $user->notification_preferences = [
            'email_enrollment' => $this->emailEnrollment,
            'email_course_complete' => $this->emailCourseComplete,
            'email_certificate' => $this->emailCertificate,
            'email_promotions' => $this->emailPromotions,
        ];
        $user->save();

        session()->flash('notification_saved', 'Notification preferences updated.');
    }

    public function render()
    {
        return view('livewire.notification-preferences');
    }
}
