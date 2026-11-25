<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\MemberProfile;
use App\Models\MonthlyDues;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReportApiTest extends TestCase
{
    use RefreshDatabase;

    private function createPayment(
        MemberProfile $member,
        User $treasurer,
        string $date,
        float $amount
    ): Payment {
        return Payment::create([
            'member_profile_id' => $member->id,
            'recorded_by_user_id' => $treasurer->id,
            'amount' => $amount,
            'payment_date' => $date,
            'payment_method' => 'cash',
        ]);
    }

    public function test_guest_cannot_access_reports(): void
    {
        $this->getJson('/api/reports/monthly')->assertUnauthorized();
        $this->getJson('/api/reports/annual')->assertUnauthorized();
    }

    public function test_only_treasurer_can_access_reports(): void
    {
        // The default factory role is member.
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/reports/monthly')->assertForbidden();
        $this->getJson('/api/reports/annual')->assertForbidden();
    }

    public function test_monthly_report_returns_payment_dues_and_donation_totals(): void
    {
        $treasurer = User::factory()->treasurer()->create();
        $member = MemberProfile::factory()->create();
        $due = MonthlyDues::factory()->for($member)->create([
            'month' => 11,
            'year' => 2026,
        ]);

        $payment = $this->createPayment($member, $treasurer, '2026-11-25', 50);

        $payment->monthlyDues()->attach($due->id, ['allocated_amount' => 30]);
        Donation::create([
            'payment_id' => $payment->id,
            'amount' => 20,
        ]);

        Sanctum::actingAs($treasurer);

        $response = $this->getJson('/api/reports/monthly?year=2026')
            ->assertOk()
            ->assertJsonPath('year', 2026)
            ->assertJsonCount(12, 'data');

        $this->assertEquals(
            50.0,
            (float) $response->json('data.10.payments_total')
        );
        $this->assertEquals(
            30.0,
            (float) $response->json('data.10.dues_allocated')
        );
        $this->assertEquals(
            20.0,
            (float) $response->json('data.10.donations_total')
        );
    }

    public function test_annual_report_groups_totals_by_payment_year(): void
    {
        $treasurer = User::factory()->treasurer()->create();
        $member = MemberProfile::factory()->create();

        $this->createPayment($member, $treasurer, '2025-06-10', 30);
        $this->createPayment($member, $treasurer, '2026-11-25', 50);

        Sanctum::actingAs($treasurer);

        $response = $this->getJson('/api/reports/annual')->assertOk();

        $this->assertEquals(30.0, (float) $response->json('data.0.payments_total'));
        $this->assertEquals(2025, (int) $response->json('data.0.year'));
        $this->assertEquals(50.0, (float) $response->json('data.1.payments_total'));
        $this->assertEquals(2026, (int) $response->json('data.1.year'));
    }
}
