<?php

namespace Database\Seeders;

use App\Models\Log;
use Illuminate\Database\Seeder;

class LogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::create([
            'user_id'        => 1,
            'action'         => 'create',
            'description'    => 'Admin added a new product.',
            'reference_type' => 'product',
            'reference_id'   => 1
        ]);

        Log::create([
            'user_id'        => 1,
            'action'         => 'update',
            'description'    => 'Admin updated the product.',
            'reference_type' => 'product',
            'reference_id'   => 1
        ]);

        Log::create([
            'user_id'        => 1,
            'action'         => 'delete',
            'description'    => 'Admin deleted the product.',
            'reference_type' => 'product',
            'reference_id'   => 1
        ]);
    }
}
