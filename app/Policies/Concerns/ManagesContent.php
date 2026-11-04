<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait ManagesContent
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, mixed $record = null): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canManageContent();
    }

    public function update(User $user, mixed $record = null): bool
    {
        return $user->canManageContent();
    }

    public function delete(User $user, mixed $record = null): bool
    {
        return $user->canManageContent();
    }

    public function deleteAny(User $user): bool
    {
        return $user->canManageContent();
    }

    public function restore(User $user, mixed $record = null): bool
    {
        return $user->canManageContent();
    }

    public function restoreAny(User $user): bool
    {
        return $user->canManageContent();
    }

    public function forceDelete(User $user, mixed $record = null): bool
    {
        return $user->isAdmin();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function reorder(User $user): bool
    {
        return $user->canManageContent();
    }
}
