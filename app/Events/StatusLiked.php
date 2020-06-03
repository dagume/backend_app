<?php

namespace App\Events;

use App\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class StatusLiked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $object;

    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Product $object)
    {
        $user = auth()->user();
        $this->object = $object;
        $this->message = "{$user->name} creó un {$object->name}";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
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
