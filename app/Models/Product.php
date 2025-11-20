<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;
use Illuminate\Http\UploadedFile;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

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
            if (! $image instanceof UploadedFile) {
                continue;
            }

            $filename = time() . '_' . $this->slug . '-' . ($index + 1) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('products', $filename, 'public');
            $this->images()->create(['image' => $filename]);
        }
    }
    public function deleteImages(array $keep = []): void
    {
        foreach ($this->images as $img) {
            if (!empty($keep) && in_array($img->image, $keep, true)) {
                continue;
            }

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
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }
    public function informations()
    {
        return $this->hasMany(ProductInformation::class);
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

class ProductVariant extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::deleting(function ($model) {
            $model->deleteImage();
        });
    }

    // Image
    public static function uploadImage(UploadedFile $image, string $productSlug, string $name): string
    {
        $filename = time() . '_' . $productSlug . '-' . Str::slug($name) . '.' . $image->getClientOriginalExtension();
        $image->storeAs('products/variants', $filename, 'public');

        return $filename;
    }
    public function deleteImage(): void
    {
        if ($this->image && Storage::disk('public')->exists('products/variants/' . $this->image)) {
            Storage::disk('public')->delete('products/variants/' . $this->image);
        }
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}

class ProductAttribute extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'lists' => 'array'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

class ProductInformation extends Model
{
    protected $table = 'product_informations';

    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
