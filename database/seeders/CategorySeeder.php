<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'slug' => 'electronics',
            'name' => 'Electronics',
            'description' => 'All electronic devices and gadgets'
        ]);
        Category::create([
            'slug' => 'books',
            'name' => 'Books',
            'description' => 'Educational and fictional books'
        ]);
    }
}
