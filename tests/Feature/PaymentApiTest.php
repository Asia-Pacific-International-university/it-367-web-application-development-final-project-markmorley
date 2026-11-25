<?php

namespace Tests\Feature;

use App\Models\MemberProfile;
use App\Models\MonthlyDues;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentApiTest extends TestCase
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

    private function payload(MemberProfile $member, MonthlyDues $due, array $overrides = []): array
    {
        return array_merge([
            'member_profile_id' => $member->id,
            'amount' => 50,
            'payment_date' => '2026-11-25',
            'payment_method' => 'cash',
            'monthly_dues_ids' => [$due->id],
        ], $overrides);
    }

    private function novemberDues(MemberProfile $member): MonthlyDues
    {
        return MonthlyDues::factory()->for($member)->create(['month' => 11, 'year' => 2026]);
    }

    public function test_guests_cannot_list_payments(): void
    {
        $this->getJson('/api/payments')->assertUnauthorized();
    }

    public function test_treasurer_can_record_a_payment_and_it_is_split(): void
    {
        $treasurer = $this->actingAsTreasurer();
        $member = MemberProfile::factory()->create();
        $due = $this->novemberDues($member);

        $response = $this->postJson('/api/payments', $this->payload($member, $due))
            ->assertCreated()
            ->assertJsonPath('data.recorded_by.id', $treasurer->id)
            ->assertJsonPath('data.monthly_dues.0.id', $due->id);

        $this->assertEquals(30.0, (float) $response->json('data.monthly_dues.0.allocated_amount'));
        $this->assertEquals(20.0, (float) $response->json('data.donation.amount'));
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_member_cannot_record_a_payment(): void
    {
        [, $profile] = $this->actingAsMemberWithProfile();
        $due = $this->novemberDues($profile);

        $this->postJson('/api/payments', $this->payload($profile, $due))->assertForbidden();

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_admin_cannot_record_a_payment(): void
    {
        $this->actingAsAdmin();
        $member = MemberProfile::factory()->create();
        $due = $this->novemberDues($member);

        $this->postJson('/api/payments', $this->payload($member, $due))->assertForbidden();

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_store_validates_input(): void
    {
        $this->actingAsTreasurer();
        $member = MemberProfile::factory()->create();
        $due = $this->novemberDues($member);

        $this->postJson('/api/payments', $this->payload($member, $due, [
            'amount' => 0,
            'payment_date' => 'yesterday',
            'payment_method' => 'card',
            'monthly_dues_ids' => [],
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['amount', 'payment_date', 'payment_method', 'monthly_dues_ids']);

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_an_unknown_member_is_rejected(): void
    {
        $this->actingAsTreasurer();
        $member = MemberProfile::factory()->create();
        $due = $this->novemberDues($member);

        $this->postJson('/api/payments', $this->payload($member, $due, ['member_profile_id' => 9999]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['member_profile_id']);
    }

    public function test_the_recorder_cannot_be_set_from_the_request(): void
    {
        $treasurer = $this->actingAsTreasurer();
        $other = User::factory()->treasurer()->create();
        $member = MemberProfile::factory()->create();
        $due = $this->novemberDues($member);

        $this->postJson('/api/payments', $this->payload($member, $due, ['recorded_by_user_id' => $other->id]))
            ->assertCreated();

        $this->assertDatabaseHas('payments', ['recorded_by_user_id' => $treasurer->id]);
    }

    public function test_a_fully_paid_month_is_rejected_with_422(): void
    {
        $this->actingAsTreasurer();
        $member = MemberProfile::factory()->create();
        $due = $this->novemberDues($member);

        $this->postJson('/api/payments', $this->payload($member, $due, ['amount' => 30]))->assertCreated();
        $this->postJson('/api/payments', $this->payload($member, $due, ['amount' => 30]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['monthly_dues_ids']);

        $this->assertDatabaseCount('payments', 1);
    }

    public function test_admin_and_treasurer_can_list_payments(): void
    {
        Payment::factory()->count(3)->create();

        $this->actingAsAdmin();
        $this->getJson('/api/payments')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'amount', 'payment_date', 'payment_method']], 'links', 'meta']);

        $this->actingAsTreasurer();
        $this->getJson('/api/payments')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_member_cannot_list_all_payments(): void
    {
        $this->actingAsMemberWithProfile();

        $this->getJson('/api/payments')->assertForbidden();
    }

    public function test_index_filters_by_payment_method(): void
    {
        Payment::factory()->count(2)->create(['payment_method' => 'cash']);
        Payment::factory()->create(['payment_method' => 'bank_transfer']);

        $this->actingAsTreasurer();

        $this->getJson('/api/payments?payment_method=bank_transfer')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_an_invalid_sort_column_is_rejected(): void
    {
        $this->actingAsTreasurer();

        $this->getJson('/api/payments?sort=password')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['sort']);
    }

    public function test_member_can_view_their_own_payment_but_not_someone_elses(): void
    {
        [, $profile] = $this->actingAsMemberWithProfile();
        $mine = Payment::factory()->create(['member_profile_id' => $profile->id]);
        $theirs = Payment::factory()->create();

        $this->getJson("/api/payments/{$mine->id}")->assertOk();
        $this->getJson("/api/payments/{$theirs->id}")->assertForbidden();
    }

    public function test_my_payments_returns_only_my_own(): void
    {
        [, $profile] = $this->actingAsMemberWithProfile();
        Payment::factory()->count(2)->create(['member_profile_id' => $profile->id]);
        Payment::factory()->count(3)->create();

        $this->getJson('/api/my/payments')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_a_user_without_a_profile_has_no_payments(): void
    {
        Payment::factory()->count(2)->create();
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/my/payments')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_responses_do_not_include_the_treasurer_email(): void
    {
        $payment = Payment::factory()->create();
        $this->actingAsTreasurer();

        $this->getJson("/api/payments/{$payment->id}")
            ->assertOk()
            ->assertJsonMissingPath('data.recorded_by.email');
    }

    public function test_payments_cannot_be_edited_or_deleted(): void
    {
        $payment = Payment::factory()->create();
        $this->actingAsTreasurer();

        $this->putJson("/api/payments/{$payment->id}", ['amount' => 1])->assertStatus(405);
        $this->deleteJson("/api/payments/{$payment->id}")->assertStatus(405);

        $this->assertDatabaseHas('payments', ['id' => $payment->id]);
    }
}
