<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\MonthlyDues;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Record a payment, split it across the selected months and
     * store any excess as a donation. All or nothing.
     *
     * @param  array{member_profile_id: int, amount: int|float|string, payment_date: string, payment_method: string, monthly_dues_ids: array<int>}  $data
     */
    public function record(array $data, User $recordedBy): Payment
    {
        $remaining = $this->toSatang($data['amount']);

        if ($remaining <= 0) {
            throw ValidationException::withMessages([
                'amount' => ['The amount must be greater than zero.'],
            ]);
        }

        return DB::transaction(function () use ($data, $recordedBy, $remaining) {
            $ids = array_unique($data['monthly_dues_ids']);

            if ($ids === []) {
                throw ValidationException::withMessages([
                    'monthly_dues_ids' => ['Select at least one month for the payment.'],
                ]);
            }

            $dues = MonthlyDues::whereIn('id', $ids)
                ->orderBy('year')
                ->orderBy('month')
                ->lockForUpdate()
                ->get();

            $allBelongToMember = $dues->every(
                fn (MonthlyDues $due) => $due->member_profile_id === (int) $data['member_profile_id']
            );

            if ($dues->count() !== count($ids) || ! $allBelongToMember) {
                throw ValidationException::withMessages([
                    'monthly_dues_ids' => ['Every selected month must exist and belong to this member.'],
                ]);
            }

            $allocations = [];

            foreach ($dues as $due) {
                $alreadyPaid = $this->toSatang(
                    DB::table('payment_monthly_dues')
                        ->where('monthly_dues_id', $due->id)
                        ->sum('allocated_amount')
                );

                $outstanding = $this->toSatang($due->required_amount) - $alreadyPaid;

                if ($outstanding <= 0) {
                    throw ValidationException::withMessages([
                        'monthly_dues_ids' => ["{$due->month}/{$due->year} is already fully paid."],
                    ]);
                }

                $allocate = min($outstanding, $remaining);
                $remaining -= $allocate;

                if ($allocate > 0) {
                    $allocations[$due->id] = [
                        'allocated_amount' => number_format($allocate / 100, 2, '.', ''),
                    ];
                }
            }

            $payment = Payment::create([
                'member_profile_id' => $data['member_profile_id'],
                'recorded_by_user_id' => $recordedBy->id,
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
            ]);

            $payment->monthlyDues()->attach($allocations);

            if ($remaining > 0) {
                Donation::create([
                    'payment_id' => $payment->id,
                    'amount' => number_format($remaining / 100, 2, '.', ''),
                ]);
            }

            return $payment->load('monthlyDues', 'donation');
        });
    }

    /** Work in whole satang (1/100 baht) so money maths never uses floats. */
    private function toSatang(int|float|string|null $value): int
    {
        return (int) round(((float) $value) * 100);
    }
}
