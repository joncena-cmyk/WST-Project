<?php

namespace App\Policies;

use App\Models\Election;
use App\Models\User;

class ElectionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Election $election): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isOfficerOrAbove() || $user->isAdviser() || $user->isAdmin();
    }

    public function update(User $user, Election $election): bool
    {
        return $user->isOfficerOrAbove() || $user->isAdviser() || $user->isAdmin();
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
