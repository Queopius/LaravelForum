<?php

namespace App\Listeners;

use App\Events\ThreadReceivedNewReply;

class NotifyThreadSubscribers
{
    /**
     * Handle the event.
     *
     * @param  ThreadReceivedNewReply $event
     * @return void
     */
    public function handle(ThreadReceivedNewReply $event)
    {
        $event->thread->notifySubscribers($event->reply);
    }
}
