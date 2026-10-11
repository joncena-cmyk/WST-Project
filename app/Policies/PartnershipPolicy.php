<?php

namespace App\Policies;

use App\Models\Partnership;
use App\Models\User;

class PartnershipPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    public function view(User $user, Partnership $partnership): bool
    {
        return $user->isActive();
    }

    public function create(User $user): bool
    {
        return $user->isOfficerOrAbove();
    }

    public function update(User $user, Partnership $partnership): bool
    {
        return $user->isOfficerOrAbove();
    }

    public function delete(User $user, Partnership $partnership): bool
    {
        return $user->isPresident() || $user->isAdviser() || $user->isAdmin();
    }

    public function review(User $user): bool
    {
        return $user->isAdviser() || $user->isPresident() || $user->isAdmin();
    }
}
