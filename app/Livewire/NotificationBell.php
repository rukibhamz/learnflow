<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public $unreadCount = 3;

    public function markAllRead(): void
    {
        $this->unreadCount = 0;
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
