<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ThreadRepository;

class ThreadService
{
    /**
     * Create a new thread.
     *
     * @param  array  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function createThread($request)
    {
        try {
            $thread = ThreadRepository::create($request);

            if (request()->wantsJson()) {
                return response($thread, 201);
            }

            return redirect($thread->path())
                ->with('flash', 'Your thread has been published!');

        } catch (\Throwable $th) {
            return redirect()->route('threads.create')
                ->withErrors([
                    'thread_creation' => 'Failed to create thread. Please check your inputs and try again.'
                ])
                ->withInput();
        }
    }
}
