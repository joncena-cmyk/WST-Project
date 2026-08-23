<?php

namespace App\Policies;

use App\Models\EventTask;
use App\Models\User;

class EventTaskPolicy
{
    /** Determine whether the user can view tasks */
    public function view(User $user, EventTask $task): bool
    {
        return $user->isActive();
    }

    /** Determine whether the user can create tasks */
    public function create(User $user): bool
    {
        return $user->isActive() && $user->isOfficerOrAbove();
    }

    /** Determine whether the user can update a task */
    public function update(User $user, EventTask $task): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        if ($user->isOfficerOrAbove()) {
            return true;
        }

        if ($task->committee && $task->committee->isHead($user)) {
            return true;
        }

        // Assigned user can update their own task
        return $task->assigned_to === $user->id;
    }

    /** Determine whether the user can change status of a task */
    public function updateStatus(User $user, EventTask $task): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        if ($user->isOfficerOrAbove()) {
            return true;
        }

        if ($task->committee && $task->committee->isHead($user)) {
            return true;
        }

        return $task->assigned_to === $user->id;
    }

    /** Determine whether the user can delete a task */
    public function delete(User $user, EventTask $task): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        if ($user->isOfficerOrAbove()) {
            return true;
        }

        return $task->committee && $task->committee->isHead($user);
    }
}
