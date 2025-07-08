<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed'
        ];
    }

    // Avatar
    public static function uploadAvatar(UploadedFile $avatar, string $name): string
    {
        $filename = time() . '-' . Str::slug($name) . '.' . $avatar->getClientOriginalExtension();
        $avatar->storeAs('avatars', $filename, 'public');
        return $filename;
    }
    public function deleteAvatar(): void
    {
        if ($this->avatar && Storage::disk('public')->exists('avatars/' . $this->avatar)) {
            Storage::disk('public')->delete('avatars/' . $this->avatar);
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

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'created_by');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function promos()
    {
        return $this->hasMany(Promo::class, 'created_by');
    }
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function pays()
    {
        return $this->hasMany(Pay::class, 'validated_by');
    }
    public function shipments()
    {
        return $this->hasMany(Pay::class, 'processed_by');
    }
    public function logs()
    {
        return $this->hasMany(Log::class);
    }
    public function logsRead()
    {
        return $this->belongsToMany(Log::class, 'log_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }
}
