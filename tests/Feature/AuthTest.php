<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
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
        $password = Str::random(10);

        $user = User::factory()->create([
            'password' => $password,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token'
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

    /**
     * Test that a user can be registered with valid data.
     */
    public function test_user_can_be_registered_with_valid_data()
    {
        $password = Str::random(10);

        $data = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'user',
            'token',
        ]);
    }

    /**
     * Test that a user cannot be registered with invalid data.
     */
    public function test_user_cannot_be_registered_with_invalid_data()
    {
        $data = [];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'name',
            'email',
            'password',
        ]);
    }
}
