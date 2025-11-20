<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Product;
use Illuminate\Support\Arr;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

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
     * @param \Illuminate\Http\Request $request
     */
    public function store(ProductRequest $request)
    {
        try {
            $data = $request->validated();
            $data['slug'] = $data['slug'] ?? Product::generateUniqueSlug($data['name']);
            $data['created_by'] = Auth::id();

            $product = DB::transaction(function () use ($data, $request) {
                // Create product
                $product = Product::create($data);

                // Images
                if ($request->hasFile('images')) {
                    $product->uploadImages($request->file('images'));
                }

                // Variants
                $variantFiles  = $request->file('variants', []);

                foreach ($data['variants'] as $index => $variant) {
                    $variantData = [
                        'name'  => $variant['name'],
                        'price' => $variant['price'],
                        'stock' => $variant['stock'],
                    ];

                    // handle image (Optional)
                    if (isset($variantFiles[$index]['image'])) {
                        $file = $variantFiles[$index]['image'];

                        $variantData['image'] = ProductVariant::uploadImage($file, $product->slug, $variant['name']);
                    }

                    $product->variants()->create($variantData);
                }

                // Attributes
                if (!empty($data['attributes'])) {
                    $attrs = collect($data['attributes'])
                        ->map(fn($attribute) => [
                            'name'  => $attribute['name'],
                            'lists' => array_values($attribute['lists']),
                        ])->toArray();

                    $product->attributes()->createMany($attrs);
                }

                // Informations
                if (!empty($data['informations'])) {
                    $informations = collect($data['informations'])
                        ->map(fn($information) => [
                            'name'        => $information['name'],
                            'description' => $information['description'],
                        ])->toArray();

                    $product->informations()->createMany($informations);
                }

                // Tags
                if (!empty($data['tags'])) {
                    $tagIds = collect($data['tags'])->map(function ($name) {
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
                'message' => 'Product failed to create',
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
     * @param \Illuminate\Http\Request $request
     */
    public function update(ProductRequest $request, Product $product)
    {
        try {
            $data = $request->validated();

            $product = DB::transaction(function () use ($data, $request, $product) {
                $productData = Arr::except($data, ['images', 'variants', 'attributes', 'informations', 'tags', 'keep_images']);
                $product->update($productData);

                // Images
                $rawKeep = $request->input('keep_images', []);
                $keep = collect(Arr::wrap($rawKeep))
                    ->map(function ($value) {
                        if (!is_string($value)) {
                            return null;
                        }

                        $path = parse_url($value, PHP_URL_PATH) ?: $value;
                        return basename($path);
                    })
                    ->filter()
                    ->values()
                    ->all();

                $product->deleteImages($keep);
                if ($request->hasFile('images')) {
                    $product->uploadImages($request->file('images'));
                }

                // Variants
                $variantInputs = $data['variants'] ?? [];
                $variantFiles  = $request->file('variants', []);

                foreach ($variantInputs as $index => $variantData) {
                    $variantId = $variantData['id'] ?? null;
                    $rawDelete = $variantData['_delete'] ?? false;
                    $toDelete  = filter_var($rawDelete, FILTER_VALIDATE_BOOLEAN);
                    $file      = $variantFiles[$index]['image'] ?? null;

                    if ($variantId) {
                        $variantModel = $product->variants()->whereKey($variantId)->first();
                        if (!$variantModel) {
                            continue;
                        }

                        if ($toDelete) {
                            $variantModel->delete();
                            continue;
                        }

                        $updateData = [
                            'name'  => $variantData['name'],
                            'price' => $variantData['price'] ?? 0,
                            'stock' => $variantData['stock'] ?? 0,
                        ];

                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                            $updateData['image'] = ProductVariant::uploadImage($file, $product->slug, $variantData['name']);
                        }

                        $variantModel->update($updateData);
                    } else {
                        if ($toDelete) {
                            continue;
                        }

                        $createData = [
                            'name'  => $variantData['name'],
                            'price' => $variantData['price'] ?? 0,
                            'stock' => $variantData['stock'] ?? 0,
                        ];

                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                            $createData['image'] = ProductVariant::uploadImage($file, $product->slug, $variantData['name']);
                        }

                        $product->variants()->create($createData);
                    }
                }

                // Attributes
                $attributeInputs = $data['attributes'] ?? [];

                foreach ($attributeInputs as $attributeData) {
                    $attributeId = $attributeData['id'] ?? null;
                    $rawDelete   = $attributeData['_delete'] ?? false;
                    $toDelete    = filter_var($rawDelete, FILTER_VALIDATE_BOOLEAN);

                    if ($attributeId) {
                        $attributeModel = $product->attributes()->whereKey($attributeId)->first();
                        if (!$attributeModel) {
                            continue;
                        }

                        if ($toDelete) {
                            $attributeModel->delete();
                            continue;
                        }

                        $attributeModel->update([
                            'name'  => $attributeData['name'],
                            'lists' => array_values($attributeData['lists'] ?? []),
                        ]);
                    } else {
                        if ($toDelete) {
                            continue;
                        }

                        $product->attributes()->create([
                            'name'  => $attributeData['name'],
                            'lists' => array_values($attributeData['lists'] ?? []),
                        ]);
                    }
                }

                // Informations
                $informationInputs = $data['informations'] ?? [];

                foreach ($informationInputs as $informationData) {
                    $informationId = $informationData['id'] ?? null;
                    $rawDelete     = $informationData['_delete'] ?? false;
                    $toDelete      = filter_var($rawDelete, FILTER_VALIDATE_BOOLEAN);

                    if ($informationId) {
                        $informationModel = $product->informations()->whereKey($informationId)->first();
                        if (!$informationModel) {
                            continue;
                        }

                        if ($toDelete) {
                            $informationModel->delete();
                            continue;
                        }

                        $informationModel->update([
                            'name'        => $informationData['name'],
                            'description' => $informationData['description'] ?? '',
                        ]);
                    } else {
                        if ($toDelete) {
                            continue;
                        }

                        $product->informations()->create([
                            'name'        => $informationData['name'],
                            'description' => $informationData['description'] ?? '',
                        ]);
                    }
                }

                // Tags
                $tagNames = $data['tags'] ?? null;

                if (is_array($tagNames) && !empty($tagNames)) {
                    $tagIds = collect($tagNames)
                        ->filter(fn($name) => filled($name))
                        ->map(fn($name) => Tag::firstOrCreate(['name' => $name])->id)
                        ->all();

                    $product->tags()->sync($tagIds);
                } else {
                    $product->tags()->sync([]);
                }

                return $product->fresh(['images', 'variants', 'attributes', 'informations', 'tags']);
            });

            return new ProductResource($product);
        } catch (\Throwable $e) {
            Log::error('Failed to update product', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Product failed to update',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::transaction(function () use ($product) {
                // Images
                $product->deleteImages();

                // Variants
                $product->variants->each(function (ProductVariant $variant) {
                    $variant->delete();
                });

                // Attributes
                $product->attributes()->delete();

                // Informations
                $product->informations()->delete();

                // Tags
                $product->tags()->detach();

                $product->delete();
            });

            return response()->json(['message' => 'Product deleted successfully.'], 200);
        } catch (\Throwable $e) {
            Log::error('Failed to delete product', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Product failed to delete',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
