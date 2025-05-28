<?php

namespace Tests\Feature\API;

use App\Models\User;
use Tests\TestCase;

/**
 * Passkey Api Test.
 *
 * @internal
 */
class PasskeyApiTest extends TestCase
{
    public function test_can_get_passkey_registration_options(): void
    {
        $site = 'passkey-l11-demo';

        $this->actingAs(User::factory()->create())
            ->get(route('passkeys.register'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'rp' => ['name', 'id'],
                'user' => ['name', 'id', 'displayName'],
                'challenge',
                'authenticatorSelection'
            ])
            ->assertJsonFragment([
                'rp' => [
                    'id' => "{$site}.ddev.site",
                    'name' => $site,
                ],
            ]);
    }
}
