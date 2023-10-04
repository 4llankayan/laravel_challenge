<?php

namespace Tests\Feature\User;

use App\Models\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class AuthTest extends TestCase
{
    private const ENDPOINT = '/api/auth';

    /** @test */
    public function assertSuccessRegister(): void {
        $body = [
            'name' => Str::random(12),
            'email' => strtolower(Str::random(116)) . '@example.com',
            'password' => Str::random(12),
        ];

        $response = $this->postJson(self::ENDPOINT . '/register', $body);
        $response->assertStatus(200);
    }

    /** @test */
    public function assertSuccessLogin(): void {
        $password = Str::random(12);

        $user = User::create([
            'name' => Str::random(12),
            'email' => strtolower(Str::random(116)) . '@example.com',
            'password' => $password,
        ]);

        $body =  [
            'email' => $user->email,
            'password' => $password,
        ];

        $response = $this->postJson(self::ENDPOINT . '/login', $body);
        $response
                ->assertStatus(200)
                ->assertJsonStructure([
                    'access_token',
                    'token_type',
                    'expires_in'
                ]);
    }

    /** @test */
    public function assertSuccessLogout(): void
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this
                        ->withToken($token)
                        ->postJson(self::ENDPOINT . '/logout');

        $response->assertStatus(200);
    }

    /** @test */
    public function assertUnauthorizedInLogout(): void {
        $response = $this->postJson(self::ENDPOINT . '/logout');

        $response->assertUnauthorized();
    }
}
