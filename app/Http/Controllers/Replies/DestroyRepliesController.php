<?php

declare(strict_types=1);

namespace App\Http\Controllers\Replies;

use App\Models\Reply;
use App\Services\ReplyService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

final class DestroyRepliesController
{
    use AuthorizesRequests;

    /**
     * Delete the given reply.
     *
     * @param  Reply $reply
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        return app()->make(ReplyService::class)->destroy($reply);
    }
}
