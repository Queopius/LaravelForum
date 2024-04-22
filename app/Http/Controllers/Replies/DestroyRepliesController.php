<?php

declare(strict_types=1);

namespace App\Http\Controllers\Replies;

use App\Models\Reply;
use App\Http\Controllers\Controller;
use App\Services\ReplyService;

final class DestroyRepliesController extends Controller
{
    /**
     * Delete the given reply.
     *
     * @param  Reply $reply
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        return (new ReplyService)->destroy($reply);
    }
}
