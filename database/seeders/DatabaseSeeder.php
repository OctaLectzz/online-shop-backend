<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AddressSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            ProductSeeder::class,
            ReviewSeeder::class,
            PromoSeeder::class,
            CartSeeder::class,
            PaymentSeeder::class,
            OrderSeeder::class,
            PaySeeder::class,
            ShipmentSeeder::class,
            FaqSeeder::class,
            ContactSeeder::class,
            SettingSeeder::class,
            LogSeeder::class
        ]);
    }
}
