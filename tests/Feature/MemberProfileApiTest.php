<?php

namespace Tests\Feature;

use App\Models\MemberProfile;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MemberProfileApiTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $user = User::factory()->admin()->create();
        Sanctum::actingAs($user);

        return $user;
    }

    private function actingAsTreasurer(): User
    {
        $user = User::factory()->treasurer()->create();
        Sanctum::actingAs($user);

        return $user;
    }

    /** @return array{0: User, 1: MemberProfile} */
    private function actingAsMemberWithProfile(): array
    {
        $user = User::factory()->create();
        $profile = MemberProfile::factory()->for($user)->create();
        Sanctum::actingAs($user);

        return [$user, $profile];
    }

    public function test_guest_cannot_list_members(): void
    {
        $this->getJson('/api/members')->assertUnauthorized();
    }

    public function test_admin_and_treasurer_can_list_members(): void
    {
        MemberProfile::factory()->count(2)->create();

        $this->actingAsAdmin();
        $this->getJson('/api/members')->assertOk()->assertJsonCount(2, 'data');

        $this->actingAsTreasurer();
        $this->getJson('/api/members')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_member_cannot_list_all_members(): void
    {
        $this->actingAsMemberWithProfile();

        $this->getJson('/api/members')->assertForbidden();
    }

    public function test_admin_can_create_a_member_profile(): void
    {
        $this->actingAsAdmin();
        $user = User::factory()->create();

        $this->postJson('/api/members', [
            'user_id' => $user->id,
            'full_name' => 'Test Member',
            'student_id' => 'TEST-001',
            'phone' => '0800000000',
        ])
            ->assertCreated()
            ->assertJsonPath('data.full_name', 'Test Member')
            ->assertJsonPath('data.student_id', 'TEST-001');

        $this->assertDatabaseHas('member_profiles', [
            'user_id' => $user->id,
            'student_id' => 'TEST-001',
        ]);
    }

    public function test_member_cannot_create_a_profile(): void
    {
        [$user] = $this->actingAsMemberWithProfile();

        $this->postJson('/api/members', [
            'user_id' => $user->id,
            'full_name' => 'Another Profile',
            'student_id' => 'TEST-002',
        ])->assertForbidden();
    }

    public function test_store_rejects_duplicate_user_profiles(): void
    {
        $this->actingAsAdmin();
        $user = User::factory()->create();
        MemberProfile::factory()->for($user)->create();

        $this->postJson('/api/members', [
            'user_id' => $user->id,
            'full_name' => 'Duplicate',
            'student_id' => 'TEST-003',
        ])->assertUnprocessable()->assertJsonValidationErrors(['user_id']);
    }

    public function test_store_rejects_users_with_non_member_roles(): void
    {
        $this->actingAsAdmin();
        $admin = User::factory()->admin()->create();

        $this->postJson('/api/members', [
            'user_id' => $admin->id,
            'full_name' => 'Invalid Role',
            'student_id' => 'TEST-004',
        ])->assertUnprocessable()->assertJsonValidationErrors(['user_id']);
    }

    public function test_member_can_view_their_own_profile_but_not_another_members(): void
    {
        [, $mine] = $this->actingAsMemberWithProfile();
        $theirs = MemberProfile::factory()->create();

        $this->getJson("/api/members/{$mine->id}")->assertOk();
        $this->getJson("/api/members/{$theirs->id}")->assertForbidden();
    }

    public function test_admin_can_update_and_delete_a_profile(): void
    {
        $this->actingAsAdmin();
        $member = MemberProfile::factory()->create();

        $this->patchJson("/api/members/{$member->id}", [
            'full_name' => 'Updated Name',
        ])->assertOk()->assertJsonPath('data.full_name', 'Updated Name');

        $this->deleteJson("/api/members/{$member->id}")->assertNoContent();

        $this->assertSoftDeleted('member_profiles', ['id' => $member->id]);
    }

    public function test_member_can_view_their_own_payment_history_only(): void
    {
        [, $mine] = $this->actingAsMemberWithProfile();
        $theirs = MemberProfile::factory()->create();

        Payment::factory()->count(2)->create(['member_profile_id' => $mine->id]);
        Payment::factory()->create(['member_profile_id' => $theirs->id]);

        $this->getJson("/api/members/{$mine->id}/payments")
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->getJson("/api/members/{$theirs->id}/payments")->assertForbidden();
    }

    public function test_admin_can_view_any_members_payment_history(): void
    {
        $this->actingAsAdmin();
        $member = MemberProfile::factory()->create();
        Payment::factory()->count(2)->create(['member_profile_id' => $member->id]);

        $this->getJson("/api/members/{$member->id}/payments")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
