<?php

namespace Database\Seeders;

use App\Models\Shipment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $order = Order::first();
        $admin = User::first();

        Shipment::create([
            'order_id' => $order->id,
            'shipping_date' => now(),
            'shipping_service' => 'Express',
            'courier_name' => 'JNE',
            'shipping_estimation' => '2-3 hari',
            'shipping_description' => 'Paket dikemas dengan aman',
            'tracking_number' => 'JNE1234567890',
            'processed_by' => $admin->id,
        ]);
    }
}
