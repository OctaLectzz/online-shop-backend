<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'order_date' => 'datetime'
    ];

    // Invoice
    public static function generateUniqueInvoice(): string
    {
        do {
            $invoice = 'INV' . now()->format('YmdHis') . strtoupper(Str::random(8));
        } while (Order::where('invoice', $invoice)->exists());

        return $invoice;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function pays()
    {
        return $this->hasMany(Pay::class);
    }
}

class OrderItem extends Model
{

    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
