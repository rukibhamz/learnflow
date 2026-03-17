<?php

namespace App\Livewire;

use Livewire\Component;

class PushNotificationToggle extends Component
{
    public bool $isSubscribed = false;

    public function mount(): void
    {
        $this->isSubscribed = auth()->user()
            ->pushSubscriptions()
            ->exists();
    }

    public function render()
    {
        return view('livewire.push-notification-toggle');
    }
}
