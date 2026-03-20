<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public bool $showDropdown = false;
    public int $unreadCount = 0;

    protected $listeners = ['notificationRead' => 'refreshCount'];

    public function mount(): void
    {
        $this->refreshCount();
    }

    public function refreshCount(): void
    {
        $this->unreadCount = auth()->user()->unreadBroadcastCount();
    }

    public function toggleDropdown(): void
    {
        $this->showDropdown = ! $this->showDropdown;
    }

    public function markAsRead(int $notificationId): void
    {
        auth()->user()->broadcastNotifications()
            ->updateExistingPivot($notificationId, ['read_at' => now()]);

        $this->refreshCount();
    }

    public function markAllRead(): void
    {
        auth()->user()->broadcastNotifications()
            ->whereNull('broadcast_notification_user.read_at')
            ->each(function ($notification) {
                auth()->user()->broadcastNotifications()
                    ->updateExistingPivot($notification->id, ['read_at' => now()]);
            });

        $this->refreshCount();
    }

    public function render()
    {
        $notifications = auth()->user()
            ->broadcastNotifications()
            ->with('sender')
            ->take(10)
            ->get();

        return view('livewire.notification-bell', [
            'notifications' => $notifications,
        ]);
    }
}
