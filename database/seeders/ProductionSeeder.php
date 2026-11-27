<?php

namespace Database\Seeders;

use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['Club Admin', 'admin@example.com', User::ROLE_ADMIN],
            ['Club Treasurer', 'treasurer@example.com', User::ROLE_TREASURER],
            ['Demo Member', 'member@example.com', User::ROLE_MEMBER],
        ];

        foreach ($accounts as [$name, $email, $role]) {
            $user = User::firstOrNew(['email' => $email]);

            $user->forceFill([
                'name' => $name,
                'role' => $role,
                'password' => config('app.demo_password'),
                'email_verified_at' => now(),
            ])->save();
        }

        $member = User::where('email', 'member@example.com')->first();

        MemberProfile::firstOrCreate(
            ['user_id' => $member->id],
            ['full_name' => 'Demo Member', 'student_id' => 'S10001'],
        );
    }
}
