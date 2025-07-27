<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'avatar' => null,
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '081234567890',
            'status' => true
        ]);

        User::create([
            'avatar' => null,
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '082112345678',
            'status' => false
        ]);

        User::create([
            'avatar' => '1752616915-octalectzz.png',
            'name' => 'Octavyan Putra Ramadhan',
            'username' => 'octalectzz',
            'email' => 'octalectzz@gmail.com',
            'password' => Hash::make('password'),
            'phone_number' => '089690220404',
            'status' => true
        ]);
    }
}
