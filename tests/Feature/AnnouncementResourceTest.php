<?php

namespace Tests\Feature;

use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_is_included_only_when_loaded_and_email_is_not_exposed(): void
    {
        $announcement = Announcement::factory()->create();

        $withoutAuthor = (new AnnouncementResource($announcement))->resolve();
        $withAuthor = (new AnnouncementResource($announcement->load('author')))->resolve();

        $this->assertArrayNotHasKey('author', $withoutAuthor);
        $this->assertSame([
            'id' => $announcement->author->id,
            'name' => $announcement->author->name,
        ], $withAuthor['author']);
        $this->assertArrayNotHasKey('email', $withAuthor['author']);
    }
}
