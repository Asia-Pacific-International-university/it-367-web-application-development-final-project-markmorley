<?php

namespace Tests\Feature;

use Tests\TestCase;

class RootRouteTest extends TestCase
{
    public function test_the_root_url_returns_json_without_needing_built_assets(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertJsonPath('status', 'ok');
    }
}
