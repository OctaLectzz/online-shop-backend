<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Pay extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    // Transfer Proof
    public static function uploadImage(UploadedFile $transfer_proof): string
    {
        $filename = time() . '.' . $transfer_proof->getClientOriginalExtension();
        $transfer_proof->storeAs('pays', $filename, 'public');
        return $filename;
    }
    public function deleteImage(): void
    {
        if ($this->transfer_proof && Storage::disk('public')->exists('pays/' . $this->transfer_proof)) {
            Storage::disk('public')->delete('pays/' . $this->transfer_proof);
        }
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
