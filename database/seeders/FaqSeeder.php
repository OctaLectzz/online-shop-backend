<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Bagaimana cara memesan produk?',
                'answer' => 'Kamu bisa memesan produk melalui halaman katalog dan menambahkannya ke keranjang.'
            ],
            [
                'question' => 'Apa saja metode pembayaran yang tersedia?',
                'answer' => 'Kami menerima pembayaran melalui transfer bank, e-wallet (OVO, Dana), QRIS, dan tunai di tempat.'
            ],
            [
                'question' => 'Berapa lama pengiriman berlangsung?',
                'answer' => 'Pengiriman biasanya memakan waktu 2-5 hari kerja tergantung lokasi dan ekspedisi.'
            ],
            [
                'question' => 'Apakah saya bisa membatalkan pesanan?',
                'answer' => 'Pembatalan hanya bisa dilakukan sebelum pesanan diproses oleh admin.'
            ],
            [
                'question' => 'Bagaimana saya bisa melacak pesanan saya?',
                'answer' => 'Setelah dikirim, kamu akan menerima nomor resi untuk melacak melalui website ekspedisi.'
            ],
            [
                'question' => 'Apakah produk bisa ditukar?',
                'answer' => 'Produk dapat ditukar jika ada kesalahan pengiriman atau cacat produksi.'
            ],
            [
                'question' => 'Kapan customer service tersedia?',
                'answer' => 'Customer service tersedia setiap hari kerja pukul 09.00 - 17.00 WIB.'
            ],
            [
                'question' => 'Apakah harga sudah termasuk ongkir?',
                'answer' => 'Harga belum termasuk ongkir. Ongkir akan dihitung saat checkout berdasarkan alamat tujuan.'
            ],
            [
                'question' => 'Apakah produk ini original?',
                'answer' => 'Ya, semua produk kami 100% original dan bergaransi resmi.'
            ],
            [
                'question' => 'Bagaimana jika saya lupa password?',
                'answer' => 'Gunakan fitur "Lupa Password" di halaman login untuk mereset sandi akunmu.'
            ]
        ];

        foreach ($faqs as $faq) {
            Faq::create([
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'status' => true
            ]);
        }
    }
}
