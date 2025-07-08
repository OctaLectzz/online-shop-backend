<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class Shipment extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'order_date' => 'datetime'
    ];

    // Activity
    protected static function booted()
    {
        static::created(function ($model) {
            ActivityLogger::log('create', class_basename($model), $model->getKey(), (Auth::check() ? Auth::user()->name : 'System') . ' Created ' . class_basename($model));
        });

        static::updated(function ($model) {
            ActivityLogger::log('update', class_basename($model), $model->getKey(), (Auth::check() ? Auth::user()->name : 'System') . ' Updated ' . class_basename($model));
        });

        static::deleted(function ($model) {
            ActivityLogger::log('delete', class_basename($model), $model->getKey(), (Auth::check() ? Auth::user()->name : 'System') . ' Deleted ' . class_basename($model));
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
