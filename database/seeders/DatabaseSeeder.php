<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Donation;
use App\Models\MemberProfile;
use App\Models\MonthlyDues;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Builds a demo database: php artisan migrate:fresh --seed
     *
     * Demo login: demo@example.com / password configured by SEED_PASSWORD
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Club Admin',
            'email' => 'admin@example.com',
            'password' => config('app.demo_password'),
        ]);

        User::factory()->treasurer()->create([
            'name' => 'Club Treasurer',
            'email' => 'treasurer@example.com',
            'password' => config('app.demo_password'),
        ]);

        $memberUser = User::factory()->create([
            'name' => 'Demo Member',
            'email' => 'member@example.com',
            'password' => config('app.demo_password'),
        ]);

        $demoProfile = MemberProfile::factory()->for($memberUser)->create([
            'full_name' => 'Demo Member',
            'student_id' => 'S10001',
        ]);

        $profiles = MemberProfile::factory()->count(10)->create();
        $profiles->push($demoProfile);

        foreach ($profiles as $profile) {
            foreach ([9, 10, 11] as $month) {
                MonthlyDues::factory()->for($profile)->create([
                    'month' => $month,
                    'year' => 2026,
                ]);
            }
        }

        $treasurer = User::where('email', 'treasurer@example.com')->first();

        foreach ($profiles as $index => $profile) {
            $dues = MonthlyDues::where('member_profile_id', $profile->id)->get();

            foreach ($dues as $due) {
                // Every third member has not paid November yet.
                if ($due->month === 11 && $index % 3 === 0) {
                    continue;
                }

                // Every second member pays 50 in October: 30 for dues, 20 extra.
                $amount = ($due->month === 10 && $index % 2 === 0) ? 50 : 30;

                $payment = Payment::factory()->create([
                    'member_profile_id' => $profile->id,
                    'recorded_by_user_id' => $treasurer->id,
                    'amount' => $amount,
                    'payment_date' => sprintf('2026-%02d-05', $due->month),
                ]);

                $payment->monthlyDues()->attach($due->id, ['allocated_amount' => 30]);

                if ($amount > 30) {
                    Donation::create([
                        'payment_id' => $payment->id,
                        'amount' => $amount - 30,
                    ]);
                }
            }
        }

        $admin = User::where('email', 'admin@example.com')->first();

        Announcement::factory()->count(3)->create([
            'author_user_id' => $admin->id,
        ]);

        Announcement::factory()->draft()->create([
            'author_user_id' => $admin->id,
            'title' => 'Draft: Foundation Day planning',
        ]);

        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => config('app.demo_password'),
        ]);

    }
}
