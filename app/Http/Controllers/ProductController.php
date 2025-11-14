<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;

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
            $data['slug'] = Product::generateUniqueSlug($data['name']);
            $data['created_by'] = Auth::id();

            $product = DB::transaction(function () use ($data, $request) {
                $product = Product::create($data);

                // Images
                if ($request->hasFile('images')) {
                    $product->uploadImages($request->file('images'));
                }

                // Variants
                if ($request->filled('variants')) {
                    foreach ($request->input('variants') as $v) {
                        $variantData = [
                            'name'  => $v['name'],
                            'price' => $v['price'],
                            'stock' => $v['stock'],
                        ];

                        if (isset($v['image']) && $v['image'] instanceof \Illuminate\Http\UploadedFile) {
                            $variantData['image'] = ProductVariant::uploadImage($v['image'], $v['name']);
                        }

                        $product->variants()->create($variantData);
                    }
                }

                // Attributes
                if ($request->filled('attributes')) {
                    $attrs = collect($request->input('attributes'))
                        ->map(fn($a) => [
                            'name'  => $a['name'],
                            'lists' => array_values($a['lists']),
                        ])->all();

                    $product->attributes()->createMany($attrs);
                }

                // Informations
                if ($request->filled('informations')) {
                    $infos = collect($request->input('informations'))
                        ->map(fn($i) => [
                            'name'        => $i['name'],
                            'description' => $i['description'],
                        ])->all();

                    $product->informations()->createMany($infos);
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
            if ($product->name !== $data['name']) {
                $data['slug'] = Product::generateUniqueSlug($data['name'], $product->product_id);
            }

            DB::transaction(function () use ($product, $data, $request) {
                $product->update($data);

                // Images
                $keepUrls = $request->input('keep_images', []);
                $keepFilenames = collect($keepUrls)->filter()
                    ->map(function (string $url) {
                        $path = parse_url($url, PHP_URL_PATH);
                        return basename($path);
                    })
                    ->values()
                    ->all();
                $product->deleteImages($keepFilenames);
                if ($request->hasFile('images')) {
                    $product->uploadImages($request->file('images'));
                }

                // Variants
                if ($request->has('variants')) {
                    $payload = collect($request->input('variants'))->filter(fn($v) => empty($v['_delete']));

                    $keepIds = $payload->pluck('id')->filter()->values()->all();

                    $product->variants()->whereNotIn('id', $keepIds ?: [0])->delete();

                    foreach ($payload as $v) {
                        $variantData = [
                            'name'  => $v['name'],
                            'price' => $v['price'],
                            'stock' => $v['stock'],
                        ];

                        if (isset($v['image']) && $v['image'] instanceof \Illuminate\Http\UploadedFile) {
                            $variant = $product->variants()->find($v['id']);
                            if ($variant) {
                                $variant->deleteImage();
                            }
                            $variantData['image'] = ProductVariant::uploadImage($v['image'], $v['name']);
                        }

                        if (!empty($v['id'])) {
                            $product->variants()->whereKey($v['id'])->update($variantData);
                        } else {
                            $product->variants()->create($variantData);
                        }
                    }
                }

                // Attributes
                if ($request->has('attributes')) {
                    $payload = collect($request->input('attributes'))
                        ->filter(fn($a) => empty($a['_delete']))
                        ->map(function ($a) {
                            return [
                                'id'    => $a['id'] ?? null,
                                'name'  => $a['name'],
                                'lists' => array_values($a['lists'] ?? []),
                            ];
                        });

                    $keepIds = $payload->pluck('id')->filter()->values()->all();

                    $product->attributes()->whereNotIn('id', $keepIds ?: [0])->delete();

                    foreach ($payload as $a) {
                        if (!empty($a['id'])) {
                            $product->attributes()->whereKey($a['id'])->update([
                                'name'  => $a['name'],
                                'lists' => $a['lists'],
                            ]);
                        } else {
                            $product->attributes()->create([
                                'name'  => $a['name'],
                                'lists' => $a['lists'],
                            ]);
                        }
                    }
                }

                // Informations
                if ($request->has('informations')) {
                    $payload = collect($request->input('informations'))
                        ->filter(fn($i) => empty($i['_delete']))
                        ->map(fn($i) => [
                            'id'          => $i['id'] ?? null,
                            'name'        => $i['name'],
                            'description' => $i['description'],
                        ]);

                    $keepIds = $payload->pluck('id')->filter()->values()->all();

                    $product->informations()->whereNotIn('id', $keepIds ?: [0])->delete();

                    foreach ($payload as $i) {
                        if (!empty($i['id'])) {
                            $product->informations()->whereKey($i['id'])->update([
                                'name'        => $i['name'],
                                'description' => $i['description'],
                            ]);
                        } else {
                            $product->informations()->create([
                                'name'        => $i['name'],
                                'description' => $i['description'],
                            ]);
                        }
                    }
                }

                // Tags
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
                'message' => 'Product failed to update',
                'error' => $e->getMessage(),
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
                $product->variants()->each(function ($variant) {
                    $variant->deleteImage();
                    $variant->delete();
                });
                $product->variants()->delete();

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
