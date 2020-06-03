<?php

namespace App\Events;

use App\Quotation;
use App\Order;
use App\Project;
use App\User;
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
    public $title;
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Quotation $object)
    {
        $user = auth()->user();
        //dd($object, $user);
        $this->object = $object;
        $order = Order::where('id', $object->order_id)->first();
        $project = Project::where('id', $order->project_id)->first();
        $provider = User::where('id', $object->contact_id)->first();
        //$this->title = $project->name;
        $this->message = "{$user->name} a autorizado comprar al proveedor {$provider->name}, {$order->name}.";
        //$this->type = 'supplier';
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
