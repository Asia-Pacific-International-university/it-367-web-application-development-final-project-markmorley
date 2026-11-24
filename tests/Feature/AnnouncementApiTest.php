<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AnnouncementApiTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        return Sanctum::actingAs(User::factory()->admin()->create());
    }

    private function actingAsMember(): User
    {
        return Sanctum::actingAs(User::factory()->create());
    }

    public function test_guests_cannot_list_announcements(): void
    {
        $this->getJson('/api/announcements')->assertUnauthorized();
    }

    public function test_admin_can_create_an_announcement(): void
    {
        $admin = $this->actingAsAdmin();

        $this->postJson('/api/announcements', [
            'title' => 'Foundation Day',
            'message' => 'Planning meeting on Friday.',
            'published_at' => now()->toDateTimeString(),
        ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Foundation Day')
            ->assertJsonPath('data.author.id', $admin->id);

        $this->assertDatabaseHas('announcements', [
            'title' => 'Foundation Day',
            'author_user_id' => $admin->id,
        ]);
    }

    public function test_member_cannot_create_an_announcement(): void
    {
        $this->actingAsMember();

        $this->postJson('/api/announcements', [
            'title' => 'Sneaky post',
            'message' => 'Should not be saved.',
        ])->assertForbidden();

        $this->assertDatabaseMissing('announcements', ['title' => 'Sneaky post']);
    }

    public function test_store_validates_input(): void
    {
        $this->actingAsAdmin();

        $this->postJson('/api/announcements', ['title' => '', 'message' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'message']);
    }

    public function test_author_cannot_be_set_from_the_request(): void
    {
        $admin = $this->actingAsAdmin();
        $other = User::factory()->admin()->create();

        $this->postJson('/api/announcements', [
            'title' => 'Mine',
            'message' => 'Body',
            'author_user_id' => $other->id,
        ])->assertCreated();

        $this->assertDatabaseHas('announcements', [
            'title' => 'Mine',
            'author_user_id' => $admin->id,
        ]);
    }

    public function test_member_only_sees_published_announcements(): void
    {
        $this->actingAsMember();

        Announcement::factory()->count(2)->create();
        Announcement::factory()->draft()->create();
        Announcement::factory()->create(['published_at' => now()->addDay()]);

        $this->getJson('/api/announcements')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data' => [['id', 'title', 'message', 'published_at']], 'links', 'meta']);
    }

    public function test_admin_sees_drafts_too(): void
    {
        $this->actingAsAdmin();

        Announcement::factory()->count(2)->create();
        Announcement::factory()->draft()->create();

        $this->getJson('/api/announcements')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_member_can_view_a_published_announcement_but_not_a_draft(): void
    {
        $this->actingAsMember();

        $published = Announcement::factory()->create();
        $draft = Announcement::factory()->draft()->create();

        $this->getJson("/api/announcements/{$published->id}")->assertOk();
        $this->getJson("/api/announcements/{$draft->id}")->assertForbidden();
    }

    public function test_responses_do_not_include_the_author_email(): void
    {
        $this->actingAsMember();

        $announcement = Announcement::factory()->create();

        $this->getJson("/api/announcements/{$announcement->id}")
            ->assertOk()
            ->assertJsonMissingPath('data.author.email');
    }

    public function test_admin_can_update_an_announcement(): void
    {
        $this->actingAsAdmin();
        $announcement = Announcement::factory()->create();

        $this->putJson("/api/announcements/{$announcement->id}", ['title' => 'New title'])
            ->assertOk()
            ->assertJsonPath('data.title', 'New title');

        $this->assertDatabaseHas('announcements', ['id' => $announcement->id, 'title' => 'New title']);
    }

    public function test_member_cannot_update_an_announcement(): void
    {
        $this->actingAsMember();
        $announcement = Announcement::factory()->create(['title' => 'Original']);

        $this->putJson("/api/announcements/{$announcement->id}", ['title' => 'Hacked'])
            ->assertForbidden();

        $this->assertDatabaseHas('announcements', ['id' => $announcement->id, 'title' => 'Original']);
    }

    public function test_admin_can_delete_and_member_cannot(): void
    {
        $announcement = Announcement::factory()->create();

        $this->actingAsMember();
        $this->deleteJson("/api/announcements/{$announcement->id}")->assertForbidden();
        $this->assertDatabaseHas('announcements', ['id' => $announcement->id]);

        $this->actingAsAdmin();
        $this->deleteJson("/api/announcements/{$announcement->id}")->assertNoContent();
        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    public function test_a_missing_announcement_returns_404(): void
    {
        $this->actingAsAdmin();

        $this->getJson('/api/announcements/9999')->assertNotFound();
    }
}
