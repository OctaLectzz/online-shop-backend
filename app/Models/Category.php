<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class Category extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    // Slug
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    // Image
    public static function uploadImage(UploadedFile $image, string $name): string
    {
        $filename = time() . '-' . Str::slug($name) . '.' . $image->getClientOriginalExtension();
        $image->storeAs('categories', $filename, 'public');
        return $filename;
    }
    public function deleteImage(): void
    {
        if ($this->image && Storage::disk('public')->exists('categories/' . $this->image)) {
            Storage::disk('public')->delete('categories/' . $this->image);
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

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
