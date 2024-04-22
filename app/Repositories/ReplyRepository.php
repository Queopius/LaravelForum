<?php

namespace App\Repositories;

use App\Models\Reply;
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

    /**
     * Delete the given reply.
     *
     * @param  Reply $reply
     * 
     * @return void
    */
    public static function delete(Reply $reply)
    {
        $reply->delete();
    }

    /**
     * Update an existing reply.
     *
     * @param  Reply $reply
     * 
     * @return void
     */
    public static function update(Reply $reply): void
    {
        $reply->update(
            request()->validate(['body' => 'required|spamfree'])
        );
    }
}
