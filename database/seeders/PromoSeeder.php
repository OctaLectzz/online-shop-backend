<?php

namespace Database\Seeders;

use App\Models\Promo;
use App\Models\User;
use Illuminate\Database\Seeder;

class PromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = User::pluck('id')->first();

        if (!$userId) {
            $this->command->warn('PromoSeeder skipped: No users found.');
            return;
        }

        Promo::create([
            'promo_code' => 'PROMO10',
            'name' => 'Diskon 10%',
            'description' => 'Dapatkan potongan 10% untuk semua produk.',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'max_discount_amount' => 50000,
            'quota' => 100,
            'usage_count' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addDays(30),
            'created_by' => $userId
        ]);

        Promo::create([
            'promo_code' => 'PROMO20',
            'name' => 'Diskon 20%',
            'description' => 'Dapatkan potongan 20% untuk semua produk.',
            'discount_type' => 'percent',
            'discount_value' => 20,
            'max_discount_amount' => 50000,
            'quota' => 200,
            'usage_count' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addDays(20),
            'created_by' => $userId
        ]);

        Promo::create([
            'promo_code' => 'PROMO15',
            'name' => 'Diskon 15%',
            'description' => 'Dapatkan potongan 15% untuk semua produk.',
            'discount_type' => 'percent',
            'discount_value' => 15,
            'max_discount_amount' => 50000,
            'quota' => 150,
            'usage_count' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addDays(5),
            'created_by' => $userId
        ]);
    }
}
