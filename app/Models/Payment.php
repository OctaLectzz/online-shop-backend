<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function pays()
    {
        return $this->hasMany(Pay::class);
    }
}
