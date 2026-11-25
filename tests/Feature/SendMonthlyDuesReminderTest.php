<?php

namespace Tests\Feature;

use App\Jobs\SendMonthlyDuesReminder;
use App\Mail\MonthlyDuesReminderMail;
use App\Models\MemberProfile;
use App\Models\MonthlyDues;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendMonthlyDuesReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_emails_the_member_their_outstanding_balance(): void
    {
        Mail::fake();

        $member = MemberProfile::factory()->create();
        $user = $member->user;

        $due = MonthlyDues::factory()->for($member)->create([
            'month' => now()->month,
            'year' => now()->year,
            'required_amount' => 30,
        ]);

        $payment = Payment::factory()->create([
            'member_profile_id' => $member->id,
        ]);

        $payment->monthlyDues()->attach($due->id, [
            'allocated_amount' => 10,
        ]);

        (new SendMonthlyDuesReminder($due->id))->handle();

        Mail::assertSent(
            MonthlyDuesReminderMail::class,
            fn (MonthlyDuesReminderMail $mail) => $mail->hasTo($user->email)
                && str_contains($mail->render(), '฿20.00')
        );
    }

    public function test_it_does_not_email_a_fully_paid_month(): void
    {
        Mail::fake();

        $member = MemberProfile::factory()->create();

        $due = MonthlyDues::factory()->for($member)->create([
            'month' => now()->month,
            'year' => now()->year,
            'required_amount' => 30,
        ]);

        $payment = Payment::factory()->create([
            'member_profile_id' => $member->id,
        ]);

        $payment->monthlyDues()->attach($due->id, [
            'allocated_amount' => 30,
        ]);

        (new SendMonthlyDuesReminder($due->id))->handle();

        Mail::assertNothingSent();
    }
}
