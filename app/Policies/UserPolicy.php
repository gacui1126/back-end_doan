<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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

    public function view(?User $user){
        if($user->can('list user')) {
            return true;
        }
    }


    public function delete(User $user){
        if($user->can('delete user')) {
            return true;
        }
    }
}
