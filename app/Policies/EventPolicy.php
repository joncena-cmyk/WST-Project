<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /** Any authenticated user can browse published events */
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    public function view(User $user, Event $event): bool
    {
        // Published events visible to all active users
        if ($event->isPublic()) return $user->isActive();

        // Officers can see their own drafts/submitted events; admin sees all
        if ($user->isOfficerOrAbove()) return true;

        return $user->id === $event->created_by;
    }

    /** Only officers+ can create events */
    public function create(User $user): bool
    {
        return $user->isActive() && $user->isOfficerOrAbove();
    }

    /** Creator or admin can edit; only in editable statuses */
    public function update(User $user, Event $event): bool
    {
        if (!$user->isActive()) return false;
        $editableStatuses = [Event::STATUS_DRAFT, Event::STATUS_RETURNED];
        if (!in_array($event->status, $editableStatuses)) return false;
        return $user->id === $event->created_by || $user->isAdmin();
    }

    /** Creator or admin can submit for review */
    public function submit(User $user, Event $event): bool
    {
        if (!$user->isActive()) return false;
        return in_array($event->status, [Event::STATUS_DRAFT, Event::STATUS_RETURNED])
            && ($user->id === $event->created_by || $user->isAdmin());
    }

    /** Creator or admin can publish after approval */
    public function publish(User $user, Event $event): bool
    {
        if (!$user->isActive()) return false;
        return $event->status === Event::STATUS_APPROVED
            && ($user->id === $event->created_by || $user->isAdmin());
    }

    /** Creator, officers, admin can cancel */
    public function cancel(User $user, Event $event): bool
    {
        if (!$user->isActive()) return false;
        $cancellable = [Event::STATUS_PUBLISHED, Event::STATUS_APPROVED, Event::STATUS_SUBMITTED];
        return in_array($event->status, $cancellable)
            && ($user->id === $event->created_by || $user->isOfficerOrAbove());
    }

    /** Only adviser reviews */
    public function review(User $user, Event $event): bool
    {
        return $user->isActive()
            && ($user->isAdviser() || $user->isAdmin())
            && in_array($event->status, [Event::STATUS_SUBMITTED, Event::STATUS_UNDER_REVIEW]);
    }

    /** Officers+ see participants */
    public function viewParticipants(User $user, Event $event): bool
    {
        return $user->isActive() && ($user->isOfficerOrAbove() || $user->id === $event->created_by);
    }
}
