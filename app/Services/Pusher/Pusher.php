<?php

namespace App\Services\Pusher;


use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Pusher implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderId;
    public $newStatusId;
    public $oldStatusId;

    public function __construct($orderId, $oldStatusId, $newStatusId)
    {
        $this->orderId = $orderId;
        $this->oldStatusId = $oldStatusId;
        $this->newStatusId = $newStatusId;
    }

    public function broadcastOn()
    {
        return ['manager-channel'];
    }

    public function broadcastAs()
    {
        return 'updateStatuses';
    }
}
