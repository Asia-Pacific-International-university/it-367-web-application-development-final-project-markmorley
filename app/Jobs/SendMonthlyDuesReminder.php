<?php

namespace App\Jobs;

use App\Mail\MonthlyDuesReminderMail;
use App\Models\MonthlyDues;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMonthlyDuesReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $monthlyDuesId) {}

    public function handle(): void
    {
        $due = MonthlyDues::with('memberProfile.user')
            ->find($this->monthlyDuesId);

        if (! $due || ! $due->memberProfile || ! $due->memberProfile->user) {
            return;
        }

        $alreadyPaid = (int) round(
            (float) $due->payments()->sum('payment_monthly_dues.allocated_amount') * 100
        );

        $required = (int) round((float) $due->required_amount * 100);
        $outstanding = $required - $alreadyPaid;

        if ($outstanding <= 0) {
            return;
        }

        $user = $due->memberProfile->user;

        Mail::to($user->email)->send(
            new MonthlyDuesReminderMail(
                memberName: $user->name,
                month: $due->month,
                year: $due->year,
                outstandingAmount: number_format($outstanding / 100, 2),
            )
        );
    }
}
