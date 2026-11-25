<?php

namespace App\Policies;

use App\Models\MonthlyDues;
use App\Models\User;

class MonthlyDuesPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_TREASURER);
    }

    public function view(User $user, MonthlyDues $monthlyDues): bool
    {
        if ($user->hasRole(User::ROLE_ADMIN, User::ROLE_TREASURER)) {
            return true;
        }

        return $user->memberProfile()->value('id') === $monthlyDues->member_profile_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(User::ROLE_TREASURER);
    }

    public function update(User $user, MonthlyDues $monthlyDues): bool
    {
        return $user->hasRole(User::ROLE_TREASURER);
    }

    public function delete(User $user, MonthlyDues $monthlyDues): bool
    {
        return $user->hasRole(User::ROLE_TREASURER);
    }
}
