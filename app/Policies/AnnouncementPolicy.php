<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Announcement $announcement): bool
    {
        $published = $announcement->published_at !== null
            && $announcement->published_at->isPast();

        return $published || $user->hasRole(User::ROLE_ADMIN);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->hasRole(User::ROLE_ADMIN);
    }
}
