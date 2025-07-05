<?php

namespace Database\Seeders;

use App\Models\Pay;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $order = Order::first();
        $payment = Payment::first();

        Pay::create([
            'order_id' => $order->id,
            'payment_id' => $payment->id,
            'transfer_date' => now(),
            'transfer_amount' => 150000,
            'transfer_proof' => '1751729865.jpg',
            'validation_status' => 'pending',
            'admin_notes' => null,
            'validated_by' => null
        ]);
    }
}
