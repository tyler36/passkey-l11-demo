<?php

namespace Tests\Feature;

use App\Models\Passkey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_a_user_can_delete_their_passkey(): void
    {
        /** @var User */
        $user = User::factory()
            ->has(Passkey::factory())
            ->create();

        $this->assertCount(1, $user->passkeys);

        /** @var Passkey */
        $passkey = $user->passkeys()->first();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete("/passkeys/{$passkey->id}");

        $this->assertCount(0, $user->refresh()->passkeys);
    }

    public function test_a_user_can_not_delete_another_users_passkey(): void
    {
        /** @var User */
        $user = User::factory()
            ->create();

        $someone = User::factory()
            ->has(Passkey::factory())
            ->create();

        $this->assertCount(1, $someone->passkeys);

        /** @var Passkey */
        $passkey = $someone->passkeys()->first();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete("/passkeys/{$passkey->id}");

        $response->assertStatus(403);

        $this->assertCount(1, $someone->refresh()->passkeys);
    }
}
