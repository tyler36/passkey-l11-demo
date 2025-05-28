<?php

namespace Tests\Unit;

use App\Models\Passkey;
use App\Models\User;
use Tests\TestCase;

/**
 * PasskeyModelTest.
 *
 * @internal
 */
class PasskeyModelTest extends TestCase
{
    public function test_it_belongs_to_a_user(): void
    {
        $passkey = Passkey::factory()->create();

        $this->assertInstanceOf(User::class, $passkey->user);
    }
}
