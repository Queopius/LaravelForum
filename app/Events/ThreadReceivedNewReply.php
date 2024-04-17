<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ThreadReceivedNewReply
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $thread;
    public $reply;

    /**
     * Create a new event instance.
     *
     * @param $reply
     */
    public function __construct($thread, $reply)
    {
        $this->reply = $reply;
        $this->thread = $thread;
    }
}
