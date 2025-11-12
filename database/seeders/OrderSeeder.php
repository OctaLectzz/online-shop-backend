<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $product = Product::first();
        $productVariant = ProductVariant::first();

        if (!$user || !$product || !$productVariant) {
            return;
        }

        $order = Order::create([
            'invoice' => 'INV' . now()->format('YmdHis') . strtoupper(Str::random(8)),
            'user_id' => $user->id,
            'promo_id' => null,
            'total_price' => 2 * $productVariant->price,
            'discount_value' => 10000,
            'subtotal_price' => 2 * $productVariant->price - 10000,
            'note' => 'Contoh pesanan',
            'order_date' => now(),
            'order_status' => 'pending',
            'payment_status' => 'unpaid'
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $productVariant->id,
            'quantity' => 2,
            'unit_price' => $productVariant->price,
            'total_price' => 2 * $productVariant->price
        ]);
    }
}
