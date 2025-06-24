<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Address::create([
            'user_id' => 1,
            'recipient_name' => 'Octavyan Putra',
            'phone_number' => '081234567890',
            'province_id' => 31,
            'province_name' => 'DKI Jakarta',
            'city_id' => 3171,
            'city_name' => 'Jakarta Selatan',
            'district_id' => 3171040,
            'district_name' => 'Tebet',
            'village_id' => 3171040001,
            'village_name' => 'Tebet Barat',
            'postal_code' => '12810',
            'address' => 'Jl. Mawar No. 123, Tebet Barat',
            'label' => 'house',
            'notes' => 'Depan Indomaret',
            'is_default' => true
        ]);
    }
}
