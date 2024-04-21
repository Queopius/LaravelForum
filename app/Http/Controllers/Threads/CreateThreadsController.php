<?php

declare(strict_types=1);

namespace App\Http\Controllers\Threads;

use Illuminate\Http\RedirectResponse;
use App\Services\ThreadService;
use App\Http\Requests\Thread\StoreThreadRequest;

final class CreateThreadsController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreThreadRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreThreadRequest $request): RedirectResponse
    {
        return (new ThreadService)->createThread($request);
    }
}
