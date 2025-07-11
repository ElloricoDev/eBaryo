<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FeedbackUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $type; // 'user' or 'admin'

    public function __construct($userId = null, $type = 'user')
    {
        $this->userId = $userId;
        $this->type = $type;
    }

    public function broadcastOn()
    {
        if ($this->type === 'admin') {
            return new Channel('admin-feedback');
        }
        return new PrivateChannel('user-feedback.' . $this->userId);
    }
} 