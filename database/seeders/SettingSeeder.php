<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::insert([
            ['key' => 'site_name', 'value' => 'Toko Hebat'],
            ['key' => 'description', 'value' => 'Tempat belanja terpercaya'],
            ['key' => 'about_us', 'value' => 'Kami adalah toko terpercaya sejak 2000'],
            ['key' => 'primary_color', 'value' => '#4F46E5'],
            ['key' => 'secondary_color', 'value' => '#10B981'],
        ]);
    }
}
