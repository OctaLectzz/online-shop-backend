<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class Order extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'order_date' => 'datetime'
    ];

    public function getRouteKeyName()
    {
        return 'invoice';
    }

    // Invoice
    public static function generateUniqueInvoice(): string
    {
        do {
            $invoice = 'INV' . now()->format('YmdHis') . strtoupper(Str::random(8));
        } while (Order::where('invoice', $invoice)->exists());

        return $invoice;
    }

    // Activity
    protected static function booted()
    {
        static::created(function ($model) {
            ActivityLogger::log('order', class_basename($model), $model->getKey(), 'there is an incoming order');
        });

        static::updated(function ($model) {
            ActivityLogger::log('update', class_basename($model), $model->getKey(), (Auth::check() ? Auth::user()->name : 'System') . 'Updated ' . class_basename($model));
        });

        static::deleted(function ($model) {
            ActivityLogger::log('delete', class_basename($model), $model->getKey(), (Auth::check() ? Auth::user()->name : 'System') . 'Deleted ' . class_basename($model));
        });
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
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
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
