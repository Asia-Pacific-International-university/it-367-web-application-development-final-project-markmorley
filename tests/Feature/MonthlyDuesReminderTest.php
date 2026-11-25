<?php

namespace Tests\Feature;

use App\Jobs\SendMonthlyDuesReminder;
use App\Models\MemberProfile;
use App\Models\MonthlyDues;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MonthlyDuesReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_queues_only_current_month_dues_with_an_outstanding_balance(): void
    {
        Queue::fake();

        $this->travelTo(now()->startOfMonth());

        $member = MemberProfile::factory()->create();

        $unpaid = MonthlyDues::factory()->for($member)->create([
            'month' => now()->month,
            'year' => now()->year,
            'required_amount' => 30,
        ]);

        $paidMember = MemberProfile::factory()->create();
        $paid = MonthlyDues::factory()->for($paidMember)->create([
            'month' => now()->month,
            'year' => now()->year,
            'required_amount' => 30,
        ]);

        $oldMonth = MonthlyDues::factory()->for($member)->create([
            'month' => now()->subMonth()->month,
            'year' => now()->subMonth()->year,
            'required_amount' => 30,
        ]);

        $payment = Payment::factory()->create([
            'member_profile_id' => $paidMember->id,
        ]);

        $payment->monthlyDues()->attach($paid->id, [
            'allocated_amount' => 30,
        ]);

        Artisan::call('dues:send-reminders');

        Queue::assertPushed(SendMonthlyDuesReminder::class, 1);
        Queue::assertPushed(
            SendMonthlyDuesReminder::class,
            fn (SendMonthlyDuesReminder $job) => $job->monthlyDuesId === $unpaid->id
        );
        Queue::assertNotPushed(
            SendMonthlyDuesReminder::class,
            fn (SendMonthlyDuesReminder $job) => $job->monthlyDuesId === $paid->id
        );
        Queue::assertNotPushed(
            SendMonthlyDuesReminder::class,
            fn (SendMonthlyDuesReminder $job) => $job->monthlyDuesId === $oldMonth->id
        );
    }
}
