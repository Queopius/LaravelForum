<?php

declare(strict_types=1);

namespace App\Http\Controllers\Replies;

use App\Models\Reply;
use App\Models\Thread;
use App\Services\ReplyService;
use App\Http\Requests\Reply\CreateRepliesRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

final class CreateRepliesController
{
    use AuthorizesRequests;

    /**
     * Persist a new reply.
     * 
     * @param  int|null             $channelId
     * @param  \App\Models\Thread   $thread
     * @param  \App\Http\Requests\Reply\CreateRepliesRequest $form
     * 
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store($channelId, Thread $thread, CreateRepliesRequest $form)
    {
        $this->authorize('create', Reply::class);

        return app()->make(ReplyService::class)->store($thread, $form);
    }
}
