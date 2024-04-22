<?php

declare(strict_types=1);

namespace App\Services;

use Throwable;
use App\Models\Reply;
use App\Models\Thread;
use App\Repositories\ReplyRepository;
use App\Http\Requests\Reply\CreateRepliesRequest;

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
    public function store(Thread $thread, CreateRepliesRequest $form)
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

    /**
     * Delete the given reply.
     *
     * @param Reply $reply
     * 
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Reply $reply)
    {
        try {

            ReplyRepository::delete($reply);

            if (request()->expectsJson()) {
                return response(['status' => 'Reply deleted']);
            }

            return back();
        } catch (Throwable $e) {
            return back()->withErrors('Unable to delete reply.');
        }
    }

    /**
     * Update an existing reply.
     *
     * @param  Reply $reply
     * @return 
     */
    public function update(Reply $reply)
    {
        try {
            ReplyRepository::update($reply);

            return back()->with('flash', 'Reply updated successfully');

        } catch (Throwable $e) {
            return back()->withErrors('Unable to update reply.');
        }
    }
}
