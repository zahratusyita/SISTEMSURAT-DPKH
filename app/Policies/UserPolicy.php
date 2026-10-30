<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function delete(User $actor, User $target): bool
    {
        return ! $actor->trashed() && ! $target->trashed() && $actor->id !== $target->id;
    }
}
