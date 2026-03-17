<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    public function markAllRead(): void
    {
        auth()->user()?->unreadNotifications->markAsRead();
        $this->unreadCount = 0;
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = auth()->user()?->notifications()->find($notificationId);
        $notification?->markAsRead();
        $this->refreshCount();
    }

    protected function refreshCount(): void
    {
        $this->unreadCount = auth()->user()?->unreadNotifications()->count() ?? 0;
    }

    public function render()
    {
        $notifications = auth()->user()
            ? auth()->user()->notifications()->latest()->take(10)->get()
            : collect();

        return view('livewire.notification-bell', [
            'notifications' => $notifications,
        ]);
    }
}
