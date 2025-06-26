<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::latest()->get();

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();
            $data['slug'] = Product::generateUniqueSlug($data['name']);

            $product = DB::transaction(function () use ($data, $request) {
                $product = Product::create($data);

                // Images
                if ($request->hasFile('images')) {
                    $product->uploadImages($request->file('images'));
                }

                // Tags
                if ($request->tags) {
                    $tagIds = collect($request->tags)->map(function ($name) {
                        return Tag::firstOrCreate(['name' => $name])->id;
                    });
                    $product->tags()->sync($tagIds);
                }

                return $product;
            });

            return new ProductResource($product);
        } catch (\Throwable $e) {
            Log::error('Failed to store product', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Gagal menyimpan produk.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        try {
            $data = $request->validated();
            if ($product->name !== $data['name']) {
                $data['slug'] = Product::generateUniqueSlug($data['name'], $product->product_id);
            }

            DB::transaction(function () use ($product, $data, $request) {
                $product->update($data);

                if ($request->hasFile('images')) {
                    $product->deleteImages();
                    $product->uploadImages($request->file('images'));
                }

                if ($request->tags) {
                    $tagIds = collect($request->tags)->map(function ($name) {
                        return Tag::firstOrCreate(['name' => $name])->id;
                    });
                    $product->tags()->sync($tagIds);
                }
            });

            return new ProductResource($product);
        } catch (\Throwable $e) {
            Log::error('Failed to update product', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Gagal memperbarui produk.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->deleteImages();
        $product->tags()->detach();
        $product->delete();

        return response()->json(['message' => 'Product deleted.']);
    }
}
