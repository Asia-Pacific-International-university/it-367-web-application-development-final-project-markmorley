<?php

namespace App\Console\Commands;

use App\Jobs\SendMonthlyDuesReminder;
use App\Models\MonthlyDues;
use Illuminate\Console\Command;

class SendMonthlyDuesReminders extends Command
{
    protected $signature = 'dues:send-reminders';

    protected $description = 'Queue reminders for members with outstanding dues this month';

    public function handle(): int
    {
        $month = now()->month;
        $year = now()->year;

        $dueIds = MonthlyDues::query()
            ->where('month', $month)
            ->where('year', $year)
            ->whereHas('memberProfile.user')
            ->whereRaw(
                'required_amount > (
                    select COALESCE(SUM(allocated_amount), 0)
                    from payment_monthly_dues
                    where payment_monthly_dues.monthly_dues_id = monthly_dues.id
                )'
            )
            ->pluck('id');

        foreach ($dueIds as $dueId) {
            SendMonthlyDuesReminder::dispatch($dueId);
        }

        $this->info("Queued {$dueIds->count()} dues reminder(s).");

        return self::SUCCESS;
    }
}
