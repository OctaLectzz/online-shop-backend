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
            ['key' => 'light_color', 'value' => 'lara-light-blue'],
            ['key' => 'dark_color', 'value' => 'lara-dark-blue']
        ]);
    }
}
