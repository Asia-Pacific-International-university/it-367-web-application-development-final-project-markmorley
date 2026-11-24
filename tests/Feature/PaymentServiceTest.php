<?php

namespace Tests\Feature;

use App\Models\MemberProfile;
use App\Models\MonthlyDues;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_is_allocated_and_excess_becomes_a_donation(): void
    {
        $treasurer = User::factory()->treasurer()->create();
        $member = MemberProfile::factory()->create();
        $november = MonthlyDues::factory()->for($member)->create([
            'month' => 11,
            'year' => 2026,
        ]);

        $payment = app(PaymentService::class)->record([
            'member_profile_id' => $member->id,
            'amount' => 50,
            'payment_date' => '2026-11-24',
            'payment_method' => 'cash',
            'monthly_dues_ids' => [$november->id],
        ], $treasurer);

        $this->assertSame('20.00', $payment->donation->amount);
        $this->assertSame(30.0, (float) $payment->monthlyDues->first()->pivot->allocated_amount);
        $this->assertDatabaseHas('payment_monthly_dues', [
            'payment_id' => $payment->id,
            'monthly_dues_id' => $november->id,
            'allocated_amount' => '30.00',
        ]);
    }

    public function test_partial_payment_has_no_donation_and_fully_paid_month_is_rejected(): void
    {
        $treasurer = User::factory()->treasurer()->create();
        $member = MemberProfile::factory()->create();
        $november = MonthlyDues::factory()->for($member)->create([
            'month' => 11,
            'year' => 2026,
        ]);
        $service = app(PaymentService::class);
        $data = [
            'member_profile_id' => $member->id,
            'amount' => 10,
            'payment_date' => '2026-11-24',
            'payment_method' => 'cash',
            'monthly_dues_ids' => [$november->id],
        ];

        $partialPayment = $service->record($data, $treasurer);

        $this->assertNull($partialPayment->donation);
        $this->assertSame(10.0, (float) $partialPayment->monthlyDues->first()->pivot->allocated_amount);

        $fullPayment = $service->record(array_merge($data, ['amount' => 20]), $treasurer);

        $this->assertSame(20.0, (float) $fullPayment->monthlyDues->first()->pivot->allocated_amount);

        try {
            $service->record(array_merge($data, ['amount' => 1]), $treasurer);
            $this->fail('A fully paid month should be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('monthly_dues_ids', $exception->errors());
        }

        $this->assertSame(2, Payment::count());
    }

    public function test_selected_dues_must_exist_and_belong_to_the_member(): void
    {
        $treasurer = User::factory()->treasurer()->create();
        $member = MemberProfile::factory()->create();
        $otherMemberDue = MonthlyDues::factory()->create();

        try {
            app(PaymentService::class)->record([
                'member_profile_id' => $member->id,
                'amount' => 30,
                'payment_date' => '2026-11-24',
                'payment_method' => 'cash',
                'monthly_dues_ids' => [$otherMemberDue->id],
            ], $treasurer);

            $this->fail('Dues belonging to another member should be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('monthly_dues_ids', $exception->errors());
        }

        $this->assertSame(0, Payment::count());
        $this->assertDatabaseCount('payment_monthly_dues', 0);
    }

    public function test_at_least_one_month_must_be_selected(): void
    {
        $treasurer = User::factory()->treasurer()->create();
        $member = MemberProfile::factory()->create();

        try {
            app(PaymentService::class)->record([
                'member_profile_id' => $member->id,
                'amount' => 30,
                'payment_date' => '2026-11-24',
                'payment_method' => 'cash',
                'monthly_dues_ids' => [],
            ], $treasurer);

            $this->fail('A payment must select at least one month.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('monthly_dues_ids', $exception->errors());
        }

        $this->assertSame(0, Payment::count());
    }
}
