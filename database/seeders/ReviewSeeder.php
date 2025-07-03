<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productIds = Product::pluck('id')->all();
        $userIds = User::pluck('id')->all();

        if (empty($productIds) || empty($userIds)) {
            $this->command->warn('ReviewSeeder skipped: No products or users found.');
            return;
        }

        $reviews = [
            [
                'product_id' => $productIds[0],
                'user_id'    => $userIds[0],
                'rating'     => 5,
                'comment'    => 'Produk sangat bagus dan sesuai deskripsi.',
            ],
            [
                'product_id' => $productIds[0],
                'user_id'    => $userIds[1] ?? $userIds[0],
                'rating'     => 4,
                'comment'    => 'Kualitas oke, pengiriman cepat.',
            ],
            [
                'product_id' => $productIds[1] ?? $productIds[0],
                'user_id'    => $userIds[0],
                'rating'     => 3,
                'comment'    => 'Lumayan, tapi bisa lebih baik.',
            ],
            [
                'product_id' => $productIds[2] ?? $productIds[0],
                'user_id'    => $userIds[2] ?? $userIds[0],
                'rating'     => 1,
                'comment'    => 'Sangat mengecewakan, produk rusak.',
            ],
        ];

        foreach ($reviews as $review) {
            Review::create(array_merge($review));
        }
    }
}
