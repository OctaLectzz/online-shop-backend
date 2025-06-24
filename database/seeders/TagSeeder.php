<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::create([
            'name' => 'Laptop'
        ]);
        Tag::create([
            'name' => 'Acer'
        ]);
        Tag::create([
            'name' => 'Laptop Murah'
        ]);
    }
}
