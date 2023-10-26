<?php

namespace Tests\Feature\Product;

use App\Models\Product;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductTest extends TestCase
{
    private const ENDPOINT = '/api/products';

    /** @test */
    public function assertGetIndex(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this
                        ->withToken($token)
                        ->getJson(self::ENDPOINT);
        $response->assertStatus(200);
    }

    /** @test */
    public function assertUnauthorizedOnGetIndex(): void {
        $response = $this->getJson(self::ENDPOINT);
        $response->assertUnauthorized();
    }

    /** @test */
    public function assertStoreProduct(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $body = [
            'name' => fake()->name(),
            'price' => (int) fake()->randomFloat(min: 100, max: 2147483647),
            'quantity' => (int) fake()->randomFloat(min: 1, max: 100),
            'description' => fake()->text(),
        ];

        $response = $this
                        ->withToken($token)
                        ->postJson(self::ENDPOINT, $body);

        $response->assertStatus(200);
    }

    /** @test */
    public function assertValidationsOnStoreProduct(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $body = [
            'price' => fake()->name(),
            'quantity' => fake()->text(),
            'description' => fake()->text(),
        ];

        $response = $this
                        ->withToken($token)
                        ->postJson(self::ENDPOINT, $body);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'price',
                'quantity'
            ]
        ]);
    }

    /** @test */
    public function assertShowProduct(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $product = Product::factory()->create();

        $response = $this
                        ->withToken($token)
                        ->getJson(self::ENDPOINT . '/' . $product->id);

        $response
                ->assertStatus(200)
                ->assertJsonFragment([
                    'data' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $product->quantity,
                        'description' => $product->description,
                        'created_at' => $product->created_at,
                        'updated_at' => $product->updated_at,
                    ]
                ]
            );
    }

    /** @test */
    public function assertUpdateProduct(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $product = Product::factory()->create();

        $body = [
            'name' => fake()->name(),
            'price' => (int) fake()->randomFloat(min: 1, max: 2147483647),
            'quantity' => (int) fake()->randomFloat(min: 1, max: 100),
            'description' => fake()->text(),
        ];

        $response = $this
                        ->withToken($token)
                        ->putJson(self::ENDPOINT . '/' . $product->id, $body);

        $response->assertStatus(200);

        $productUpdated = Product::find($product->id);

        $this->assertEquals($body['name'], $productUpdated->name);
        $this->assertEquals($body['price'], $productUpdated->price);
        $this->assertEquals($body['quantity'], $productUpdated->quantity);
        $this->assertEquals($body['description'], $productUpdated->description);
    }

    /** @test */
    public function assertDestroyProduct(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $product = Product::factory()->create();

        $response = $this
                        ->withToken($token)
                        ->deleteJson(self::ENDPOINT . '/' . $product->id);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => "Product successfully deleted"
        ]);

    }
}
