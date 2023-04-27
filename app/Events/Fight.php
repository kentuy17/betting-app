<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Fight implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $fight;

    /**
     * Create a new event instance.
     */
    public function __construct($fight)
    {
        $this->fight = $fight;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('fight');
    }

    public function broadcastAs()
    {
        return 'fight';
    }
}
