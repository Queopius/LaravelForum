<?php


declare(strict_types=1);

namespace App\Services;

use Throwable;
use App\Models\Thread;
use App\Http\Requests\Reply\CreateRepliesRequest;
use App\Repositories\ReplyRepository;

class ReplyService
{
    /**
     * Persist a new reply.
     * 
     * @param  int                  $channelId
     * @param  Thread               $thread
     * @param  CreateRepliesRequest $form
     * 
     * @return \Illuminate\Http\Response|\Illuminate\Database\Eloquent\Model
     */
    public function addReply(Thread $thread, CreateRepliesRequest $form)
    {
        try {

            if ($thread->locked) {
                return response('Thread is locked', 422);
            }

            return ReplyRepository::create($thread, $form);

        } catch (Throwable $e) {
            return response()->json(['error' => 'Unable to store reply'], 500);
        }
    }
}
