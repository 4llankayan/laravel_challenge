<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShoppingList\AddProductRequest;
use App\Http\Requests\ShoppingList\RemoveProductRequest;
use App\Http\Requests\ShoppingList\StoreRequest;
use App\Http\Resources\ShoppingList\IndexResource;
use App\Http\Resources\ShoppingList\ShowResource;
use App\Models\Product;
use App\Models\ShoppingList;
use Illuminate\Support\Facades\DB;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class ShoppingListController extends Controller
{
    public function index() {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        $shopping_lists = ShoppingList::with('products')
            ->where('user_id', $user->id)
            ->get();

        return IndexResource::collection($shopping_lists);
    }

    public function store(StoreRequest $request) {
        $validated = $request->validated();

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        DB::beginTransaction();

        try {
            $shopping_list = ShoppingList::create([
                'name' => $validated['name'],
                'user_id' => $user->id,
            ]);

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'failed',
                'message' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Shopping List created successfully',
            'shopping_list' => $shopping_list,
        ]);
    }

    public function show($id) {
        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        $shopping_list = ShoppingList::with('products')
            ->find($id);

        if ($shopping_list->user != $user) {
            return response()->json(['error' => 'You do not have permission to access this shopping list'], 403);
        }

        if (is_null($shopping_list)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Shopping List not found',
            ], 404);
        }

        return app(ShowResource::class, ['resource' => $shopping_list]);
    }

    public function addProduct($id, AddProductRequest $request) {
        $validated = $request->validated();

        $shopping_list = ShoppingList::with('products')->find($id);
        $product = Product::find($validated['product_id']);

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        if ($shopping_list->user != $user) {
            return response()->json(['error' => 'You do not have permission to add products this shopping list'], 403);
        }

        if ($shopping_list->closed) {
            return response()->json(['error' => 'This shopping list is closed'], 400);
        }

        if ($shopping_list->products->contains($product)) {
            return response()->json(['error' => 'This product is already on the shopping list'], 400);
        }

        DB::beginTransaction();

        try {
            $shopping_list->products()->syncWithoutDetaching($product);

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'failed',
                'message' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Product successfully added to the shopping list',
        ]);
    }

    public function removeProduct($id, RemoveProductRequest $request) {
        $validated = $request->validated();

        $shopping_list = ShoppingList::with('products')->find($id);
        $product = Product::find($validated['product_id']);

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        if ($shopping_list->user != $user) {
            return response()->json(['error' => 'You do not have permission to remove products from this shopping list'], 403);
        }

        if ($shopping_list->closed) {
            return response()->json(['error' => 'This shopping list is closed'], 400);
        }

        if (!$shopping_list->products->contains($product)) {
            return response()->json(['error' => "This product isn't on the shopping list"], 400);
        }

        DB::beginTransaction();

        try {
            $shopping_list->products()->detach($product);

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'failed',
                'message' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Product successfully removed from shopping list',
        ]);
    }

    public function checkout($id) {
        $shopping_list = ShoppingList::with('products')->find($id);

        $token = JWTAuth::getToken();
        $user = JWTAuth::toUser($token);

        if ($shopping_list->user != $user) {
            return response()->json(['error' => 'You do not have permission to close this shopping list'], 403);
        }

        if ($shopping_list->closed) {
            return response()->json(['error' => 'This shopping list is already closed'], 400);
        }

        DB::beginTransaction();

        try {
            $shopping_list->update([
                'closed' => true,
            ]);

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'failed',
                'message' => $th->getMessage(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Shopping List successfully closed',
        ]);
    }
}
