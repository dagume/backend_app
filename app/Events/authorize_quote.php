<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class authorize_quote
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $object;
    public $type;
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($object)
    {
        $this->object = $object;
        $this->message = 'XXXX a autorizado la compra de la cotizacion xxxx';
        $this->type = 'supplier';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['status-liked'];
    }
    public function broadcastAs()
    {
        return 'test-statusLiked';
    }
}
