<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserTyping implements ShouldBroadcast
{
    public $jobId, $senderId, $receiverId;

    public function __construct($jobId, $senderId, $receiverId)
    {
        $this->jobId = $jobId;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("chat.$this->jobId.$this->receiverId");
    }

    public function broadcastAs()
    {
        return 'typing';
    }
}
