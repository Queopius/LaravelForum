<?php

namespace App\Http\Controllers\Threads;

use App\Models\Thread;
use Illuminate\Routing\Controller;

class ThreadSubscriptionsController extends Controller
{
    /**
     * Store a new thread subscription.
     *
     * @param int    $channelId
     * @param Thread $thread
     */
    public function store($channelId, Thread $thread)
    {
        $thread->subscribe();
    }

    /**
     * Delete an existing thread subscription.
     *
     * @param int    $channelId
     * @param Thread $thread
     */
    public function destroy($channelId, Thread $thread)
    {
        $thread->unsubscribe();
    }
}
