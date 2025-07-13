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
            ['key' => 'site_name', 'value' => 'Online Shop'],
            ['key' => 'description', 'value' => 'Trusted shopping place'],
            ['key' => 'about_us', 'value' => 'Kami adalah toko terpercaya sejak 2000'],
            ['key' => 'light_color', 'value' => '#0f172b'],
            ['key' => 'dark_color', 'value' => '#e2e8f0']
        ]);
    }
}
