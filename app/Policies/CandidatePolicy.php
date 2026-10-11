<?php

namespace App\Policies;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\User;

class CandidatePolicy
{
    public function apply(User $user, Election $election): bool
    {
        return $election->isFilingOpen() && !$election->hasUserApplied($user) && $user->isActive();
    }

    public function review(User $user): bool
    {
        return $user->isOfficerOrAbove();
    }
}
