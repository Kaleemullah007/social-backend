<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic feature test example.
     */
    public function test__user_without_varification_cannot_login(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $token = $user->createToken('API Token')->plainTextToken;

        $userData = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $userData);

        $response->assertStatus(401);
    }

    public function test__user_can_login_successfull_varification(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('API Token')->plainTextToken;

        $userData = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $userData);

        $response->assertStatus(200);
    }

    public function test__user_registration(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email_verified_at' => null]);
        $userData = [
            'email' => 'email@fmial.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'name' => 'Hello',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(200);
    }


    public function test__user_registration_fail_when_email_is_duplicate(): void
    {
        Notification::fake();
         User::factory()->create(['email' => 'email@fmial.com']);
        $userData = [
            'email' => 'email@fmial.com ',
            'password' => 'password',
            'password_confirmation' => 'password',
            'name' => 'Hello',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422);
    }
}
