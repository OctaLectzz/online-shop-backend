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
            'slug' => 'iphone-12-pro-max',
            'sku' => 'PRD001',
            'name' => 'iPhone 12 Pro Max',
            'description' => "Seperti saudaranya yang lain, desain iPhone 12 Pro Max mempunyai sentuhan nostalgia tapi juga modern. Hal itu karena bingkai handphone yang terinspirasi dari iPhone 4 yang memberikan sentuhan klasik ke handphone baru ini. Selain bingkai, iPhone 12 Pro juga mempunyai Ceramic Shield untuk layarnya, sehingga layarnya lebih kokoh dan tahan banting.

            Tapi tentu saja, masih ada perbedaan di antara iPhone 12 Pro Max dan iPhone 12 lainnya!

            Perbedaan pertama adalah ukuran dan kualitas layar iPhone 12 Pro Max. iPhone 12 Pro Max mempunyai layar OLED sebesar 6.7 inci. Ukurannya cukup besar, bahkan lebih besar daripada handphone lain seperti Samsung Galaxy Note 20 Ultra.

            Kualitas layar milik iPhone 12 Pro Max juga mendapat upgrade dari Apple. Layar handphone yang harus dipegang dengan dua tangan ini sangat cerah, warna yang jelas, dan kita bisa menonton video HDR dengan baik. Handphone ini juga dilengkapi dengan stereo yang akan menemani kamu saat menonton video atau mendengarkan musik.",
            'price' => 11999000,
            'stock' => 50,
            'weight' => 30,
            'height' => 10,
            'width' => 15,
            'length' => 20,
            'status' => true,
            'sold' => 124,
            'category_id' => 1,
            'created_by' => 1
        ]);

        // Image
        $images = ['iphone-12-pro-max-1.jpg', 'iphone-12-pro-max-2.jpg', 'iphone-12-pro-max-3.jpg'];
        foreach ($images as $img) {
            $product->images()->create([
                'image' => $img
            ]);
        }

        // Tags
        $product->tags()->sync($tagIds);
    }
}
