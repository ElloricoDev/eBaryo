<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FeedbackUpdated
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $type; // 'user' or 'admin'

    public function __construct($userId = null, $type = 'user')
    {
        $this->userId = $userId;
        $this->type = $type;
    }
} 