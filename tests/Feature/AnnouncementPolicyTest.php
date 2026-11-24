<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AnnouncementPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_any_authenticated_user_can_list_announcements(): void
    {
        $member = User::factory()->create();

        $this->assertTrue(Gate::forUser($member)->allows('viewAny', Announcement::class));
    }

    public function test_only_admins_can_view_drafts_and_future_announcements(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();
        $draft = Announcement::factory()->draft()->create();
        $future = Announcement::factory()->create(['published_at' => now()->addDay()]);
        $published = Announcement::factory()->create();

        $this->assertFalse(Gate::forUser($member)->allows('view', $draft));
        $this->assertFalse(Gate::forUser($member)->allows('view', $future));
        $this->assertTrue(Gate::forUser($admin)->allows('view', $draft));
        $this->assertTrue(Gate::forUser($member)->allows('view', $published));
    }

    public function test_only_admins_can_create_update_and_delete_announcements(): void
    {
        $admin = User::factory()->admin()->create();
        $treasurer = User::factory()->treasurer()->create();
        $announcement = Announcement::factory()->create();

        foreach (['create', 'update', 'delete'] as $ability) {
            $arguments = $ability === 'create'
                ? Announcement::class
                : $announcement;

            $this->assertTrue(Gate::forUser($admin)->allows($ability, $arguments));
            $this->assertFalse(Gate::forUser($treasurer)->allows($ability, $arguments));
        }
    }
}
