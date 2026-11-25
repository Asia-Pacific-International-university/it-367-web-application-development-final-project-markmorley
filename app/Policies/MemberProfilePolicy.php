<?php

namespace App\Policies;

use App\Models\MemberProfile;
use App\Models\User;

class MemberProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_TREASURER);
    }

    public function view(User $user, MemberProfile $memberProfile): bool
    {
        if ($user->hasRole(User::ROLE_ADMIN, User::ROLE_TREASURER)) {
            return true;
        }

        return $user->memberProfile()->value('id') === $memberProfile->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_TREASURER);
    }

    public function update(User $user, MemberProfile $memberProfile): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_TREASURER);
    }

    public function delete(User $user, MemberProfile $memberProfile): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_TREASURER);
    }
}
