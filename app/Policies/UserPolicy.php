<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function create(User $actor): bool
    {
        return $actor->is_admin;
    }

    public function delete(User $actor, User $target): bool
    {
        return $actor->is_admin && $actor->id !== $target->id;
    }
}
