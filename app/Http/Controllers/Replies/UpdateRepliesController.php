<?php

declare(strict_types=1);

namespace App\Http\Controllers\Replies;

use App\Models\Reply;
use App\Services\ReplyService;
use App\Http\Controllers\Controller;

final class UpdateRepliesController extends Controller
{
    /**
     * Delete the given reply.
     *
     * @param  Reply $reply
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        return (new ReplyService)->update($reply);
    }
}
