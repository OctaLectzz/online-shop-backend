<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class Product extends Model
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

    // Images
    public function uploadImages(array $images): void
    {
        foreach ($images as $index => $image) {
            $filename = $this->slug . '-' . ($index + 1) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('products', $filename, 'public');
            $this->images()->create(['image' => $filename]);
        }
    }
    public function deleteImages(): void
    {
        foreach ($this->images as $img) {
            if ($img->image && Storage::disk('public')->exists('products/' . $img->image)) {
                Storage::disk('public')->delete('products/' . $img->image);
            }

            $img->delete();
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}

class ProductImage extends Model
{
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
