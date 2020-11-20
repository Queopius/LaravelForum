<?php

namespace App\Policies;

use App\Models\{Thread, User};
use Illuminate\Auth\Access\HandlesAuthorization;

class ThreadPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the thread.
     *
     * @param  \App\Models\User   $user
     * @param  \App\Models\Thread $thread
     * @return mixed
     */
    public function update(User $user, Thread $thread)
    {
        return $user->is($thread->creator);
    }
}
