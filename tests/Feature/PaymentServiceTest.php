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

    private PaymentService $service;

    private User $treasurer;

    private MemberProfile $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(PaymentService::class);
        $this->treasurer = User::factory()->treasurer()->create();
        $this->member = MemberProfile::factory()->create();
    }

    private function dues(int $month, ?MemberProfile $member = null): MonthlyDues
    {
        return MonthlyDues::factory()
            ->for($member ?? $this->member)
            ->create(['month' => $month, 'year' => 2026]);
    }

    private function record(float|int $amount, array $dueIds, ?MemberProfile $member = null): Payment
    {
        return $this->service->record([
            'member_profile_id' => ($member ?? $this->member)->id,
            'amount' => $amount,
            'payment_date' => '2026-11-24',
            'payment_method' => 'cash',
            'monthly_dues_ids' => $dueIds,
        ], $this->treasurer);
    }

    /** @return array<int, float> allocated amount keyed by monthly dues id */
    private function allocations(Payment $payment): array
    {
        return $payment->monthlyDues
            ->mapWithKeys(fn ($due) => [$due->id => (float) $due->pivot->allocated_amount])
            ->all();
    }

    public function test_exact_monthly_amount_pays_the_month_with_no_donation(): void
    {
        $nov = $this->dues(11);

        $payment = $this->record(30, [$nov->id]);

        $this->assertEquals([$nov->id => 30.0], $this->allocations($payment));
        $this->assertNull($payment->donation);
    }

    public function test_extra_money_becomes_a_donation(): void
    {
        $nov = $this->dues(11);

        $payment = $this->record(50, [$nov->id]);

        $this->assertEquals([$nov->id => 30.0], $this->allocations($payment));
        $this->assertEquals(20.0, (float) $payment->donation->amount);
    }

    public function test_a_payment_is_split_across_several_months(): void
    {
        $nov = $this->dues(11);
        $dec = $this->dues(12);

        $payment = $this->record(100, [$nov->id, $dec->id]);

        $this->assertEquals([$nov->id => 30.0, $dec->id => 30.0], $this->allocations($payment));
        $this->assertEquals(40.0, (float) $payment->donation->amount);
    }

    public function test_small_payments_add_up_to_a_paid_month(): void
    {
        $nov = $this->dues(11);

        $first = $this->record(10, [$nov->id]);
        $second = $this->record(20, [$nov->id]);

        $this->assertEquals([$nov->id => 10.0], $this->allocations($first));
        $this->assertEquals([$nov->id => 20.0], $this->allocations($second));
        $this->assertNull($first->donation);
        $this->assertNull($second->donation);
        $this->assertEquals(30.0, (float) $nov->payments()->sum('allocated_amount'));
    }

    public function test_a_fully_paid_month_is_rejected_and_nothing_is_saved(): void
    {
        $nov = $this->dues(11);
        $this->record(30, [$nov->id]);

        try {
            $this->record(30, [$nov->id]);
            $this->fail('Expected a ValidationException for an already paid month.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('monthly_dues_ids', $e->errors());
        }

        $this->assertSame(1, Payment::count());
    }

    public function test_a_month_belonging_to_another_member_is_rejected(): void
    {
        $someoneElse = MemberProfile::factory()->create();
        $theirs = $this->dues(11, $someoneElse);

        try {
            $this->record(30, [$theirs->id]);
            $this->fail('Expected a ValidationException for another member\'s month.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('monthly_dues_ids', $e->errors());
        }

        $this->assertSame(0, Payment::count());
    }

    public function test_a_zero_amount_is_rejected(): void
    {
        $nov = $this->dues(11);

        try {
            $this->record(0, [$nov->id]);
            $this->fail('Expected a ValidationException for a zero amount.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('amount', $e->errors());
        }

        $this->assertSame(0, Payment::count());
    }

    public function test_everything_is_rolled_back_when_one_selected_month_fails(): void
    {
        $nov = $this->dues(11);
        $dec = $this->dues(12);
        $this->record(30, [$nov->id]);

        try {
            $this->record(60, [$dec->id, $nov->id]);
            $this->fail('Expected a ValidationException because November is already paid.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('monthly_dues_ids', $e->errors());
        }

        $this->assertSame(1, Payment::count());
        $this->assertSame(0, $dec->payments()->count());
        $this->assertDatabaseCount('donations', 0);
    }

    public function test_the_payment_is_attributed_to_the_treasurer_who_recorded_it(): void
    {
        $nov = $this->dues(11);

        $payment = $this->record(30, [$nov->id]);

        $this->assertSame($this->treasurer->id, $payment->recorded_by_user_id);
        $this->assertSame($this->member->id, $payment->member_profile_id);
    }
}
