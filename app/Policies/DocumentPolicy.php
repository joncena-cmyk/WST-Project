<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /** Any active officer, adviser, or admin can view the documents hub */
    public function viewAny(User $user): bool
    {
        return $user->isActive() && ($user->isOfficerOrAbove() || $user->isAdviser());
    }

    /** Uploader, officers, adviser, and admin can view a single document */
    public function view(User $user, Document $document): bool
    {
        if (!$user->isActive()) return false;
        if ($user->isOfficerOrAbove() || $user->isAdviser()) return true;
        return $user->id === $document->uploaded_by;
    }

    /** Officers and admin can create/upload documents */
    public function create(User $user): bool
    {
        return $user->isActive() && $user->isOfficerOrAbove();
    }

    /** Uploader or admin can edit when in draft or returned status */
    public function update(User $user, Document $document): bool
    {
        if (!$user->isActive()) return false;
        return $document->isEditable() && ($user->id === $document->uploaded_by || $user->isAdmin());
    }

    /** Uploader or admin can submit when in draft or returned status */
    public function submit(User $user, Document $document): bool
    {
        if (!$user->isActive()) return false;
        return $document->isSubmittable() && ($user->id === $document->uploaded_by || $user->isAdmin());
    }

    /** Only adviser or admin can review submitted / under_review documents */
    public function review(User $user, Document $document): bool
    {
        if (!$user->isActive()) return false;
        return ($user->isAdviser() || $user->isAdmin())
            && in_array($document->status, [Document::STATUS_SUBMITTED, Document::STATUS_UNDER_REVIEW]);
    }

    /** Authorized users who can view can also download/preview */
    public function download(User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }

    /** Uploader or admin can delete drafts */
    public function delete(User $user, Document $document): bool
    {
        if (!$user->isActive()) return false;
        return $document->status === Document::STATUS_DRAFT && ($user->id === $document->uploaded_by || $user->isAdmin());
    }
}
