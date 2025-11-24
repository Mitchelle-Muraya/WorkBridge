<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReadReceiptSent implements ShouldBroadcast
{
    public $jobId, $readerId, $otherId;

    public function __construct($jobId, $readerId, $otherId)
    {
        $this->jobId = $jobId;
        $this->readerId = $readerId;
        $this->otherId = $otherId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("chat.$this->jobId.$this->otherId");
    }

    public function broadcastAs()
    {
        return 'message.read';
    }
}
