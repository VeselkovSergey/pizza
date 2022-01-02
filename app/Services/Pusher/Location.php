<?php

namespace App\Services\Pusher;


use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Location implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lat;
    public $lon;

    public function __construct($lat, $lon)
    {
        $this->lat = $lat;
        $this->lon = $lon;
    }

    public function broadcastOn()
    {
        return ['location-channel'];
    }

    public function broadcastAs()
    {
        return 'updateLocation';
    }
}
