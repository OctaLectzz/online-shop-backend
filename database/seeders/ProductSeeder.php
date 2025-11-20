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
        $tags = ['Baru', 'Diskon', 'Populer', 'iPhone', 'iphone murah', 'iphone 12 pro max'];
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
            'weight' => 30,
            'height' => 10,
            'width' => 15,
            'length' => 20,
            'status' => true,
            'use_variant' => true,
            'sold' => 124,
            'category_id' => 1,
            'created_by'  => 1,
        ]);

        // Product Images
        $images = [
            '1763628487_iphone-12-pro-max-1.jpg',
            '1763628506_iphone-12-pro-max-2.jpg',
            '1763629460_iphone-12-pro-max-3.jpg',
        ];

        foreach ($images as $img) {
            $product->images()->create(['image' => $img]);
        }

        // Product Variants
        $variants = [
            [
                'name'  => '128 GB',
                'price' => 11999000,
                'stock' => 20,
                'sold'  => 50,
                'image' => '1763636272_iphone-12-pro-max-128-gb.png',
            ],
            [
                'name'  => '256 GB',
                'price' => 13499000,
                'stock' => 15,
                'sold'  => 40,
                'image' => '1763636272_iphone-12-pro-max-256-gb.png',
            ],
            [
                'name'  => '512 GB',
                'price' => 15999000,
                'stock' => 10,
                'sold'  => 34,
                'image' => '1763636272_iphone-12-pro-max-512-gb.png',
            ],
        ];

        foreach ($variants as $variant) {
            $product->variants()->create($variant);
        }

        // Product Attributes
        $attributes = [
            [
                'name'  => 'Warna',
                'lists' => ['Silver', 'Graphite', 'Pacific Blue'],
            ],
        ];

        foreach ($attributes as $attr) {
            $product->attributes()->create($attr);
        }

        // Product Informations
        $informations = [
            [
                'name'        => 'Garansi',
                'description' => 'Garansi resmi Apple Indonesia selama 1 tahun',
            ],
            [
                'name'        => 'Isi Kotak',
                'description' => 'iPhone, kabel USB-C ke Lightning, dokumentasi',
            ],
            [
                'name'        => 'Tipe Layar',
                'description' => 'Super Retina XDR OLED, 120Hz, HDR10, Dolby Vision',
            ],
        ];

        foreach ($informations as $info) {
            $product->informations()->create($info);
        }

        // Attach Tags
        $product->tags()->sync($tagIds);
    }
}
