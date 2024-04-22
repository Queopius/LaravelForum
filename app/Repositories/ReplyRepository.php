<?php

namespace App\Repositories;

use App\Models\Thread;
use Illuminate\Database\Eloquent\Model;

class ReplyRepository
{
    /**
     * Create a new reply for the given thread.
     *
     * @param  Model  $thread
     * @param  object $form
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function create(Thread $thread, $form)
    {
        return $thread->addReply(
            [
                'body' => $form->body,
                'user_id' => auth()->id()
            ]
        )->load('owner');
    }
}
