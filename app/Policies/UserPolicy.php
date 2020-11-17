<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the given profile.
     *
     * @param  \App\Models\User $signedInUser
     * @param  \App\Models\User $user
     * @return boolean
     */
    public function update(User $signedInUser, User $user)
    {
        return $signedInUser->id === $user->id;
    }
}
