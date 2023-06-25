<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test that a user can be authenticated with valid credentials.
     */
    public function test_user_can_be_authenticated_with_valid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => $user->password,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'token' => $user->api_token,
        ]);
    }

    /**
     * Test that a user cannot be authenticated with invalid credentials.
     */
    public function test_user_cannot_be_authenticated_with_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid@email.com',
            'password' => 'invalid_password',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials.',
        ]);
    }
}
