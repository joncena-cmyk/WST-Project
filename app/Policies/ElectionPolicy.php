<?php

namespace App\Policies;

use App\Models\Election;
use App\Models\User;

class ElectionPolicy
{
    /** Any active user can list elections */
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    /** Any active user can view a single election */
    public function view(User $user, Election $election): bool
    {
        return $user->isActive();
    }

    public function create(User $user): bool
    {
        return $user->isOfficerOrAbove();
    }

    public function update(User $user, Election $election): bool
    {
        return $user->isOfficerOrAbove();
    }

    public function delete(User $user, Election $election): bool
    {
        return $user->isPresident() || $user->isAdviser() || $user->isAdmin();
    }

    public function vote(User $user, Election $election): bool
    {
        return $election->isVotingOpen() && !$election->hasUserVoted($user) && $user->isActive();
    }
}
