<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function create(User $user): bool
    {
        return $user->isActive();
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->isActive() && $user->id === $comment->user_id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        if (!$user->isActive()) return false;
        return $user->id === $comment->user_id || $user->isOfficerOrAbove();
    }

    public function moderate(User $user): bool
    {
        return $user->isActive() && $user->isOfficerOrAbove();
    }
}
