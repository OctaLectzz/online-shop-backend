<?php

namespace App\Models;

use App\Helpers\ActivityLogger;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pay extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    // Transfer Proof
    public static function uploadImage(UploadedFile $transfer_proof): string
    {
        $filename = time() . '.' . $transfer_proof->getClientOriginalExtension();
        $transfer_proof->storeAs('pays', $filename, 'public');
        return $filename;
    }
    public function deleteImage(): void
    {
        if ($this->transfer_proof && Storage::disk('public')->exists('pays/' . $this->transfer_proof)) {
            Storage::disk('public')->delete('pays/' . $this->transfer_proof);
        }
    }

    // Status
    public function updateOrderStatusBasedOnPayment(Pay $pay, string $validationStatus): void
    {
        if (!$pay->order) {
            return;
        }

        $updates = [];

        switch ($validationStatus) {
            case 'accepted':
                $updates = [
                    'order_status' => 'processing',
                    'payment_status' => 'paid',
                    'paid_at' => now()
                ];
                break;

            case 'rejected':
                $updates = [
                    'order_status' => 'refunded',
                    'payment_status' => 'refunded',
                    'cancelled_at' => now()
                ];

                // Restore product stock for rejected payments
                $order = Order::findOrFail($pay->order_id);
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                        $product->decrement('sold', $item->quantity);

                        // Log stock restoration
                        ActivityLogger::log(
                            'inventory',
                            'product',
                            $product->id,
                            'Restored ' . $item->quantity . ' items due to payment rejection for order #' . $order->id
                        );
                    }
                }
                break;
        }

        if (!empty($updates)) {
            $pay->order->update($updates);

            // Trigger appropriate notifications
            $this->sendStatusNotification($pay->order, $validationStatus);
        }
    }

    // Log
    public function logPaymentActivity(Pay $pay, ?string $validationStatus, string $userName): void
    {
        $action = match ($validationStatus) {
            'accepted' => 'accepted payment from',
            'rejected' => 'rejected payment from',
            default => 'updated payment info for'
        };

        $details = Auth::user()->name . ' ' . $action . ' ' . $userName;

        if ($validationStatus) {
            $details .= ' (Status: ' . $validationStatus . ')';
        }

        ActivityLogger::log(
            'validation_status',
            'pay',
            $pay->id,
            $details
        );
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
