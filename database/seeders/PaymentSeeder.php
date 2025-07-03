<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Payment::create([
            'image'          => '1750764568-bca.png',
            'name'           => 'BCA',
            'type'           => 'bank',
            'account_number' => '1234567890',
            'account_name'   => 'PT Contoh BCA',
            'tutorial'       => '1. Masuk ke aplikasi BCA\n2. Pilih transfer\n3. Masukkan nomor rekening di atas',
            'status'         => true,
        ]);

        Payment::create([
            'image'          => '1750764568-ovo.png',
            'name'           => 'OVO',
            'type'           => 'ewallet',
            'account_number' => '081234567890',
            'account_name'   => 'Contoh OVO',
            'tutorial'       => '1. Buka OVO\n2. Masukkan nomor\n3. Kirim',
            'status'         => true,
        ]);

        Payment::create([
            'image'          => '1750764568-qris.png',
            'name'           => 'QRIS',
            'type'           => 'qris',
            'account_number' => 'IDQRIS123456',
            'account_name'   => 'Toko QRIS',
            'tutorial'       => 'Scan QR toko',
            'status'         => true,
        ]);

        Payment::create([
            'image'          => '1750764568-cash-on-delivery.png',
            'name'           => 'Cash On Delivery',
            'type'           => 'cash',
            'account_number' => null,
            'account_name'   => null,
            'tutorial'       => 'Bayar langsung saat barang sampai.',
            'status'         => true,
        ]);
    }
}
