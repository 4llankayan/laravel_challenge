<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShoppingList\AddProductRequest;
use App\Http\Requests\ShoppingList\RemoveProductRequest;
use App\Http\Requests\ShoppingList\StoreRequest;
use App\Http\Resources\ShoppingList\IndexResource;
use App\Http\Resources\ShoppingList\ShowResource;
use App\Models\Product;
use App\Models\ShoppingList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShoppingListController extends Controller
{
    public function index() {
        $user = auth()->user();

        $shopping_lists = ShoppingList::with('products')
            ->where('user_id', $user->id)
            ->get();

        return IndexResource::collection($shopping_lists);
    }

    public function store(StoreRequest $request) {
        $validated = $request->validated();

        $user = auth()->user();

        try {
            $shopping_list = ShoppingList::create([
                'name' => $validated['name'],
                'user_id' => $user->id,
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Shopping List created successfully',
            'shopping_list' => $shopping_list,
        ]);
    }

    public function show($id) {
        $user = auth()->user();

        $shopping_list = ShoppingList::with('products')
            ->findOrFail($id);

        if ($shopping_list->user != $user) {
            return response()->json(['error' => 'You do not have permission to access this shopping list'], 403);
        }

        return app(ShowResource::class, ['resource' => $shopping_list]);
    }

    public function addProduct($id, AddProductRequest $request) {
        $validated = $request->validated();

        $shopping_list = ShoppingList::with('products')->findOrFail($id);
        $product = Product::findOrFail($validated['product_id']);

        $user = auth()->user();

        if ($shopping_list->user != $user) {
            return response()->json(['message' => 'You do not have permission to add products this shopping list'], 403);
        }

        if ($shopping_list->closed) {
            return response()->json(['message' => 'This shopping list is closed'], 400);
        }

        if ($shopping_list->products->contains($product)) {
            return response()->json(['message' => 'This product is already on the shopping list'], 400);
        }

        try {
            $shopping_list->products()->syncWithoutDetaching($product);
        } catch (Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Product successfully added to the shopping list',
        ]);
    }

    public function removeProduct($id, RemoveProductRequest $request): JsonResponse {
        $validated = $request->validated();

        $shoppingList = ShoppingList::with('products')->findOrFail($id);
        $product = Product::findOrFail($validated['product_id']);

        $user = auth()->user();

        if ($shoppingList->user != $user) {
            return response()->json(['message' => 'You do not have permission to remove products from this shopping list'], 403);
        }

        if ($shoppingList->closed) {
            return response()->json(['message' => 'This shopping list is closed'], 400);
        }

        if (!$shoppingList->products->contains($product)) {
            return response()->json(['message' => "This product isn't on the shopping list"], 400);
        }

        try {
            $shoppingList->products()->detach($product);
        } catch (Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Product successfully removed from shopping list',
        ]);
    }

    public function checkout($id): JsonResponse {
        $shopping_list = ShoppingList::with('products')->find($id);

        $user = auth()->user();

        if ($shopping_list->user != $user) {
            return response()->json(['error' => 'You do not have permission to close this shopping list'], 403);
        }

        if ($shopping_list->closed) {
            return response()->json(['error' => 'This shopping list is already closed'], 400);
        }

        try {
            $shopping_list->update([
                'closed' => true,
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Shopping List successfully closed'
        ]);
    }
}
