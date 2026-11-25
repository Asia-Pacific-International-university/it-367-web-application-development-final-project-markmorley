<?php

namespace Tests\Feature;

use App\Models\MemberProfile;
use App\Models\MonthlyDues;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MonthlyDuesApiTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsTreasurer(): User
    {
        return Sanctum::actingAs(User::factory()->treasurer()->create());
    }

    private function actingAsAdmin(): User
    {
        return Sanctum::actingAs(User::factory()->admin()->create());
    }

    /** @return array{0: User, 1: MemberProfile} */
    private function actingAsMemberWithProfile(): array
    {
        $user = Sanctum::actingAs(User::factory()->create());
        $profile = MemberProfile::factory()->for($user)->create();

        return [$user, $profile];
    }

    private function dues(MemberProfile $member, int $month, int $year = 2026): MonthlyDues
    {
        return MonthlyDues::factory()->for($member)->create(['month' => $month, 'year' => $year]);
    }

    private function allocate(MonthlyDues $due, float|int $amount): void
    {
        $payment = Payment::factory()->create(['member_profile_id' => $due->member_profile_id]);
        $payment->monthlyDues()->attach($due->id, ['allocated_amount' => $amount]);
    }

    public function test_guests_cannot_list_monthly_dues(): void
    {
        $this->getJson('/api/monthly-dues')->assertUnauthorized();
    }

    public function test_treasurer_can_create_dues_with_the_default_amount(): void
    {
        $this->actingAsTreasurer();
        $member = MemberProfile::factory()->create();

        $this->postJson('/api/monthly-dues', [
            'member_profile_id' => $member->id,
            'month' => 11,
            'year' => 2026,
        ])
            ->assertCreated()
            ->assertJsonPath('data.required_amount', '30.00')
            ->assertJsonPath('data.status', 'unpaid')
            ->assertJsonPath('data.allocated_total', '0.00')
            ->assertJsonPath('data.member.full_name', $member->full_name);

        $this->assertDatabaseHas('monthly_dues', ['member_profile_id' => $member->id, 'month' => 11, 'year' => 2026]);
    }

    public function test_only_the_treasurer_can_create_dues(): void
    {
        $member = MemberProfile::factory()->create();
        $payload = ['member_profile_id' => $member->id, 'month' => 11, 'year' => 2026];

        $this->actingAsAdmin();
        $this->postJson('/api/monthly-dues', $payload)->assertForbidden();

        $this->actingAsMemberWithProfile();
        $this->postJson('/api/monthly-dues', $payload)->assertForbidden();

        $this->assertDatabaseCount('monthly_dues', 0);
    }

    public function test_store_validates_input(): void
    {
        $this->actingAsTreasurer();

        $this->postJson('/api/monthly-dues', ['month' => 13])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['member_profile_id', 'month', 'year']);

        $this->assertDatabaseCount('monthly_dues', 0);
    }

    public function test_the_same_member_month_and_year_cannot_be_created_twice(): void
    {
        $this->actingAsTreasurer();
        $member = MemberProfile::factory()->create();
        $this->dues($member, 11);

        $this->postJson('/api/monthly-dues', [
            'member_profile_id' => $member->id,
            'month' => 11,
            'year' => 2026,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['month']);

        $this->assertDatabaseCount('monthly_dues', 1);
    }

    public function test_an_unknown_member_is_rejected(): void
    {
        $this->actingAsTreasurer();

        $this->postJson('/api/monthly-dues', ['member_profile_id' => 9999, 'month' => 11, 'year' => 2026])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['member_profile_id']);
    }

    public function test_admin_and_treasurer_can_list_but_a_member_cannot(): void
    {
        $member = MemberProfile::factory()->create();
        $this->dues($member, 10);
        $this->dues($member, 11);

        $this->actingAsAdmin();
        $this->getJson('/api/monthly-dues')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data' => [['id', 'month', 'year', 'status']], 'links', 'meta']);

        $this->actingAsTreasurer();
        $this->getJson('/api/monthly-dues')->assertOk();

        $this->actingAsMemberWithProfile();
        $this->getJson('/api/monthly-dues')->assertForbidden();
    }

    public function test_status_filter_separates_paid_and_unpaid_months(): void
    {
        $this->actingAsTreasurer();
        $member = MemberProfile::factory()->create();

        $paid = $this->dues($member, 9);
        $partial = $this->dues($member, 10);
        $this->dues($member, 11);

        $this->allocate($paid, 30);
        $this->allocate($partial, 10);

        $this->getJson('/api/monthly-dues?status=paid')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $paid->id)
            ->assertJsonPath('data.0.status', 'paid');

        $this->getJson('/api/monthly-dues?status=unpaid')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_index_filters_by_month_and_year(): void
    {
        $this->actingAsTreasurer();
        $member = MemberProfile::factory()->create();
        $this->dues($member, 11, 2026);
        $this->dues($member, 12, 2026);
        $this->dues($member, 11, 2025);

        $this->getJson('/api/monthly-dues?month=11&year=2026')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/monthly-dues?year=2026')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_an_invalid_status_filter_is_rejected(): void
    {
        $this->actingAsTreasurer();

        $this->getJson('/api/monthly-dues?status=maybe')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_a_month_with_no_payments_is_unpaid_with_zero_allocated(): void
    {
        $this->actingAsTreasurer();
        $due = $this->dues(MemberProfile::factory()->create(), 11);

        $this->getJson("/api/monthly-dues/{$due->id}")
            ->assertOk()
            ->assertJsonPath('data.status', 'unpaid')
            ->assertJsonPath('data.allocated_total', '0.00')
            ->assertJsonCount(0, 'data.payments');
    }

    public function test_small_payments_add_up_to_a_paid_month(): void
    {
        $this->actingAsTreasurer();
        $due = $this->dues(MemberProfile::factory()->create(), 11);

        $this->allocate($due, 10);
        $this->allocate($due, 20);

        $this->getJson("/api/monthly-dues/{$due->id}")
            ->assertOk()
            ->assertJsonPath('data.status', 'paid')
            ->assertJsonPath('data.allocated_total', '30.00')
            ->assertJsonCount(2, 'data.payments');
    }

    public function test_member_can_view_their_own_dues_but_not_someone_elses(): void
    {
        [, $profile] = $this->actingAsMemberWithProfile();
        $mine = $this->dues($profile, 11);
        $theirs = $this->dues(MemberProfile::factory()->create(), 11);

        $this->getJson("/api/monthly-dues/{$mine->id}")->assertOk();
        $this->getJson("/api/monthly-dues/{$theirs->id}")->assertForbidden();
    }

    public function test_my_monthly_dues_returns_only_my_own(): void
    {
        [, $profile] = $this->actingAsMemberWithProfile();
        $this->dues($profile, 10);
        $this->dues($profile, 11);
        $this->dues(MemberProfile::factory()->create(), 11);

        $this->getJson('/api/my/monthly-dues')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_a_user_without_a_profile_has_no_dues(): void
    {
        $this->dues(MemberProfile::factory()->create(), 11);
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/my/monthly-dues')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_update_changes_only_the_required_amount(): void
    {
        $this->actingAsTreasurer();
        $due = $this->dues(MemberProfile::factory()->create(), 11);

        $this->putJson("/api/monthly-dues/{$due->id}", ['required_amount' => 45, 'month' => 5])
            ->assertOk()
            ->assertJsonPath('data.required_amount', '45.00');

        $this->assertDatabaseHas('monthly_dues', ['id' => $due->id, 'month' => 11]);
    }

    public function test_update_validates_the_amount(): void
    {
        $this->actingAsTreasurer();
        $due = $this->dues(MemberProfile::factory()->create(), 11);

        $this->putJson("/api/monthly-dues/{$due->id}", ['required_amount' => 0])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['required_amount']);
    }

    public function test_a_member_cannot_update_dues(): void
    {
        [, $profile] = $this->actingAsMemberWithProfile();
        $due = $this->dues($profile, 11);

        $this->putJson("/api/monthly-dues/{$due->id}", ['required_amount' => 1])->assertForbidden();

        $this->assertDatabaseHas('monthly_dues', ['id' => $due->id, 'required_amount' => 30]);
    }

    public function test_treasurer_can_delete_an_unpaid_month(): void
    {
        $this->actingAsTreasurer();
        $due = $this->dues(MemberProfile::factory()->create(), 11);

        $this->deleteJson("/api/monthly-dues/{$due->id}")->assertNoContent();

        $this->assertDatabaseMissing('monthly_dues', ['id' => $due->id]);
    }

    public function test_a_month_with_payments_cannot_be_deleted(): void
    {
        $this->actingAsTreasurer();
        $due = $this->dues(MemberProfile::factory()->create(), 11);
        $this->allocate($due, 30);

        $this->deleteJson("/api/monthly-dues/{$due->id}")
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['monthly_due']);

        $this->assertDatabaseHas('monthly_dues', ['id' => $due->id]);
    }

    public function test_admin_and_member_cannot_delete_dues(): void
    {
        $due = $this->dues(MemberProfile::factory()->create(), 11);

        $this->actingAsAdmin();
        $this->deleteJson("/api/monthly-dues/{$due->id}")->assertForbidden();

        $this->actingAsMemberWithProfile();
        $this->deleteJson("/api/monthly-dues/{$due->id}")->assertForbidden();

        $this->assertDatabaseHas('monthly_dues', ['id' => $due->id]);
    }

    public function test_a_missing_month_returns_404(): void
    {
        $this->actingAsTreasurer();

        $this->getJson('/api/monthly-dues/9999')->assertNotFound();
    }
}
