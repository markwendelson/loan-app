<?php

namespace App\Livewire\Notifications;

use Livewire\Component;

class NotificationsData extends Component
{
    public $notifications = [];
    public $userId;

    public function mount()
    {
        $this->userId = auth()->id();

        $this->notifications = collect(
            auth()->user()->unreadNotifications()->latest()->get()
        );
    }

    public function getListeners()
    {
        return [
            "echo-private:user-notification.{$this->userId},notification.created" => 'handleNewNotification',
        ];
    }

    public function handleNewNotification($payload)
    {
        $this->notifications = $this->notifications->prepend($payload);
    }

    public function render()
    {
        return view('livewire.notifications.notifications-data');
    }
}