<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use Throwable;

class ProductController extends Controller
{
    public function index() {
        $products = Product::all();

        return response()->json([
            'status' => 'success',
            'products' => $products,
        ]);
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

        return response()->json([
            'status' => 'success',
            'product' => $product,
        ]);
    }

    public function update(UpdateRequest $request, $id) {
        $validated = $request->validated();
        $product = Product::find($id);

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

        return response()->json([
            'status' => 'success',
            'message' => 'Product successfully updated',
            'product' => $product,
        ]);
    }

    public function destroy($id) {
        $product = Product::find($id);

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
