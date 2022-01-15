<?php

namespace App\Services\Pusher;


use App\Models\Orders;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewOrderForKitchen implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public $orderId)
    {
    }

    public function broadcastOn()
    {
        return ['kitchen-channel'];
    }

    public function broadcastAs()
    {
        return 'newOrderForKitchen';
    }
}
