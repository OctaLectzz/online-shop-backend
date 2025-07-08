<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class Promo extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime'
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
