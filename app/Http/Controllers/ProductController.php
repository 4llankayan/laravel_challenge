<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\Product\IndexResource;
use App\Http\Resources\Product\ShowResource;
use Throwable;

class ProductController extends Controller
{
    public function index() {
        $products = Product::all();

        return IndexResource::collection($products);
    }

    public function store(StoreRequest $request) {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $product = Product::create([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'quantity' => $validated['quantity'],
                'description' => $validated['description'],
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
            'message' => 'Product created successfully',
            'product' => $product,
        ]);
    }

    public function show($id) {
        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Product not found',
            ], 404);
        }

        return app(ShowResource::class, ['resource' => $product]);
    }

    public function update(UpdateRequest $request, $id) {
        $validated = $request->validated();

        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Product not found',
            ], 404);
        }

        DB::beginTransaction();

        try {
            $product->update([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'quantity' => $validated['quantity'],
                'description' => $validated['description'],
            ]);

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'failed',
                'message' => $th->getMessage(),
            ]);
        }

        return app(ShowResource::class, ['resource' => $product]);
    }

    public function destroy($id) {
        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Product not found',
            ], 404);
        }

        DB::beginTransaction();

        try {
            $product->delete();

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
            'message' => 'Product successfully deleted',
        ]);
    }
}
