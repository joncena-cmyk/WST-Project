<?php

namespace App\Policies;

use App\Models\PartnerOrganization;
use App\Models\User;

class PartnerOrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    public function view(User $user, PartnerOrganization $partnerOrganization): bool
    {
        return $user->isActive();
    }

    public function create(User $user): bool
    {
        return $user->isOfficerOrAbove();
    }

    public function update(User $user, PartnerOrganization $partnerOrganization): bool
    {
        return $user->isOfficerOrAbove();
    }

    public function delete(User $user, PartnerOrganization $partnerOrganization): bool
    {
        return $user->isPresident() || $user->isAdviser() || $user->isAdmin();
    }
}
