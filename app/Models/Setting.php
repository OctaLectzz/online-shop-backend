<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getAllKeyValue(): array
    {
        return static::pluck('value', 'key')->toArray();
    }

    // Activity
    protected static function booted()
    {
        static::updated(function ($model) {
            ActivityLogger::log('update', class_basename($model), $model->getKey(), (Auth::check() ? Auth::user()->name : 'System') . ' Updated ' . class_basename($model));
        });
    }
}
