<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /** Any authenticated active user can view the feed. */
    public function viewAny(User $user): bool
    {
        return $user->isActive();
    }

    /** Create community post — any active user. Official post — officer+. */
    public function create(User $user): bool
    {
        return $user->isActive();
    }

    public function createOfficial(User $user): bool
    {
        return $user->isActive() && $user->isOfficerOrAbove();
    }

    /** Only the author can edit their own post (within a reasonable time — enforced in controller). */
    public function update(User $user, Post $post): bool
    {
        return $user->isActive() && $user->id === $post->user_id;
    }

    /** Author can delete own post; officers+ can delete any. */
    public function delete(User $user, Post $post): bool
    {
        if (!$user->isActive()) return false;
        return $user->id === $post->user_id || $user->isOfficerOrAbove();
    }

    /** Officers+ can hide/unhide posts (moderation). */
    public function moderate(User $user): bool
    {
        return $user->isActive() && $user->isOfficerOrAbove();
    }
}
