<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'order_date' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
