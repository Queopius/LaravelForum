<?php

declare(strict_types=1);

namespace App\Services;

use Throwable;
use App\Models\{Reply, Thread};
use App\Http\Requests\Reply\CreateRepliesRequest;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use App\Repositories\Interface\ReplyRepositoryInterface;

final class ReplyService
{
    /**
     * @var ReplyRepositoryInterface
     */
    protected $replyRepository;

    /**
     * Constructor de la clase.
     *
     * @param ReplyRepositoryInterface $replyRepository
     */
    public function __construct(ReplyRepositoryInterface $replyRepository)
    {
        $this->replyRepository = $replyRepository;
    }

    /**
     * Persist a new reply.
     *
     * @param  \App\Models\Thread               $thread
     * @param  \App\Http\Requests\Reply\CreateRepliesRequest $form
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Thread $thread, CreateRepliesRequest $form)
    {
        try {
            if ($thread->locked) {
                return response('Thread is locked', 422);
            }

            return $this->replyRepository->create($thread, $form);

        } catch (Throwable $e) {
            return response()->json(['error' => 'Unable to store reply'], 500);
        }
    }

    /**
     * Delete the given reply.
     *
     * @param Reply $reply
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Reply $reply)
    {
        try {
            $this->replyRepository->delete($reply);

            return $this->responseForDeleteRequest();
        } catch (Throwable $e) {
            return back()->withErrors('Unable to delete reply.');
        }
    }

    /**
     * Update an existing reply.
     *
     * @param  Reply $reply
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Reply $reply)
    {
        try {
            $this->replyRepository->update($reply);

            return $this->responseForUpdateRequest();
        } catch (Throwable $e) {
            return back()->withErrors('Unable to update reply.');
        }
    }

    /**
     * Return a JSON error response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(): JsonResponse
    {
        return response()->json(['error' => 'Unable to store reply'], 500);
    }

    /**
     * Return a response for delete request.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    protected function responseForDeleteRequest()
    {
        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }

    /**
     * Return a response for update request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function responseForUpdateRequest(): RedirectResponse
    {
        return back()->with('flash', 'Reply updated successfully');
    }
}
