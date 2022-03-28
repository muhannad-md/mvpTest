<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $user_model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $user_model)
    {
        return $user->id === $user_model->id ? Response::allow() : Response::deny();
    }
}
