<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DonationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_list_donations(): void
    {
        $this->getJson('/api/donations')->assertUnauthorized();
    }

    public function test_members_cannot_list_donations(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/donations')->assertForbidden();
    }

    public function test_admin_can_list_donations(): void
    {
        Donation::factory()->count(2)->create();

        Sanctum::actingAs(User::factory()->admin()->create());

        $this->getJson('/api/donations')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'amount',
                    'payment' => [
                        'id',
                        'payment_date',
                        'payment_method',
                        'member',
                    ],
                ]],
                'links',
                'meta',
            ]);
    }

    public function test_treasurer_can_list_donations(): void
    {
        Donation::factory()->create();

        Sanctum::actingAs(User::factory()->treasurer()->create());

        $this->getJson('/api/donations')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
