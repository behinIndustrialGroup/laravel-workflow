<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewInboxEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $inbox;

    public function __construct($inbox)
    {
        $this->inbox = $inbox;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->inbox->actor); 
    }

    public function broadcastWith()
    {
        return [
            'case_id' => $this->inbox->case_id,
            'case_name' => $this->inbox->case_name,
        ];
    }
}
