<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $product = Product::first();

        if (!$user || !$product) {
            $this->command->warn('CartSeeder skipped: No users or products found.');
            return;
        }

        Cart::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'quantity'   => 2
        ]);
    }
}
