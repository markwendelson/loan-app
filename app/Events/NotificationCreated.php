<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification->load('sender');
    }

    public function broadcastOn()
    {
        return new Channel('user.' . $this->notification->notifiable_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'message' => $this->notification->message,
            'link' => $this->notification->link,
            'sender' => $this->notification->sender ? $this->notification->sender->name : null,
            'created_at' => $this->notification->created_at->toDateTimeString(),
        ];
    }

    public function broadcastAs()
    {
        return 'notification.created';
    }
}
