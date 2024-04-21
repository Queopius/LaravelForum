<?php 

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ThreadRepository;

class ThreadService
{
    public function createThread($request)
    {
        /* if (empty($request['title'])) {
            return "Faltan datos";
        } */
        $thread = ThreadRepository::create($request);

        if (request()->wantsJson()) {
            return response($thread, 201);
        }

        if ($thread) {
            return redirect($thread->path())
                ->with('flash', 'Your thread has been published!');
        } else {
            return redirect()->route('threads.create')
                ->withErrors(['thread_creation' => 'Failed to create thread. Please check your inputs and try again.'])
                ->withInput();
        }
    }
}
