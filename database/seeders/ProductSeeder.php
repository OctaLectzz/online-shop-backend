<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tags
        $tags = ['Baru', 'Diskon', 'Populer'];

        // Create tag if not exists
        $tagIds = [];
        foreach ($tags as $name) {
            $tag = Tag::firstOrCreate(['name' => $name]);
            $tagIds[] = $tag->id;
        }

        // Product
        $product = Product::create([
            'slug' => 'produk-pertama',
            'sku' => 'PRD001',
            'name' => 'Produk Pertama',
            'description' => 'Deskripsi produk pertama',
            'price' => 100000,
            'stock' => 50,
            'weight' => 500,
            'height' => 10,
            'width' => 15,
            'length' => 20,
            'status' => true,
            'sold' => 124,
            'category_id' => 1,
            'created_by' => 1
        ]);

        // Image
        $images = ['produk-pertama-1.jpg', 'produk-pertama-2.jpg'];
        foreach ($images as $img) {
            $product->images()->create([
                'image' => $img
            ]);
        }

        // Tags
        $product->tags()->sync($tagIds);
    }
}
