<?php

namespace App\Models;

class Trending
{
    /**
     * Fetch all trending threads.
     *
     * @return array
     */
    public function get()
    {
        return array_map(
            'json_decode',
            app()->make('redis')->zrevrange($this->cacheKey(), 0, 4)
        );
    }

    /**
     * Push a new thread to the trending list.
     *
     * @param Thread $thread
     */
    public function push($thread)
    {
        app()->make('redis')->zincrby($this->cacheKey(), 1, json_encode([
            'title' => $thread->title,
            'path' => $thread->path()
        ]));
    }

    /**
     * Get the cache key name.
     *
     * @return string
     */
    public function cacheKey()
    {
        return app()->environment('testing')
            ? 'testing_trending_threads'
            : 'trending_threads';
    }

    /**
     * Reset all trending threads.
     */
    public function reset()
    {
        app()->make('redis')->del($this->cacheKey());
    }
}
