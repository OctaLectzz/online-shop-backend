<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class Payment extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean'
    ];

    // Image
    public static function uploadImage(UploadedFile $image, string $name): string
    {
        $filename = time() . '-' . Str::slug($name) . '.' . $image->getClientOriginalExtension();
        $image->storeAs('payments', $filename, 'public');
        return $filename;
    }
    public function deleteImage(): void
    {
        if ($this->image && Storage::disk('public')->exists('payments/' . $this->image)) {
            Storage::disk('public')->delete('payments/' . $this->image);
        }
    }

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

    public function pays()
    {
        return $this->hasMany(Pay::class);
    }
}
