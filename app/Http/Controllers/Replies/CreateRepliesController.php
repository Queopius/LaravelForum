<?php

declare(strict_types=1);

namespace App\Http\Controllers\Replies;

use App\Models\Reply;
use App\Models\Thread;
use App\Services\ReplyService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reply\CreateRepliesRequest;

final class CreateRepliesController extends Controller
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
    public function store($channelId, Thread $thread, CreateRepliesRequest $form)
    {
        $this->authorize('create', Reply::class);

        return (new ReplyService)->store($thread, $form);
    }
}
