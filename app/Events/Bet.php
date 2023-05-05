<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Bet
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $type;

    /**
     * Create a new event instance.
     */
    public function __construct($id, $type)
    {
        $this->id = $id;
        $this->$type = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
        return new Channel('betChannel');
    }
}
