<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Contact::create([
            'email' => 'info@example.com',
            'phone_number' => '+6281234567890',
            'address' => 'Jl. Mawar No.123, Jakarta',
            'maps' => 'https://maps.google.com/?q=-6.200000,106.816666',
            'whatsapp' => '+6281234567890',
            'facebook' => 'https://facebook.com/tokokami',
            'instagram' => 'https://instagram.com/tokokami',
            'tiktok' => 'https://tiktok.com/@tokokami',
            'twitter' => 'https://twitter.com/tokokami',
            'linkedin' => 'https://linkedin.com/company/tokokami'
        ]);
    }
}
