<?php

namespace App\Policies;

use App\Models\Committee;
use App\Models\User;

class CommitteePolicy
{
    /** Determine whether the user can view any committees */
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    /** Determine whether the user can view a specific committee */
    public function view(User $user, Committee $committee): bool
    {
        return $user->isActive();
    }

    /** Determine whether the user can create a committee for an event */
    public function create(User $user): bool
    {
        return $user->isActive() && $user->isOfficerOrAbove();
    }

    /** Determine whether the user can update a committee */
    public function update(User $user, Committee $committee): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        return $user->isOfficerOrAbove() || $committee->isHead($user);
    }

    /** Determine whether the user can delete a committee */
    public function delete(User $user, Committee $committee): bool
    {
        return $user->isActive() && $user->isOfficerOrAbove();
    }

    /** Determine whether the user can manage (add/remove) committee members */
    public function manageMembers(User $user, Committee $committee): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        return $user->isOfficerOrAbove() || $committee->isHead($user);
    }
}
