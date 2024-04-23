<?php

namespace App\Repositories;

use App\Models\Thread;

class ThreadRepository
{
    public static function create($request)
    {
        return Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => $request->channel_id,
            'title' => $request->title,
            'body' => $request->body
        ]);
    }
}
