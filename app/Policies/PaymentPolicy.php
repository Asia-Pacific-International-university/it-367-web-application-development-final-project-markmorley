<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(User::ROLE_ADMIN, User::ROLE_TREASURER);
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasRole(User::ROLE_ADMIN, User::ROLE_TREASURER)) {
            return true;
        }

        // A member may only open their own payment.
        return $user->memberProfile()->value('id') === $payment->member_profile_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(User::ROLE_TREASURER);
    }
}
