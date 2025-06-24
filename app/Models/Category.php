<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

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
}
