<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class Review extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    // Activity
    protected static function booted()
    {
        static::created(function ($model) {
            $productName = optional($model->product)->name;
            $userName = Auth::check() ? Auth::user()->name : 'System';
            ActivityLogger::log('create', class_basename($model), $model->getKey(), "$userName rated product $productName");
        });

        static::updated(function ($model) {
            $productName = optional($model->product)->name;
            $userName = Auth::check() ? Auth::user()->name : 'System';
            ActivityLogger::log('update', class_basename($model), $model->getKey(), "$userName updated review for $productName");
        });

        static::deleted(function ($model) {
            $productName = optional($model->product)->name;
            $userName = Auth::check() ? Auth::user()->name : 'System';
            ActivityLogger::log('delete', class_basename($model), $model->getKey(), "$userName deleted review on $productName");
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
