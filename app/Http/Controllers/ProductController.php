<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\Product\IndexResource;
use App\Http\Resources\Product\ShowResource;
use Throwable;

class ProductController extends Controller
{
    public function index(): AnonymousResourceCollection {
        $products = Product::all();

        return IndexResource::collection($products);
    }

    public function store(StoreRequest $request): JsonResponse {
        $validated = $request->validated();

        try {
            $product = Product::create([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'quantity' => $validated['quantity'],
                'description' => $validated['description'] ?? null,
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
        ]);
    }

    public function show($id): ShowResource {
        $product = Product::findOrFail($id);

        return app(ShowResource::class, ['resource' => $product]);
    }

    public function update(UpdateRequest $request, $id) {
        $validated = $request->validated();

        $product = Product::findOrFail($id);

        try {
            $product->update([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'quantity' => $validated['quantity'],
                'description' => $validated['description'],
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }

        return app(ShowResource::class, ['resource' => $product]);
    }

    public function destroy($id): JsonResponse {
        $product = Product::findOrFail($id);

        try {
            $product->delete();
        } catch (Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Product successfully deleted',
        ]);
    }
}
