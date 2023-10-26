<?php

namespace Tests\Feature\ShoppingList;

use App\Models\Product;
use App\Models\ShoppingList;
use App\Models\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShoppingListTest extends TestCase
{
    private const ENDPOINT = '/api/shopping_lists';

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
    public function assertStoreShoppingList(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $body = [
            'name' => fake()->name(),
        ];

        $response = $this
                        ->withToken($token)
                        ->postJson(self::ENDPOINT, $body);

        $response->assertStatus(200);
    }

    /** @test */
    public function assertShowShoppingList():void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $shopping_list = ShoppingList::create([
            'name' => fake()->name(),
            'closed' => 0,
            'user_id' => $user->id,
        ]);

        $response = $this
                        ->withToken($token)
                        ->getJson(self::ENDPOINT . '/' . $shopping_list->id);

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    'id' => $shopping_list->id,
                    'name' => $shopping_list->name,
                    'closed' => $shopping_list->closed,
                    'products' => [],
                    'user_id' => $shopping_list->user->id,
                ]
            ]
        );
    }

    /** @test */
    public function assertAddProductInShoppingList(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $shopping_list = ShoppingList::create([
            'name' => fake()->name(),
            'closed' => 0,
            'user_id' => $user->id,
        ]);

        $product = Product::factory()->create();

        $body = [
            'product_id' => $product->id,
        ];

        $response = $this
                        ->withToken($token)
                        ->postJson(self::ENDPOINT . '/' . $shopping_list->id . '/products', $body);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Product successfully added to the shopping list'
        ]);
    }

    /** @test */
    public function assertRemoveProductInShoppingList(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $product = Product::factory()->create();

        $shopping_list = ShoppingList::create([
            'name' => fake()->name(),
            'closed' => 0,
            'user_id' => $user->id,
        ]);

        $shopping_list->products()->attach($product);

        $body = [
            'product_id' => $product->id,
        ];

        $response = $this
                        ->withToken($token)
                        ->deleteJson(self::ENDPOINT . '/' . $shopping_list->id . '/products', $body);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Product successfully removed from shopping list'
        ]);
    }

    /** @test */
    public function assertCheckoutShoppingList(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $shopping_list = ShoppingList::create([
            'name' => fake()->name(),
            'closed' => 0,
            'user_id' => $user->id,
        ]);

        $response = $this
                        ->withToken($token)
                        ->postJson(self::ENDPOINT . '/' . $shopping_list->id . '/checkout');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Shopping List successfully closed'
        ]);
    }

    /** @test */
    public function assertUnauthorizedOnGetIndex(): void {
        $response = $this->getJson(self::ENDPOINT);
        $response->assertUnauthorized();
    }

    /** @test */
    public function assertUnauthorizedOnGetShoppingListOfOtherUser(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $shopping_list = ShoppingList::factory()->create();

        $response = $this
            ->withToken($token)
            ->getJson(self::ENDPOINT . '/' . $shopping_list->id);

        $response
            ->assertForbidden()
            ->assertJson([
                'error' => 'You do not have permission to access this shopping list',
            ]);
    }

    /** @test */
    public function assertCantAddProductsToAnotherUserShoppingList(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $shopping_list = ShoppingList::factory()->create();

        $product = Product::factory()->create();

        $body = [
            'product_id' => $product->id,
        ];

        $response = $this
            ->withToken($token)
            ->postJson(self::ENDPOINT . '/' . $shopping_list->id . '/products', $body);

        $response
            ->assertForbidden()
            ->assertJson([
                'message' => 'You do not have permission to add products this shopping list',
            ]);
    }

    /** @test */
    public function assertCantRemoveProductsOfAnotherUserShoppingList(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $shopping_list = ShoppingList::factory()->create();

        $product = Product::factory()->create();

        $shopping_list->products()->attach($product);

        $body = [
            'product_id' => $product->id,
        ];

        $response = $this
            ->withToken($token)
            ->deleteJson(self::ENDPOINT . '/' . $shopping_list->id . '/products', $body);

        $response
            ->assertForbidden()
            ->assertJson([
                'message' => 'You do not have permission to remove products from this shopping list',
            ]);
    }

    /** @test */
    public function assertCantCheckoutAnotherUserShoppingList(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $shopping_list = ShoppingList::factory()->create();

        $response = $this
            ->withToken($token)
            ->postJson(self::ENDPOINT . '/' . $shopping_list->id . '/checkout');

        $response
            ->assertForbidden()
            ->assertJson([
                'error' => 'You do not have permission to close this shopping list',
            ]);
    }

    /** @test */
    public function assertCantAddProductInClosedShoppingList(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $shopping_list = ShoppingList::create([
            'name' => fake()->name(),
            'closed' => 1,
            'user_id' => $user->id,
        ]);

        $product = Product::factory()->create();

        $body = [
            'product_id' => $product->id,
        ];

        $response = $this
                        ->withToken($token)
                        ->postJson(self::ENDPOINT . '/' . $shopping_list->id . '/products', $body);

        $response
            ->assertBadRequest()
            ->assertJson([
                'message' => 'This shopping list is closed',
            ]);
    }

    /** @test */
    public function assertCantRemoveProductInClosedShoppingList(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $product = Product::factory()->create();

        $shopping_list = ShoppingList::create([
            'name' => fake()->name(),
            'closed' => 1,
            'user_id' => $user->id,
        ]);

        $shopping_list->products()->attach($product);

        $body = [
            'product_id' => $product->id,
        ];

        $response = $this
                        ->withToken($token)
                        ->deleteJson(self::ENDPOINT . '/' . $shopping_list->id . '/products', $body);

        $response
            ->assertBadRequest()
            ->assertJson([
                'message' => 'This shopping list is closed',
            ]);
    }

    /** @test */
    public function assertCantAddProductAlreadyAddedInShoppingList(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $product = Product::factory()->create();

        $shopping_list = ShoppingList::create([
            'name' => fake()->name(),
            'closed' => 0,
            'user_id' => $user->id,
        ]);

        $shopping_list->products()->attach($product);

        $body = [
            'product_id' => $product->id,
        ];

        $response = $this
                        ->withToken($token)
                        ->postJson(self::ENDPOINT . '/' . $shopping_list->id . '/products', $body);

        $response
            ->assertBadRequest()
            ->assertJson([
                'message' => 'This product is already on the shopping list',
            ]);
    }

    /** @test */
    public function assertCantRemoveProductThatIsntOnTheShoppingList(): void {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $product = Product::factory()->create();

        $shopping_list = ShoppingList::create([
            'name' => fake()->name(),
            'closed' => 0,
            'user_id' => $user->id,
        ]);

        $body = [
            'product_id' => $product->id,
        ];

        $response = $this
                        ->withToken($token)
                        ->deleteJson(self::ENDPOINT . '/' . $shopping_list->id . '/products', $body);

        $response
            ->assertBadRequest()
            ->assertJson([
                'message' => "This product isn't on the shopping list",
            ]);
    }
}
