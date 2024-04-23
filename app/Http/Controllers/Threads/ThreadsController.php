<?php

namespace App\Http\Controllers\Threads;

use App\Filters\ThreadFilters;
use App\Http\Controllers\Controller;
use App\Models\{Channel, Thread, Trending};

class ThreadsController extends Controller
{
    /**
     * Create a new ThreadsController instance.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Channel        $channel
     * @param \App\Models\Trending       $trending
     * @param \App\Filters\ThreadFilters $filters
     *
     * @return \Illuminate\View\View
     */
    public function index(Channel $channel, ThreadFilters $filters, Trending $trending)
    {
        $threads = $this->getThreads($channel, $filters);

        if (request()->wantsJson()) {
            return $threads;
        }

        return view('threads.index', [
            'threads' => $threads,
            'trending' => $trending->get()
        ]);
    }

    /**
     * Fetch all relevant threads.
     *
     * @param \App\Models\Channel       $channel
     * @param \App\Filters\ThreadFilters $filters
     *
     * @return mixed
     */
    protected function getThreads(Channel $channel, ThreadFilters $filters)
    {
        $threads = Thread::latest()->filter($filters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        return $threads->latest()->paginate(25);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int                 $channel
     * @param  \App\Models\Thread  $thread
     * @param \App\Models\Trending $trending
     *
     * @return \Illuminate\View\View
     */
    public function show($channel, Thread $thread, Trending $trending)
    {
        if (auth()->check()) {
            auth()->user()->read($thread);
        }

        $trending->push($thread);

        $thread->increment('visits');

        return view('threads.show', compact('thread'));
    }

    /**
     * Update the given thread.
     *
     * @param string $channel
     * @param \App\Models\Thread $thread
     */
    public function update($channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->update(request()->validate([
            'title' => 'required',
            'body' => 'required'
        ]));

        return $thread;
    }

    /**
     * Delete the given thread.
     *
     * @param        $channel
     * @param \App\Models\Thread $thread
     * @return mixed
     */
    public function destroy($channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();

        if (request()->wantsJson()) {
            return response([], 204);
        }

        return redirect('/threads');
    }
}
