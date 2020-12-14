<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class SendMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $data = ['asas'];

    public function __construct()
    {
        //
    }


    public function broadcastOn()
    {
        return new Channel('user-channel');
        //return new PrivateChannel('channel-name');
    }

    public function broadcastAs()
    {
        return 'UserEvent';
    }

    public function broadcastWith()
    {
        return ['title'=>'This notification from ItSolutionStuff.com'];
    }
}
