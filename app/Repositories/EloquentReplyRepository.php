<?php

namespace App\Repositories;

use App\Models\{Reply, Thread};
use App\Repositories\Interface\ReplyRepositoryInterface;

class EloquentReplyRepository implements ReplyRepositoryInterface
{
    /**
     * Create a new reply for the given thread.
     *
     * @param  Thread $thread
     * @param  object $form
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create($thread, $form)
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
    public function delete($reply)
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
    public function update($reply): void
    {
        $reply->update(
            request()->validate(['body' => 'required|spamfree'])
        );
    }
}
