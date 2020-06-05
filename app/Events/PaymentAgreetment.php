<?php

namespace App\Events;

use App\PaymentAgreement;
use App\Order;
use App\Project;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PaymentAgreetment implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $paymentAgreement;
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PaymentAgreement $pay)
    {
        $this->paymentAgreement = $pay;
        $order = Order::where('id', $pay->order_id)->first();
        $project = Project::where('id', $order->project_id)->first();
        $this->message = "Proyecto {$project->name}, Pendiente a pagar esta semana {$pay->amount} en ordenes de compra";
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
