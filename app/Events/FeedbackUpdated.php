<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FeedbackUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $type; // 'user' or 'admin'

    public function __construct($userId = null, $type = 'user')
    {
        $this->userId = $userId;
        $this->type = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        if ($this->type === 'admin') {
            return [new \Illuminate\Broadcasting\Channel('admin-notifications')];
        }
        
        if ($this->userId) {
            return [new \Illuminate\Broadcasting\Channel('user-feedback.' . $this->userId)];
        }
        
        return [];
    }
} 