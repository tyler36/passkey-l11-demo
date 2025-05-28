<?php

namespace Tests\Unit\Models;

use App\Models\Passkey;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class UserModelTest.
 *
 * @internal
 */
class UserModelTest extends TestCase
{
    use DatabaseMigrations;
    
    public function test_it_has_many_passkeys(): void
    {
        $user = User::factory()->create();
        $passkey = Passkey::factory()->make();
        $user->passkeys()->save($passkey);

        $this->assertInstanceOf(Passkey::class, $user->passkeys->first());
    }
}
