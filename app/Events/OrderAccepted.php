<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderAccepted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function broadcastOn()
    {
        return new Channel('orders');
    }

    public function broadcastAs()
    {
        return 'order.accepted';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->order->id,
            'status' => $this->order->status,
            'delivery_id' => $this->order->delivery_id,
            'shop_id' => $this->order->shop_id,
            'message' => 'Order #' . $this->order->id . ' has been accepted',
        ];
    }
}
